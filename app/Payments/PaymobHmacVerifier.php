<?php

namespace App\Payments;

class PaymobHmacVerifier
{
    /** @var list<string> */
    private const KEYS = [
        'amount_cents',
        'created_at',
        'currency',
        'error_occured',
        'has_parent_transaction',
        'id',
        'integration_id',
        'is_3d_secure',
        'is_auth',
        'is_capture',
        'is_refunded',
        'is_standalone_payment',
        'is_voided',
        'order.id',
        'owner',
        'pending',
        'source_data.pan',
        'source_data.sub_type',
        'source_data.type',
        'success',
    ];

    /** @param array<string, mixed> $object */
    public function verify(array $object, string $providedHmac): bool
    {
        $secret = (string) config('services.paymob.hmac_secret');

        if ($secret === '' || $providedHmac === '') {
            return false;
        }

        $value = collect(self::KEYS)
            ->map(fn (string $key): string => $this->stringValue(data_get($object, $key, '')))
            ->implode('');

        return hash_equals(hash_hmac('sha512', $value, $secret), $providedHmac);
    }

    private function stringValue(mixed $value): string
    {
        return match (true) {
            $value === true => 'true',
            $value === false => 'false',
            $value === null => '',
            default => (string) $value,
        };
    }
}
