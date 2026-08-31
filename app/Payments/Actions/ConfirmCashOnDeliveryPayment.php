<?php

namespace App\Payments\Actions;

use App\Models\PaymentTransaction;
use App\Models\User;
use App\Payments\Enums\PaymentMethod;
use App\Payments\Enums\PaymentStatus;
use DomainException;
use Illuminate\Support\Facades\DB;

class ConfirmCashOnDeliveryPayment
{
    public function execute(PaymentTransaction $payment, User $administrator): PaymentTransaction
    {
        if ($payment->method !== PaymentMethod::CashOnDelivery || $payment->status !== PaymentStatus::Pending) {
            throw new DomainException('Only pending cash-on-delivery payments can be confirmed manually.');
        }

        return DB::transaction(function () use ($payment, $administrator): PaymentTransaction {
            $payment->forceFill(['status' => PaymentStatus::Paid, 'paid_at' => now(), 'failed_at' => null])->save();
            $payment->order?->forceFill(['payment_status' => PaymentStatus::Paid->value])->save();

            activity('payments')->causedBy($administrator)->performedOn($payment)
                ->withProperties(['status' => PaymentStatus::Paid->value])->log('Cash-on-delivery payment confirmed');

            return $payment->refresh();
        });
    }
}
