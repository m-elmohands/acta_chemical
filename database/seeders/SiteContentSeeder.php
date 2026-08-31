<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    /**
     * Seed only the CMS records without touching users, settings, articles, events, or contacts.
     */
    public function run(): void
    {
        app(DatabaseSeeder::class)->seedSiteContentsOnly();
    }
}
