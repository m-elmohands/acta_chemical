<?php

namespace App\Commerce\Actions;

use App\Commerce\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use DomainException;

class TransitionOrderStatus
{
    public function execute(Order $order, OrderStatus $nextStatus, User $administrator): Order
    {
        $currentStatus = $order->status;

        if (! in_array($nextStatus, $currentStatus->allowedTransitions(), true)) {
            throw new DomainException("An order cannot move from {$currentStatus->value} to {$nextStatus->value}.");
        }

        $order->forceFill([
            'status' => $nextStatus,
            'cancelled_at' => $nextStatus === OrderStatus::Cancelled ? now() : $order->cancelled_at,
        ])->save();

        activity('orders')
            ->causedBy($administrator)
            ->performedOn($order)
            ->withProperties(['from' => $currentStatus->value, 'to' => $nextStatus->value])
            ->log('Order status changed');

        return $order->refresh();
    }
}
