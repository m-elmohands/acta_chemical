<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([Section::make('Customer')->schema([TextInput::make('name')->required(), TextInput::make('email')->email()->required()->unique(ignoreRecord: true), TextInput::make('phone')->tel()->unique(ignoreRecord: true)])->columns(3)]);
    }
}
