<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            SpatieMediaLibraryImageColumn::make('thumbnail')->collection('thumbnail')->conversion('thumb')->square(),
            TextColumn::make('name_en')->label('Product')->searchable(['name_en', 'name_ar', 'sku'])->sortable(),
            TextColumn::make('sku')->searchable()->copyable(),
            TextColumn::make('category.name_en')->label('Category')->sortable(),
            TextColumn::make('price')->money('EGP')->sortable(),
            TextColumn::make('sale_price')->money('EGP')->placeholder('—')->sortable(),
            TextColumn::make('stock_quantity')->label('Stock')->sortable()->color(fn (int $state) => $state <= 5 ? 'danger' : null),
            IconColumn::make('is_active')->boolean(),
            IconColumn::make('is_featured')->boolean(),
        ])->filters([
            SelectFilter::make('category')->relationship('category', 'name_en')->searchable()->preload(),
            TernaryFilter::make('is_active'), TernaryFilter::make('is_featured'), TrashedFilter::make(),
        ])->recordActions([EditAction::make(), DeleteAction::make()->requiresConfirmation(), RestoreAction::make()])
            ->defaultSort('created_at', 'desc');
    }
}
