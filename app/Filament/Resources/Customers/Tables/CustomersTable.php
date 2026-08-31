<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table->modifyQueryUsing(fn ($query) => $query->withCount(['orders', 'addresses']))->columns([TextColumn::make('name')->searchable()->sortable(), TextColumn::make('email')->searchable()->copyable(), TextColumn::make('phone')->searchable(), TextColumn::make('orders_count')->label('Orders')->sortable(), TextColumn::make('addresses_count')->label('Addresses'), TextColumn::make('created_at')->dateTime()->sortable()])->recordActions([ViewAction::make(), EditAction::make()])->defaultSort('created_at', 'desc');
    }
}
