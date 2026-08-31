<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Commerce\Enums\OrderStatus;
use App\Filament\Resources\Orders\Actions\ChangeOrderStatusAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table->modifyQueryUsing(fn ($query) => $query->with(['user'])->withCount('items'))->columns([
            TextColumn::make('reference')->searchable()->copyable()->limit(13),
            TextColumn::make('user.name')->label('Customer')->searchable(['name', 'email']),
            TextColumn::make('items_count')->label('Items'),
            TextColumn::make('total_cents')->label('Total')->money('EGP', divideBy: 100)->sortable(),
            TextColumn::make('status')->badge()->sortable(), TextColumn::make('payment_status')->badge()->sortable(),
            TextColumn::make('payment_method'), TextColumn::make('placed_at')->dateTime()->sortable(),
        ])->filters([
            SelectFilter::make('status')->options(collect(OrderStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),
            SelectFilter::make('payment_status')->options(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed']),
            SelectFilter::make('payment_method')->options(['paymob' => 'Paymob', 'cash_on_delivery' => 'Cash on delivery']),
        ])->recordActions([ViewAction::make(), ChangeOrderStatusAction::make()])->defaultSort('placed_at', 'desc');
    }
}
