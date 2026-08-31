<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Category')->schema([
                Select::make('parent_id')->relationship('parent', 'name_en')->searchable()->preload(),
                TextInput::make('name_en')->label('English name')->required()->live(onBlur: true)
                    ->afterStateUpdated(fn (?string $state, callable $set) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('name_ar')->label('Arabic name')->required(),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Textarea::make('description_en')->label('English description')->columnSpanFull(),
                Textarea::make('description_ar')->label('Arabic description')->columnSpanFull(),
                TextInput::make('sort_order')->numeric()->default(0)->required(),
                Toggle::make('is_active')->default(true),
                SpatieMediaLibraryFileUpload::make('image')->collection('image')->image()->imageEditor()->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
        ]);
    }
}
