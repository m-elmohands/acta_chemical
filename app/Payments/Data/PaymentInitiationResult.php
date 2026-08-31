<?php

namespace App\Payments\Data;

readonly class PaymentInitiationResult
{
    /**
     * @param  array<string, mixed>  $checkout
     * @param  array<string, mixed>  $gatewayResponse
     */
    public function __construct(
        public ?string $gatewayReference = null,
        public array $checkout = [],
        public array $gatewayResponse = [],
    ) {}
}
