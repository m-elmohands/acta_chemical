<?php

namespace App\Models\Concerns;

trait HasLocalizedAttributes
{
    public function localized(string $field, ?string $locale = null): ?string
    {
        $locale = $this->normalizeLocale($locale);
        $fallbackLocale = $locale === 'ar' ? 'en' : 'ar';

        return $this->getAttribute("{$field}_{$locale}")
            ?: $this->getAttribute("{$field}_{$fallbackLocale}");
    }

    public function translations(string $field): array
    {
        return [
            'en' => $this->getAttribute("{$field}_en"),
            'ar' => $this->getAttribute("{$field}_ar"),
        ];
    }

    protected function localizedFields(): array
    {
        return property_exists($this, 'localizedFields') ? $this->localizedFields : [];
    }

    private function normalizeLocale(?string $locale): string
    {
        $locale ??= app()->getLocale();

        return str_starts_with(strtolower($locale), 'ar') ? 'ar' : 'en';
    }
}
