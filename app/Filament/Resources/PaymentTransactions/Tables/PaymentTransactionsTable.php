<?php

namespace App\Filament\Resources\PaymentTransactions\Tables;

use App\Filament\Resources\PaymentTransactions\Actions\ConfirmCashPaymentAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table->modifyQueryUsing(fn ($query) => $query->with(['user', 'order']))->columns([
            TextColumn::make('reference')->copyable()->searchable()->limit(13),
            TextColumn::make('order_reference')->label('Order')->copyable()->searchable()->limit(13),
            TextColumn::make('user.name')->label('Customer'), TextColumn::make('method')->badge(),
            TextColumn::make('status')->badge()->sortable(), TextColumn::make('amount_cents')->money('EGP', divideBy: 100)->sortable(),
            TextColumn::make('gateway_transaction_id')->label('Gateway ID')->searchable()->placeholder('—'),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ])->filters([
            SelectFilter::make('method')->options(['paymob' => 'Paymob', 'cash_on_delivery' => 'Cash on delivery']),
            SelectFilter::make('status')->options(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed']),
        ])->recordActions([ViewAction::make(), ConfirmCashPaymentAction::make()])->defaultSort('created_at', 'desc');
    }
}
