<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    private const SHIPPING_FEE = 25000;

    public function index()
    {
        $user = Auth::user();
        $addresses = ShippingAddress::where('user_id', $user->id)->get();
        $defaultAddress = $addresses->where('default', 1)->first();
        if (is_null($addresses) || is_null($defaultAddress)) {
            toastr()->error('Vui lòng thêm địa chỉ giao hàng!');
            return redirect()->route('account');
        }

        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();
        $activeCoupon = $this->resolveCouponFromSession($cartItems);
        $amounts = $this->calculateOrderAmounts($cartItems, $activeCoupon);

        $appliedCoupon = null;
        if ($activeCoupon) {
            $appliedCoupon = [
                'code' => $activeCoupon->code,
                'discount_percentage' => $activeCoupon->discount_percentage,
                'discount_amount' => $amounts['discount_amount'],
            ];
        }

        return view('clients.pages.checkout', [
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'cartItems' => $cartItems,
            'subtotal' => $amounts['subtotal'],
            'shippingFee' => $amounts['shipping_fee'],
            'discountAmount' => $amounts['discount_amount'],
            'totalPrice' => $amounts['total'],
            'appliedCoupon' => $appliedCoupon,
        ]);
    }

    public function getAddress(Request $request)
    {
        $address = ShippingAddress::where('id', $request->address_id)
            ->where('user_id', Auth::id())->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy địa chỉ!']);
        }

        return response()->json([
            'success' => true,
            'data' => $address,
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Giỏ hàng của bạn đang trống.',
            ], 422);
        }

        $code = strtoupper(trim($request->coupon_code));
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon || !$this->isCouponUsable($coupon)) {
            return response()->json([
                'status' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.',
            ], 422);
        }

        $amounts = $this->calculateOrderAmounts($cartItems, $coupon);

        if ($amounts['discount_amount'] <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Giỏ hàng không đủ điều kiện để áp dụng mã này.',
            ], 422);
        }

        session(['checkout_coupon' => ['code' => $coupon->code]]);

        return response()->json([
            'status' => true,
            'message' => 'Áp dụng mã giảm giá thành công.',
            'data' => [
                'coupon_code' => $coupon->code,
                'discount_percentage' => $coupon->discount_percentage,
                'discount_amount' => $amounts['discount_amount'],
                'subtotal' => $amounts['subtotal'],
                'shipping_fee' => $amounts['shipping_fee'],
                'total' => $amounts['total'],
            ],
        ]);
    }

    public function removeCoupon()
    {
        session()->forget('checkout_coupon');

        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();
        $amounts = $this->calculateOrderAmounts($cartItems, null);

        return response()->json([
            'status' => true,
            'message' => 'Đã gỡ mã giảm giá.',
            'data' => [
                'coupon_code' => null,
                'discount_percentage' => null,
                'discount_amount' => $amounts['discount_amount'],
                'subtotal' => $amounts['subtotal'],
                'shipping_fee' => $amounts['shipping_fee'],
                'total' => $amounts['total'],
            ],
        ]);
    }

    public function placeOrder(Request $request)
    {
        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Giỏ hàng trống!');
        }

        DB::beginTransaction();

        try {
            $coupon = $this->resolveCouponFromSession($cartItems);
            $amounts = $this->calculateOrderAmounts($cartItems, $coupon);

            $order = new Order();
            $order->user_id = $user->id;
            $order->shipping_address_id = $request->address_id;
            $order->subtotal = $amounts['subtotal'];
            $order->discount_amount = $amounts['discount_amount'];
            $order->shipping_fee = $amounts['shipping_fee'];
            $order->total_price = $amounts['total'];
            $order->status = 'pending';

            if ($coupon) {
                $order->coupon_id = $coupon->id;
                $order->coupon_code = $coupon->code;
            }

            $order->save();

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                $product = $item->product;
                if ($product->stock < $item->quantity) {
                    throw new \Exception("Sản phẩm {$product->name} không đủ hàng trong kho.");
                }
                $product->stock -= $item->quantity;
                $product->save();
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'amount' => $order->total_price,
                'status' => 'pending',
                'paid_at' => null,
            ]);

            CartItem::where('user_id', $user->id)->delete();

            if ($coupon) {
                $coupon->increment('times_used');
                session()->forget('checkout_coupon');
            }

            DB::commit();

            Notification::create([
                'user_id' => $user->id,
                'type' => 'order',
                'message' => "Có đơn đặt hàng mới từ " . $user->email,
                'link' => '/orders',
                'is_read' => 0,
            ]);

            toastr()->success('Đặt hàng thành công!');
            return redirect()->route('account');
        } catch (\Exception $e) {
            Log::error('Lỗi đặt hàng: ' . $e->getMessage());
            DB::rollBack();
            toastr()->error('Có lỗi xảy ra, vui lòng thử lại! ' . $e->getMessage());
            return redirect()->route('checkout');
        }
    }

    public function placeOrderPayPal(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Giỏ hàng trống.'], 422);
            }

            $coupon = $this->resolveCouponFromSession($cartItems);
            $amounts = $this->calculateOrderAmounts($cartItems, $coupon);

            $order = new Order();
            $order->user_id = $user->id;
            $order->shipping_address_id = $request->address_id;
            $order->subtotal = $amounts['subtotal'];
            $order->discount_amount = $amounts['discount_amount'];
            $order->shipping_fee = $amounts['shipping_fee'];
            $order->total_price = $amounts['total'];
            $order->status = 'pending';

            if ($coupon) {
                $order->coupon_id = $coupon->id;
                $order->coupon_code = $coupon->code;
            }

            $order->save();

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                $product = $item->product;
                if ($product->stock < $item->quantity) {
                    throw new \Exception("Sản phẩm {$product->name} không đủ hàng trong kho.");
                }
                $product->stock -= $item->quantity;
                $product->save();
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'paypal',
                'transaction_id' => $request->transactionID,
                'amount' => $order->total_price,
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            CartItem::where('user_id', $user->id)->delete();

            if ($coupon) {
                $coupon->increment('times_used');
                session()->forget('checkout_coupon');
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Lỗi đặt hàng PayPal: ' . $e->getMessage());
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại!'], 500);
        }
    }

    private function resolveCouponFromSession($cartItems): ?Coupon
    {
        $sessionCoupon = session('checkout_coupon');

        if (!$sessionCoupon || empty($sessionCoupon['code'])) {
            return null;
        }

        $coupon = Coupon::where('code', Str::upper($sessionCoupon['code']))->first();

        if ($coupon && $this->isCouponUsable($coupon) && $cartItems->isNotEmpty()) {
            return $coupon;
        }

        session()->forget('checkout_coupon');

        return null;
    }

    private function isCouponUsable(Coupon $coupon): bool
    {
        if (!$coupon->is_active) {
            return false;
        }

        if ($coupon->isExpired()) {
            return false;
        }

        if ($coupon->hasReachedUsageLimit()) {
            return false;
        }

        return true;
    }

    private function calculateOrderAmounts($cartItems, ?Coupon $coupon = null): array
    {
        $subtotal = (float) $cartItems->sum(fn($item) => $item->quantity * $item->product->price);
        $shippingFee = $subtotal > 0 ? (float) self::SHIPPING_FEE : 0.0;
        $discountAmount = 0.0;

        if ($coupon && $subtotal > 0) {
            $discountAmount = round($subtotal * $coupon->discount_percentage / 100, 2);
            $discountAmount = min($discountAmount, $subtotal);
        }

        $total = max($subtotal + $shippingFee - $discountAmount, 0);

        return [
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'discount_amount' => $discountAmount,
            'total' => $total,
        ];
    }
}