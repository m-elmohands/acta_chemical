<?php

namespace App\Payments\Contracts;

use App\Models\PaymentTransaction;
use App\Payments\Data\PaymentInitiationResult;

interface PaymentGateway
{
    /** @param array<string, mixed> $data */
    public function initiate(PaymentTransaction $transaction, array $data): PaymentInitiationResult;
}
