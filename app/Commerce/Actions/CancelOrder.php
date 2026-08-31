<?php

namespace App\Commerce\Actions;

use App\Commerce\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CancelOrder
{
    public function execute(Order $order): Order
    {
        return DB::transaction(function () use ($order): Order {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->status !== OrderStatus::Pending || $order->payment_status === 'paid') {
                throw ValidationException::withMessages(['order' => ['This order can no longer be cancelled.']]);
            }

            $order->load('items');

            foreach ($order->items as $item) {
                if ($item->product_id !== null) {
                    Product::query()->whereKey($item->product_id)->where('track_inventory', true)->increment('stock_quantity', $item->quantity);
                }
            }

            $order->update(['status' => OrderStatus::Cancelled, 'cancelled_at' => now()]);

            return $order->refresh()->load('items');
        });
    }
}
