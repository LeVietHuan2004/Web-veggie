<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderItems', 'shippingAddress', 'user', 'payment', 'deliveryStaff')
            ->orderByDesc('id')
            ->get();

        $deliveryStaffs = User::whereHas('role', function ($query) {
            $query->where('name', 'delivery_staff');
        })->where('status', 'active')->get();

        return view('admin.pages.orders', compact('orders', 'deliveryStaffs'));
    }

    public function confrimOrder(Request $request)
    {
        $order = Order::with('orderItems')->find($request->id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Đơn hàng không tồn tại!',
            ]);
        }

        if ($order->status !== Order::STATUS_PENDING) {
            return response()->json([
                'status' => false,
                'message' => 'Chỉ có thể xác nhận đơn hàng đang chờ duyệt.',
            ]);
        }

        $order->status = Order::STATUS_PROCESSING;
        $order->save();
        $order->recordStatus(Order::STATUS_PROCESSING, 'Order confirmed by ' . Auth::guard('admin')->user()->name);

        return response()->json([
            'status' => true,
            'message' => 'Xác nhận đơn hàng thành công!',
        ]);
    }

    public function markReadyForDelivery(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'integer'],
            'delivery_staff_id' => ['required', 'integer', 'exists:users,id'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $order = Order::with('orderItems')->find($data['order_id']);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Đơn hàng không tồn tại!',
            ]);
        }

        if (!in_array($order->status, [Order::STATUS_PROCESSING, Order::STATUS_READY_FOR_DELIVERY], true)) {
            return response()->json([
                'status' => false,
                'message' => 'Chỉ có thể gán giao cho đơn đang xử lý hoặc chờ giao.',
            ]);
        }

        $deliveryStaff = User::where('id', $data['delivery_staff_id'])
            ->whereHas('role', function ($query) {
                $query->where('name', 'delivery_staff');
            })
            ->first();

        if (!$deliveryStaff) {
            return response()->json([
                'status' => false,
                'message' => 'Nhân viên giao hàng không hợp lệ.',
            ]);
        }

        $order->delivery_staff_id = $deliveryStaff->id;
        $order->status = Order::STATUS_READY_FOR_DELIVERY;
        $order->dispatched_at = null;
        $order->delivered_at = null;
        $order->save();

        $note = $data['note'] ?? null;
        $order->recordStatus(Order::STATUS_READY_FOR_DELIVERY, $note);

        return response()->json([
            'status' => true,
            'message' => 'Đơn hàng đã được chuyển sang trạng thái chờ giao.',
        ]);
    }

    public function showOrderDetail($id)
    {
        $order = Order::with('orderItems.product', 'shippingAddress', 'user', 'payment', 'deliveryStaff')->find($id);

        return view('admin.pages.order-detail', compact('order'));
    }

    public function sendMailInvoice(Request $request)
    {
        // Existing logic kept intact.
        $id = $request->id;
        $order = Order::with('orderItems.product', 'shippingAddress', 'user', 'payment')->find($id);

        try {
            \Mail::send('admin.emails.invoice', compact('order'), function ($message) use ($order) {
                $message->to($order->user->email)
                    ->subject('Hóa đơn đặt tour của khách hàng ' . $order->shippingAddress->full_name);
            });

            return response()->json([
                'status' => true,
                'message' => 'Hóa đơn đã được gửi qua email!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể gửi hóa đơn qua email. Vui lòng thử lại sau. ' . $th->getMessage(),
            ]);
        }
    }

    public function cancelOrder(Request $request)
    {
        $order = Order::with('orderItems.product')->find($request->id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Đơn hàng không tồn tại!',
            ]);
        }

        if (in_array($order->status, [Order::STATUS_DELIVERED, Order::STATUS_COMPLETED], true)) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể hủy đơn đã giao hoặc hoàn thành.',
            ]);
        }

        foreach ($order->orderItems as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        $order->status = Order::STATUS_CANCELED;
        $order->delivery_staff_id = null;
        $order->dispatched_at = null;
        $order->delivered_at = null;
        $order->save();
        $order->recordStatus(Order::STATUS_CANCELED, 'Order canceled by ' . Auth::guard('admin')->user()->name);

        return response()->json([
            'status' => true,
            'message' => 'Đơn hàng đã được hủy thành công!',
        ]);
    }
}

