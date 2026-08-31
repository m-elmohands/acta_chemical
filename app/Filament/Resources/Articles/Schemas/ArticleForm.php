<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title_en')
                    ->label('Title (English)')
                    ->required(),
                TextInput::make('title_ar')
                    ->label('Title (Arabic)')
                    ->required(),
                RichEditor::make('content_en')
                    ->label('Content (English)')
                    ->required()
                    ->columnSpanFull(),
                RichEditor::make('content_ar')
                    ->label('Content (Arabic)')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('articles')
                    ->columnSpanFull(),
            ]);
    }
}
