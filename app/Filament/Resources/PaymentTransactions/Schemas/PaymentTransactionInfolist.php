<?php

namespace App\Filament\Resources\PaymentTransactions\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentTransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Transaction')->schema([
                TextEntry::make('reference')->copyable(), TextEntry::make('order_reference')->copyable(),
                TextEntry::make('method')->badge(), TextEntry::make('status')->badge(),
                TextEntry::make('amount_cents')->money('EGP', divideBy: 100), TextEntry::make('created_at')->dateTime(),
                TextEntry::make('gateway_reference')->copyable()->placeholder('—'),
                TextEntry::make('gateway_transaction_id')->copyable()->placeholder('—'),
                TextEntry::make('paid_at')->dateTime()->placeholder('—'), TextEntry::make('failed_at')->dateTime()->placeholder('—'),
            ])->columns(3),
            Section::make('Gateway data')->collapsed()->schema([
                KeyValueEntry::make('gateway_response'), KeyValueEntry::make('callback_payload'),
            ]),
        ]);
    }
}
