<?php

namespace App\Http\Controllers\Api\V1;

use App\Commerce\Actions\CancelOrder;
use App\Http\Resources\Commerce\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** @group Orders */
class OrderController extends ApiController
{
    /** List the customer's orders. */
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()->with('items')->latest()->paginate(min(max($request->integer('per_page', 20), 1), 50));

        return $this->success(OrderResource::collection($orders->items())->resolve(), 'Orders retrieved successfully.', meta: [
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'per_page' => $orders->perPage(),
            'total' => $orders->total(),
        ]);
    }

    /** Retrieve an owned order. */
    public function show(Request $request, Order $order): JsonResponse
    {
        Gate::authorize('view', $order);

        return $this->success((new OrderResource($order->load('items')))->resolve(), 'Order retrieved successfully.');
    }

    /** Cancel an owned pending unpaid order. */
    public function cancel(Request $request, Order $order, CancelOrder $action): JsonResponse
    {
        Gate::authorize('cancel', $order);

        return $this->success((new OrderResource($action->execute($order)))->resolve(), 'Order cancelled successfully.');
    }
}
