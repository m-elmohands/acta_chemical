<?php

namespace App\Payments;

use App\Payments\Contracts\PaymentGateway;
use App\Payments\Enums\PaymentMethod;
use App\Payments\Gateways\CashOnDeliveryGateway;
use App\Payments\Gateways\PaymobGateway;

class PaymentGatewayManager
{
    public function for(PaymentMethod $method): PaymentGateway
    {
        return match ($method) {
            PaymentMethod::Paymob => app(PaymobGateway::class),
            PaymentMethod::CashOnDelivery => app(CashOnDeliveryGateway::class),
        };
    }
}
