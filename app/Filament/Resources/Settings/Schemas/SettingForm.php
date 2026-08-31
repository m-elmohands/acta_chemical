<?php

namespace App\Filament\Resources\Settings\Schemas;

use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title_en')->label('Title (English)')->required(),
                TextInput::make('title_ar')->label('Title (Arabic)')->required(),
                Textarea::make('description_en')->label('Description (English)')->columnSpanFull(),
                Textarea::make('description_ar')->label('Description (Arabic)')->columnSpanFull(),
                TextInput::make('email')->email(),
                TextInput::make('watts')->label('WhatsApp'),
                TextInput::make('phone')->tel(),
                TextInput::make('address')->columnSpanFull(),
                FileUpload::make('logo')->image()->disk('public')->directory('settings')->columnSpanFull(),
                Map::make('location')
                    ->label('Location')
                    ->columnSpanFull()
                    ->defaultLocation(latitude: 30.0444, longitude: 31.2357)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('lat', $state['lat']);
                        $set('lng', $state['lng']);
                    })
                    ->afterStateHydrated(function ($state, callable $set, $record) {
                        if ($record && $record->lat && $record->lng) {
                            $set('location', ['lat' => $record->lat, 'lng' => $record->lng]);
                        }
                    })
                    ->dehydrated(false),
                TextInput::make('lat')->label('Latitude')->readOnly(),
                TextInput::make('lng')->label('Longitude')->readOnly(),
            ]);
    }
}
