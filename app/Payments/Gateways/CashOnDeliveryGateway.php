<?php

namespace App\Payments\Gateways;

use App\Models\PaymentTransaction;
use App\Payments\Contracts\PaymentGateway;
use App\Payments\Data\PaymentInitiationResult;

class CashOnDeliveryGateway implements PaymentGateway
{
    public function initiate(PaymentTransaction $transaction, array $data): PaymentInitiationResult
    {
        return new PaymentInitiationResult(checkout: [
            'type' => 'cash_on_delivery',
            'instructions' => 'Pay the order total in cash when it is delivered.',
        ]);
    }
}
