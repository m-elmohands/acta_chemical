<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/** @group Payments */
class PaymentMethodController extends Controller
{
    /**
     * List available EGP payment methods.
     *
     * @unauthenticated
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                [
                    'code' => 'paymob',
                    'name' => ['en' => 'Card payment', 'ar' => 'الدفع بالبطاقة'],
                    'currency' => 'EGP',
                    'requires_online_checkout' => true,
                    'enabled' => filled(config('services.paymob.secret_key'))
                        && filled(config('services.paymob.public_key'))
                        && filled(config('services.paymob.integration_id')),
                ],
                [
                    'code' => 'cash_on_delivery',
                    'name' => ['en' => 'Cash on delivery', 'ar' => 'الدفع عند الاستلام'],
                    'currency' => 'EGP',
                    'requires_online_checkout' => false,
                    'enabled' => true,
                ],
            ],
            'message' => 'Payment methods retrieved successfully.',
            'errors' => null,
        ]);
    }
}
