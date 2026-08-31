<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EventInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title_en')
                    ->label('Title (English)'),
                TextEntry::make('title_ar')
                    ->label('Title (Arabic)'),
                TextEntry::make('content_en')
                    ->label('Content (English)')
                    ->columnSpanFull(),
                TextEntry::make('content_ar')
                    ->label('Content (Arabic)')
                    ->columnSpanFull(),
                ImageEntry::make('image')
                    ->disk('public')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
