<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Customer')->schema([TextEntry::make('name'), TextEntry::make('email'), TextEntry::make('phone')->placeholder('—'), TextEntry::make('created_at')->dateTime()])->columns(4),
            Section::make('Addresses')->schema([RepeatableEntry::make('addresses')->schema([TextEntry::make('label'), TextEntry::make('first_name'), TextEntry::make('last_name'), TextEntry::make('phone'), TextEntry::make('street'), TextEntry::make('city')])->columns(3)]),
            Section::make('Recent orders')->schema([RepeatableEntry::make('orders')->schema([TextEntry::make('reference'), TextEntry::make('status')->badge(), TextEntry::make('total_cents')->money('EGP', divideBy: 100), TextEntry::make('placed_at')->dateTime()])->columns(4)]),
        ]);
    }
}
