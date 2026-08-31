<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Product details')->schema([
                Select::make('category_id')->relationship('category', 'name_en')->searchable()->preload()->required(),
                TextInput::make('sku')->required()->unique(ignoreRecord: true),
                TextInput::make('name_en')->label('English name')->required()->live(onBlur: true)
                    ->afterStateUpdated(fn (?string $state, callable $set) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('name_ar')->label('Arabic name')->required(),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Textarea::make('short_description_en')->label('English short description'),
                Textarea::make('short_description_ar')->label('Arabic short description'),
                RichEditor::make('description_en')->label('English description')->columnSpanFull(),
                RichEditor::make('description_ar')->label('Arabic description')->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
            Section::make('Pricing and inventory')->schema([
                TextInput::make('price')->numeric()->prefix('EGP')->minValue(0)->required(),
                TextInput::make('sale_price')->numeric()->prefix('EGP')->minValue(0)->lte('price'),
                TextInput::make('stock_quantity')->numeric()->minValue(0)->default(0)->required(),
                TextInput::make('sort_order')->numeric()->minValue(0)->default(0)->required(),
                Toggle::make('track_inventory')->default(true),
                Toggle::make('is_active')->default(true),
                Toggle::make('is_featured')->default(false),
            ])->columns(3),
            Section::make('Media')->schema([
                SpatieMediaLibraryFileUpload::make('thumbnail')->collection('thumbnail')->image()->imageEditor()->required()->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('gallery')->collection('gallery')->multiple()->reorderable()->image()->imageEditor()->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
