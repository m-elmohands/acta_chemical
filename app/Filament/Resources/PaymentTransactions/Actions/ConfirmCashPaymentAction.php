<?php

namespace App\Filament\Resources\PaymentTransactions\Actions;

use App\Models\PaymentTransaction;
use App\Payments\Actions\ConfirmCashOnDeliveryPayment;
use App\Payments\Enums\PaymentMethod;
use App\Payments\Enums\PaymentStatus;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ConfirmCashPaymentAction
{
    public static function make(): Action
    {
        return Action::make('confirmCashPayment')->label('Confirm cash received')
            ->icon('heroicon-o-banknotes')->color('success')->requiresConfirmation()
            ->modalDescription('This marks the cash-on-delivery transaction and its order as paid.')
            ->visible(fn (PaymentTransaction $record) => $record->method === PaymentMethod::CashOnDelivery
                && $record->status === PaymentStatus::Pending
                && (auth()->user()?->can('manage payments') ?? false))
            ->action(function (PaymentTransaction $record, ConfirmCashOnDeliveryPayment $confirm): void {
                $confirm->execute($record, auth()->user());
                Notification::make()->title('Payment confirmed')->success()->send();
            });
    }
}
