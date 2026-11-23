<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cms_blocks', function (Blueprint $table) {
            if (!Schema::hasColumn('cms_blocks', 'template_key')) {
                $table->string('template_key')->default('rich_text')->after('slug');
            }

            if (!Schema::hasColumn('cms_blocks', 'zone')) {
                $table->string('zone')->default('content')->after('template_key');
            }

            if (!Schema::hasColumn('cms_blocks', 'template_data')) {
                $table->json('template_data')->nullable()->after('zone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cms_blocks', function (Blueprint $table) {
            if (Schema::hasColumn('cms_blocks', 'template_data')) {
                $table->dropColumn('template_data');
            }

            if (Schema::hasColumn('cms_blocks', 'zone')) {
                $table->dropColumn('zone');
            }

            if (Schema::hasColumn('cms_blocks', 'template_key')) {
                $table->dropColumn('template_key');
            }
        });
    }
};
