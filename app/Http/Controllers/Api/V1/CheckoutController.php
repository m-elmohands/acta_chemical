<?php

namespace App\Http\Controllers\Api\V1;

use App\Commerce\Actions\CreateOrderFromCart;
use App\Http\Requests\Commerce\CheckoutRequest;
use App\Http\Resources\Commerce\OrderResource;
use App\Payments\Actions\InitiatePayment;
use App\Payments\Exceptions\PaymentGatewayException;
use Illuminate\Http\JsonResponse;

/** @group Orders */
class CheckoutController extends ApiController
{
    /** Create an order from the authenticated customer's cart. */
    public function store(
        CheckoutRequest $request,
        CreateOrderFromCart $createOrder,
        InitiatePayment $initiatePayment,
    ): JsonResponse {
        $order = $createOrder->execute($request->user(), $request->validated());

        try {
            $payment = $initiatePayment->execute([
                'user_id' => $request->user()->id,
                'order_id' => $order->id,
                'order_reference' => $order->reference,
                'method' => $order->payment_method,
                'amount' => $order->total_cents / 100,
                'billing_data' => $order->shipping_address + ['email' => $request->user()->email],
            ]);
        } catch (PaymentGatewayException $exception) {
            return response()->json([
                'data' => ['order' => (new OrderResource($order))->resolve()],
                'message' => $exception->getMessage(),
                'errors' => ['payment' => [$exception->getMessage()]],
            ], 502);
        }

        return $this->success([
            'order' => (new OrderResource($order))->resolve(),
            'payment' => [
                'reference' => $payment['transaction']->reference,
                'status' => $payment['transaction']->status->value,
                'checkout' => $payment['result']->checkout,
            ],
        ], 'Order placed successfully.', 201);
    }
}
