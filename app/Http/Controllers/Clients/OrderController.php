<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function showOrder($id)
    {
        $order = Order::with(['orderItems.product', 'user', 'shippingAddress', 'payment', 'deliveryStaff'])->findOrFail($id);

        $userId = auth()->id();

        return view('clients.pages.order-detail', compact('order'));
    }

    public function cancel($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', Order::STATUS_PENDING)
            ->firstOrFail();

        foreach ($order->orderItems as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        $order->status = Order::STATUS_CANCELED;
        $order->save();
        $order->recordStatus(Order::STATUS_CANCELED, 'Order canceled by customer');

        return redirect()->back()->with('success', 'Đơn hàng đã được hủy thành công và sản phẩm đã được hoàn kho.');
    }

    public function received($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', Order::STATUS_DELIVERED)
            ->firstOrFail();

        if ($payment = $order->payment) {
            if ($payment->status !== 'completed') {
                $payment->status = 'completed';
                $payment->paid_at = $payment->paid_at ?? now(); 
                $payment->save();
            }
        }

        $order->status = Order::STATUS_COMPLETED;
        $order->save();
        $order->recordStatus(Order::STATUS_COMPLETED, 'Order completed by customer confirmation');

        return redirect()
            ->back()
            ->with('success', 'Xác nhận thành công. Bạn có thể đánh giá đơn hàng này!');
    }

}
