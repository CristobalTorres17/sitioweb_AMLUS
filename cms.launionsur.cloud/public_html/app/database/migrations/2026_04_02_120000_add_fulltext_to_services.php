<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only add FULLTEXT on MySQL/MariaDB. Skip for sqlite (tests) or other drivers.
        $driver = Schema::getConnection()->getDriverName();
        if (in_array($driver, ['mysql', 'mysqli'])) {
            // Add a FULLTEXT index on title and description for faster searching
            DB::statement('ALTER TABLE services ADD FULLTEXT services_fulltext_title_description (title, description)');
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if (in_array($driver, ['mysql', 'mysqli'])) {
            DB::statement('ALTER TABLE services DROP INDEX services_fulltext_title_description');
        }
    }
};
