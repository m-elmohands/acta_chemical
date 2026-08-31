<?php

namespace App\Payments\Gateways;

use App\Models\PaymentTransaction;
use App\Payments\Contracts\PaymentGateway;
use App\Payments\Data\PaymentInitiationResult;
use App\Payments\Exceptions\PaymentGatewayException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class PaymobGateway implements PaymentGateway
{
    public function initiate(PaymentTransaction $transaction, array $data): PaymentInitiationResult
    {
        $secretKey = (string) config('services.paymob.secret_key');
        $publicKey = (string) config('services.paymob.public_key');
        $integrationId = config('services.paymob.integration_id');

        if ($secretKey === '' || $publicKey === '' || blank($integrationId)) {
            throw new PaymentGatewayException('Paymob is not configured.');
        }

        try {
            $response = Http::baseUrl((string) config('services.paymob.base_url'))
                ->withToken($secretKey)
                ->acceptJson()
                ->asJson()
                ->timeout(15)
                ->retry(2, 250, throw: false)
                ->post('/v1/intention/', [
                    'amount' => $transaction->amount_cents,
                    'currency' => $transaction->currency,
                    'payment_methods' => [(int) $integrationId],
                    'items' => $data['items'] ?? [],
                    'billing_data' => $this->billingData((array) $data['billing_data']),
                    'special_reference' => $transaction->reference,
                    'notification_url' => route('api.v1.payments.paymob.webhook'),
                    'redirection_url' => config('services.paymob.redirect_url'),
                ]);
        } catch (ConnectionException $exception) {
            throw new PaymentGatewayException('Paymob is currently unavailable.', previous: $exception);
        }

        if ($response->failed()) {
            throw new PaymentGatewayException('Paymob rejected the payment request.');
        }

        /** @var array<string, mixed> $payload */
        $payload = $response->json();
        $clientSecret = $payload['client_secret'] ?? null;

        if (! is_string($clientSecret) || $clientSecret === '') {
            throw new PaymentGatewayException('Paymob returned an invalid payment response.');
        }

        $checkoutUrl = rtrim((string) config('services.paymob.checkout_url'), '/').'/?'.http_build_query([
            'publicKey' => $publicKey,
            'clientSecret' => $clientSecret,
        ]);

        return new PaymentInitiationResult(
            gatewayReference: isset($payload['id']) ? (string) $payload['id'] : null,
            checkout: [
                'type' => 'paymob',
                'client_secret' => $clientSecret,
                'public_key' => $publicKey,
                'checkout_url' => $checkoutUrl,
            ],
            gatewayResponse: $payload,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string>
     */
    private function billingData(array $data): array
    {
        return [
            'first_name' => (string) ($data['first_name'] ?? 'NA'),
            'last_name' => (string) ($data['last_name'] ?? 'NA'),
            'email' => (string) ($data['email'] ?? 'NA'),
            'phone_number' => (string) ($data['phone_number'] ?? $data['phone'] ?? 'NA'),
            'street' => (string) ($data['street'] ?? 'NA'),
            'building' => (string) ($data['building'] ?? 'NA'),
            'floor' => (string) ($data['floor'] ?? 'NA'),
            'apartment' => (string) ($data['apartment'] ?? 'NA'),
            'city' => (string) ($data['city'] ?? 'Cairo'),
            'state' => (string) ($data['state'] ?? 'Cairo'),
            'country' => (string) ($data['country'] ?? 'EG'),
        ];
    }
}
