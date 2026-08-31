<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EcommerceStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()?->can('view ecommerce dashboard') ?? false;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Orders today', Order::whereDate('created_at', today())->count())
                ->description('All orders placed today')->icon('heroicon-o-shopping-bag'),
            Stat::make('Pending orders', Order::where('status', 'pending')->count())
                ->description('Awaiting confirmation')->color('warning')->icon('heroicon-o-clock'),
            Stat::make('Paid revenue', 'EGP '.number_format(Order::where('payment_status', 'paid')->sum('total_cents') / 100, 2))
                ->description('Lifetime confirmed revenue')->color('success')->icon('heroicon-o-banknotes'),
            Stat::make('Low-stock products', Product::where('track_inventory', true)->where('stock_quantity', '<=', 5)->count())
                ->description('Five units or fewer')->color('danger')->icon('heroicon-o-exclamation-triangle'),
            Stat::make('Customers', User::role('super-admin')->count() === User::count() ? 0 : User::whereDoesntHave('roles')->count())
                ->description('Registered mobile customers')->icon('heroicon-o-users'),
        ];
    }
}
