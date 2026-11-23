<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add icon column to services table
        Schema::table('services', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('documentation');
        });

        // Add is_basic flag to service_requirements
        Schema::table('service_requirements', function (Blueprint $table) {
            $table->boolean('is_basic')->default(true)->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('icon');
        });

        Schema::table('service_requirements', function (Blueprint $table) {
            $table->dropColumn('is_basic');
        });
    }
};
