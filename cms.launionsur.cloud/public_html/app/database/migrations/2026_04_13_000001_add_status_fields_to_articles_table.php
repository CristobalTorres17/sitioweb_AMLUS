<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'status')) {
                $table->enum('status', ['draft', 'published', 'archived'])
                    ->default('draft')
                    ->after('is_published');
            }

            if (!Schema::hasColumn('articles', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }

            if (!Schema::hasColumn('articles', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('is_featured');
            }

            if (!Schema::hasColumn('articles', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
        });

        // Migrate existing data: is_published=1 → status='published'
        DB::statement("UPDATE articles SET status = CASE WHEN is_published = 1 THEN 'published' ELSE 'draft' END");
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            foreach (['meta_description', 'meta_title', 'is_featured', 'status'] as $col) {
                if (Schema::hasColumn('articles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
