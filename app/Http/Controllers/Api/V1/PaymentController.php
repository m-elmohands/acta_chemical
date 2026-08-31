<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\InitiatePaymentRequest;
use App\Http\Resources\Payments\PaymentTransactionResource;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Payments\Actions\InitiatePayment;
use App\Payments\Exceptions\PaymentGatewayException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** @group Payments */
class PaymentController extends Controller
{
    /** Retry payment for an owned pending order. */
    public function store(InitiatePaymentRequest $request, InitiatePayment $action): JsonResponse
    {
        try {
            $data = $request->validated();
            $order = Order::query()
                ->where('user_id', $request->user()->id)
                ->where('reference', $data['order_reference'])
                ->firstOrFail();
            abort_if($order->status === 'cancelled' || $order->payment_status === 'paid', 422, 'This order cannot accept another payment.');
            abort_if($order->payment_method !== $data['method'], 422, 'The payment method must match the order.');

            $payment = $action->execute([
                'user_id' => $request->user()->id,
                'order_id' => $order->id,
                'order_reference' => $order->reference,
                'method' => $data['method'],
                'amount' => $order->total_cents / 100,
                'billing_data' => $order->shipping_address + ['email' => $request->user()->email],
            ]);
        } catch (PaymentGatewayException $exception) {
            return response()->json([
                'data' => null,
                'message' => $exception->getMessage(),
                'errors' => ['payment' => [$exception->getMessage()]],
            ], 502);
        }

        return response()->json([
            'data' => [
                'transaction' => (new PaymentTransactionResource($payment['transaction']))->resolve(),
                'checkout' => $payment['result']->checkout,
            ],
            'message' => 'Payment initialized successfully.',
            'errors' => null,
        ], 201);
    }

    /** Retrieve an owned payment transaction. */
    public function show(Request $request, PaymentTransaction $payment): JsonResponse
    {
        abort_unless($payment->user_id === $request->user()->id, 404);

        return response()->json([
            'data' => (new PaymentTransactionResource($payment))->resolve(),
            'message' => 'Payment retrieved successfully.',
            'errors' => null,
        ]);
    }
}
