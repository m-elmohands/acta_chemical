<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Payments\Enums\PaymentStatus;
use App\Payments\PaymobHmacVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** @group Payments */
class PaymobWebhookController extends Controller
{
    /**
     * Process a Paymob transaction callback.
     *
     * This endpoint is authenticated using Paymob's callback HMAC.
     *
     * @unauthenticated
     *
     * @queryParam hmac string required Paymob SHA-512 callback signature.
     */
    public function __invoke(Request $request, PaymobHmacVerifier $verifier): JsonResponse
    {
        /** @var array<string, mixed> $object */
        $object = (array) $request->input('obj', []);

        if (! $verifier->verify($object, (string) $request->query('hmac'))) {
            return response()->json([
                'data' => null,
                'message' => 'Invalid Paymob callback signature.',
                'errors' => ['hmac' => ['The callback signature is invalid.']],
            ], 422);
        }

        $transaction = $this->findTransaction($object);

        if ($transaction === null) {
            return response()->json([
                'data' => null,
                'message' => 'Payment transaction not found.',
                'errors' => ['payment' => ['The payment transaction was not found.']],
            ], 404);
        }

        $transaction->callback_payload = $request->all();
        $transaction->gateway_transaction_id = isset($object['id']) ? (string) $object['id'] : null;

        if ($transaction->status !== PaymentStatus::Paid) {
            if (($object['success'] ?? false) === true && ($object['pending'] ?? true) === false) {
                $transaction->status = PaymentStatus::Paid;
                $transaction->paid_at = now();
                $transaction->failed_at = null;
            } elseif (($object['pending'] ?? false) === false) {
                $transaction->status = PaymentStatus::Failed;
                $transaction->failed_at = now();
            }
        }

        $transaction->save();

        if ($transaction->order !== null && $transaction->status === PaymentStatus::Paid) {
            $transaction->order->update(['payment_status' => 'paid']);
        }

        return response()->json([
            'data' => ['reference' => $transaction->reference, 'status' => $transaction->status->value],
            'message' => 'Paymob callback processed successfully.',
            'errors' => null,
        ]);
    }

    /** @param array<string, mixed> $object */
    private function findTransaction(array $object): ?PaymentTransaction
    {
        $merchantReference = data_get($object, 'order.merchant_order_id');
        $gatewayReference = data_get($object, 'order.id');

        return PaymentTransaction::query()
            ->when(filled($merchantReference), fn ($query) => $query->where('reference', $merchantReference))
            ->when(blank($merchantReference) && filled($gatewayReference), fn ($query) => $query->where('gateway_reference', $gatewayReference))
            ->first();
    }
}
