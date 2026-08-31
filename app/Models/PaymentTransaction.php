<?php

namespace App\Models;

use App\Payments\Enums\PaymentMethod;
use App\Payments\Enums\PaymentStatus;
use Database\Factories\PaymentTransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    /** @use HasFactory<PaymentTransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'reference',
        'user_id',
        'order_id',
        'order_reference',
        'method',
        'status',
        'amount_cents',
        'currency',
        'gateway_reference',
        'gateway_transaction_id',
        'gateway_response',
        'callback_payload',
        'paid_at',
        'failed_at',
    ];

    protected $hidden = [
        'gateway_response',
        'callback_payload',
    ];

    protected function casts(): array
    {
        return [
            'method' => PaymentMethod::class,
            'status' => PaymentStatus::class,
            'amount_cents' => 'integer',
            'gateway_response' => 'encrypted:array',
            'callback_payload' => 'encrypted:array',
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
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

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
