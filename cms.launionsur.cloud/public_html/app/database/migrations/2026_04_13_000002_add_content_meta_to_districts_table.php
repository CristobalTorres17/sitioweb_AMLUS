<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('districts', function (Blueprint $table) {
            if (!Schema::hasColumn('districts', 'content')) {
                $table->longText('content')->nullable()->after('description');
            }

            if (!Schema::hasColumn('districts', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('is_active');
            }

            if (!Schema::hasColumn('districts', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('districts', function (Blueprint $table) {
            foreach (['content', 'meta_title', 'meta_description'] as $col) {
                if (Schema::hasColumn('districts', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
