<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use App\Models\Setting;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    public function mount(): void
    {
        if (Setting::exists()) {
            redirect()->to(SettingResource::getUrl('edit', ['record' => Setting::first()]))->send();
            exit;
        }

        parent::mount();
    }
}
