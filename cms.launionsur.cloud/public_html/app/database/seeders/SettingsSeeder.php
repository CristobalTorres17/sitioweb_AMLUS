<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $defaults = [
            ['key' => 'site_name', 'value' => 'Mi Sitio', 'group' => 'branding'],
            ['key' => 'site_short', 'value' => 'Municipio', 'group' => 'branding'],
            ['key' => 'site_contact_email', 'value' => 'contacto@example.com', 'group' => 'contact'],
            ['key' => 'site_contact_phone', 'value' => '+000000000', 'group' => 'contact'],
            ['key' => 'default_locale', 'value' => 'es', 'group' => 'i18n'],
            ['key' => 'analytics_id', 'value' => '', 'group' => 'integrations'],
        ];

        foreach ($defaults as $row) {
            DB::table('settings')->updateOrInsert(['key' => $row['key']], $row);
        }
    }
}
