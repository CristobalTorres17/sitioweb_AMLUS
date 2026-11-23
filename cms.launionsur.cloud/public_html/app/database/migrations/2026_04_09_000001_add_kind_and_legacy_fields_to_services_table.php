<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('kind')->default('service')->after('category_id');
            $table->string('cost')->nullable()->after('estimated_time');
            $table->string('unit')->nullable()->after('cost');
            $table->string('schedule')->nullable()->after('unit');
            $table->text('documentation')->nullable()->after('schedule');
        });

        DB::table('services')
            ->whereNull('kind')
            ->orWhere('kind', 'servicio')
            ->orWhere('kind', 'tramite')
            ->update([
                'kind' => DB::raw("CASE WHEN kind = 'tramite' THEN 'procedure' ELSE 'service' END"),
            ]);
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['kind', 'cost', 'unit', 'schedule', 'documentation']);
        });
    }
};
