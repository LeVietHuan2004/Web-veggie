<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_READY_FOR_DELIVERY = 'ready_for_delivery';
    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELED = 'canceled';

    protected $fillable = [
        'user_id',
        'subtotal',
        'discount_amount',
        'shipping_fee',
        'total_price',
        'status',
        'shipping_address_id',
        'coupon_id',
        'coupon_code',
        'delivery_staff_id',
        'dispatched_at',
        'delivered_at',
    ];

    protected $casts = [
        'dispatched_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'Chờ xác nhận',
            self::STATUS_PROCESSING => 'Đang xử lý',
            self::STATUS_READY_FOR_DELIVERY => 'Chờ giao',
            self::STATUS_OUT_FOR_DELIVERY => 'Đang giao',
            self::STATUS_DELIVERED => 'Đã giao',
            self::STATUS_COMPLETED => 'Hoàn thành',
            self::STATUS_CANCELED => 'Đã hủy',
        ];
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function orderStatusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function deliveryStaff()
    {
        return $this->belongsTo(User::class, 'delivery_staff_id');
    }

    public function recordStatus(string $status, ?string $note = null): void
    {
        $this->orderStatusHistory()->create([
            'status' => $status,
            'changed_at' => now(),
            'note' => $note,
        ]);
    }
}

