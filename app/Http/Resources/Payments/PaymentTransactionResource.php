<?php

namespace App\Http\Resources\Payments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentTransactionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'reference' => $this->reference,
            'order_reference' => $this->order_reference,
            'method' => $this->method->value,
            'status' => $this->status->value,
            'amount' => number_format($this->amount_cents / 100, 2, '.', ''),
            'amount_cents' => $this->amount_cents,
            'currency' => $this->currency,
            'gateway_reference' => $this->gateway_reference,
            'gateway_transaction_id' => $this->gateway_transaction_id,
            'paid_at' => $this->paid_at?->toISOString(),
            'failed_at' => $this->failed_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
