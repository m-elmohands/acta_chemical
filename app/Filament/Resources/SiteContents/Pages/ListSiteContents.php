<?php

namespace App\Filament\Resources\SiteContents\Pages;

use App\Filament\Resources\SiteContents\SiteContentResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListSiteContents extends ListRecords
{
    protected static string $resource = SiteContentResource::class;

    public function getTitle(): string|Htmlable
    {
        return SiteContentResource::currentSection()['label'] ?? parent::getTitle();
    }

    public function getBreadcrumb(): ?string
    {
        return SiteContentResource::currentSection()['label'] ?? parent::getBreadcrumb();
    }
}
