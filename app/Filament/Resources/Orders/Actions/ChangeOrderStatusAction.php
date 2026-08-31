<?php

namespace App\Filament\Resources\Orders\Actions;

use App\Commerce\Actions\TransitionOrderStatus;
use App\Commerce\Enums\OrderStatus;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class ChangeOrderStatusAction
{
    public static function make(): Action
    {
        return Action::make('changeStatus')
            ->label('Change status')->icon('heroicon-o-arrow-path')->requiresConfirmation()
            ->visible(fn (Order $record) => $record->status->allowedTransitions() !== [] && auth()->user()?->can('manage orders'))
            ->schema(fn (Order $record) => [
                Select::make('status')->options(collect($record->status->allowedTransitions())->mapWithKeys(
                    fn (OrderStatus $status) => [$status->value => $status->label()]
                )->all())->required(),
            ])->action(function (Order $record, array $data, TransitionOrderStatus $transition): void {
                $transition->execute($record, OrderStatus::from($data['status']), auth()->user());
                Notification::make()->title('Order status updated')->success()->send();
            });
    }
}
