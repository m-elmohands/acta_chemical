<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LowStockProducts extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->can('view ecommerce dashboard') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table->heading('Low-stock products')->query(
            Product::query()->with('category')->where('track_inventory', true)->where('stock_quantity', '<=', 5)
        )->columns([
            TextColumn::make('name_en')->label('Product')->searchable(), TextColumn::make('sku'),
            TextColumn::make('category.name_en')->label('Category'),
            TextColumn::make('stock_quantity')->label('Stock')->color('danger')->sortable(),
        ])->recordActions([
            Action::make('edit')->url(fn (Product $record) => ProductResource::getUrl('edit', ['record' => $record]))->icon('heroicon-o-pencil-square'),
        ])->defaultSort('stock_quantity');
    }
}
