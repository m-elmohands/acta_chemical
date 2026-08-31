<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use App\Models\Setting;
use Filament\Resources\Pages\EditRecord;

class ManageSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    public function mount(int|string|null $record = null): void
    {
        $setting = Setting::first();

        if (! $setting) {
            $setting = Setting::create([
                'title_en' => 'Acta Chemical',
                'title_ar' => 'اكتا كيميكال',
            ]);
        }

        parent::mount($setting->id);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Settings saved successfully';
    }
}
