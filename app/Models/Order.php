<?php

namespace App\Models;

use App\Commerce\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference', 'user_id', 'address_id', 'status', 'payment_status',
        'payment_method', 'subtotal_cents', 'delivery_fee_cents', 'total_cents',
        'currency', 'shipping_address', 'customer_notes', 'placed_at', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal_cents' => 'integer',
            'delivery_fee_cents' => 'integer',
            'total_cents' => 'integer',
            'shipping_address' => 'encrypted:array',
            'placed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'status' => OrderStatus::class,
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}
