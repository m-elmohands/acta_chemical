<?php

namespace App\Payments\Actions;

use App\Models\PaymentTransaction;
use App\Payments\Data\PaymentInitiationResult;
use App\Payments\Enums\PaymentMethod;
use App\Payments\Enums\PaymentStatus;
use App\Payments\Exceptions\PaymentGatewayException;
use App\Payments\PaymentGatewayManager;
use Illuminate\Support\Str;

class InitiatePayment
{
    public function __construct(private readonly PaymentGatewayManager $gateways) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array{transaction: PaymentTransaction, result: PaymentInitiationResult}
     */
    public function execute(array $data): array
    {
        $method = PaymentMethod::from($data['method']);
        $transaction = PaymentTransaction::query()->create([
            'reference' => (string) Str::uuid(),
            'user_id' => $data['user_id'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'order_reference' => $data['order_reference'],
            'method' => $method,
            'status' => PaymentStatus::Pending,
            'amount_cents' => (int) round(((float) $data['amount']) * 100),
            'currency' => 'EGP',
        ]);

        try {
            $result = $this->gateways->for($method)->initiate($transaction, $data);
        } catch (PaymentGatewayException $exception) {
            $transaction->update([
                'status' => PaymentStatus::Failed,
                'failed_at' => now(),
            ]);

            throw $exception;
        }

        $transaction->update([
            'gateway_reference' => $result->gatewayReference,
            'gateway_response' => $result->gatewayResponse ?: null,
        ]);

        return ['transaction' => $transaction->refresh(), 'result' => $result];
    }
}
