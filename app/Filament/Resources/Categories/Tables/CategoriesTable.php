<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            SpatieMediaLibraryImageColumn::make('image')->collection('image')->conversion('thumb')->circular(),
            TextColumn::make('name_en')->label('Name')->searchable(['name_en', 'name_ar'])->sortable(),
            TextColumn::make('parent.name_en')->label('Parent')->placeholder('Root'),
            TextColumn::make('products_count')->counts('products')->label('Products'),
            IconColumn::make('is_active')->boolean(),
            TextColumn::make('sort_order')->sortable(),
        ])->filters([TrashedFilter::make()])->recordActions([
            EditAction::make(), DeleteAction::make()->requiresConfirmation(), RestoreAction::make(),
        ])->defaultSort('sort_order');
    }
}
