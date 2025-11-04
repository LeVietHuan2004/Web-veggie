@extends('layouts.client')

@php
    use App\Models\Order;
@endphp

@section('title', 'Chi tiết đơn hàng')

@section('breadcrumb', 'Chi tiết đơn hàng')

@section('content')
    <div class="liton__shoping-cart-area mb-120">
        <div class="container mt-4">
            <h3>Chi tiết đơn hàng #{{ $order->id }}</h3>
            <p>Ngày đặt: {{ $order->created_at->format('d/m/Y') }}</p>
            <p>Trạng thái:
                @php
                    $statusLabels = Order::statusLabels();
                    $badgeClasses = [
                        Order::STATUS_PENDING => 'badge bg-warning',
                        Order::STATUS_PROCESSING => 'badge bg-primary',
                        Order::STATUS_READY_FOR_DELIVERY => 'badge bg-info',
                        Order::STATUS_OUT_FOR_DELIVERY => 'badge bg-info',
                        Order::STATUS_DELIVERED => 'badge bg-success',
                        Order::STATUS_COMPLETED => 'badge bg-success',
                        Order::STATUS_CANCELED => 'badge bg-danger',
                    ];
                    $badgeClass = $badgeClasses[$order->status] ?? 'badge bg-secondary';
                @endphp
                <span class="{{ $badgeClass }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
            </p>
            <p>Phương thức thanh toán:
                @if ($order->payment && $order->payment->payment_method == 'cash')
                    <span class="badge bg-primary">Thanh toán khi nhận hàng</span>
                @elseif($order->payment && $order->payment->payment_method == 'paypal')
                    <span class="badge bg-primary">Thanh toán bằng PayPal</span>
                @else
                    <span class="badge bg-danger">Chưa xác định</span>
                @endif
            </p>
            <p>Tổng tiền:
                {{ number_format($order->total_price, 0, ',', '.') }} VNĐ
            </p>

            <h4 class="mt-4">Sản phẩm trong đơn hàng</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderItems as $item)
                        <tr>
                            <td>
                                <img src="{{  $item->product->image_url }}" width="50">
                            </td>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ number_format($item->price, 0, ',', '.') }} đ
                            <td>{{ $item->quantity }}</td>
                            </td>
                            <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h4 class="mt-4">Thông tin giao hàng</h4>
            <p>Người nhận: {{ $order->shippingAddress->full_name }}</p>
            <p>Địa chỉ: {{ $order->shippingAddress->address }}</p>
            <p>Thành phố: {{ $order->shippingAddress->city }}</p>
            <p>Số điện thoại: {{ $order->shippingAddress->phone }}</p>

            @if ($order->deliveryStaff)
                <h4 class="mt-4">Thông tin nhân viên giao hàng</h4>
                <p>Họ tên: {{ $order->deliveryStaff->name }}</p>
                <p>Số điện thoại: {{ $order->deliveryStaff->phone_number }}</p>
            @endif

            @if ($order->status === Order::STATUS_PENDING)
                <form action="{{ route('order.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm mt-3">Hủy đơn hàng</button>
                </form>
            @endif

            @if ($order->status === Order::STATUS_DELIVERED)
                <form action="{{ route('order.received', $order->id) }}" method="POST" onsubmit="return confirm('Đã nhận được đơn hàng?');">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm mt-3">Đã nhận được hàng</button>
                </form>
            @endif

            @if ($order->status === Order::STATUS_COMPLETED)
            <h4 class="mt-4">Đánh giá sản phẩm</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đánh giá</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderItems as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td> 
                                <a href="{{ route('product.detail', $item->product->slug) }}" class="btn theme-btn-1 btn-effect-1">Đánh giá</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        </div>
    </div>
@endsection
