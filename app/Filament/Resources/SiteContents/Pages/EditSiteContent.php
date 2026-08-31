<?php

namespace App\Filament\Resources\SiteContents\Pages;

use App\Filament\Resources\SiteContents\SiteContentResource;
use Filament\Resources\Pages\EditRecord;

class EditSiteContent extends EditRecord
{
    protected static string $resource = SiteContentResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['key'] = $this->record->key;
        $data['group'] = $this->record->group;
        $data['label'] = $this->record->label;

        return $data;
    }
}
