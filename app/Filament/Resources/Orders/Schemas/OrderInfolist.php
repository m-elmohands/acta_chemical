<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order')->schema([
                TextEntry::make('reference')->copyable(), TextEntry::make('status')->badge(),
                TextEntry::make('payment_status')->badge(), TextEntry::make('payment_method'),
                TextEntry::make('placed_at')->dateTime(), TextEntry::make('customer_notes')->placeholder('—'),
            ])->columns(3),
            Section::make('Customer and delivery')->schema([
                TextEntry::make('user.name'), TextEntry::make('user.email'), TextEntry::make('user.phone'),
                TextEntry::make('shipping_address')
                    ->getStateUsing(fn (Order $record): string => self::formatShippingAddress($record->shipping_address))
                    ->columnSpanFull(),
            ])->columns(3),
            Section::make('Items')->schema([
                RepeatableEntry::make('items')->schema([
                    TextEntry::make('name_en')->label('Product'), TextEntry::make('sku'),
                    TextEntry::make('quantity'), TextEntry::make('unit_price_cents')->label('Unit price')->money('EGP', divideBy: 100),
                    TextEntry::make('line_total_cents')->label('Total')->money('EGP', divideBy: 100),
                ])->columns(5),
            ]),
            Section::make('Totals')->schema([
                TextEntry::make('subtotal_cents')->money('EGP', divideBy: 100),
                TextEntry::make('delivery_fee_cents')->money('EGP', divideBy: 100),
                TextEntry::make('total_cents')->money('EGP', divideBy: 100)->weight('bold'),
            ])->columns(3),
        ]);
    }

    private static function formatShippingAddress(mixed $address): string
    {
        if (is_string($address)) {
            $decodedAddress = json_decode($address, true);
            $address = is_array($decodedAddress) ? $decodedAddress : [$address];
        }

        if (! is_array($address)) {
            return '—';
        }

        return collect($address)
            ->filter(fn (mixed $value): bool => is_scalar($value) && filled($value))
            ->map(fn (mixed $value): string => (string) $value)
            ->implode(', ') ?: '—';
    }
}
