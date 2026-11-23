<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'type')) {
                $table->string('type')->default('article')->after('slug');
            }

            if (!Schema::hasColumn('categories', 'description')) {
                $table->text('description')->nullable()->after('type');
            }

            if (!Schema::hasColumn('categories', 'icon')) {
                $table->string('icon')->nullable()->after('description');
            }

            if (!Schema::hasColumn('categories', 'color')) {
                $table->string('color')->nullable()->after('icon');
            }

            if (!Schema::hasColumn('categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('color');
            }

            if (!Schema::hasColumn('categories', 'position')) {
                $table->integer('position')->default(0)->after('is_active');
            }

            if (!Schema::hasColumn('categories', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });

        try {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropUnique('categories_slug_unique');
            });
        } catch (\Throwable) {
            // Ignore if the legacy unique index is missing.
        }

        try {
            Schema::table('categories', function (Blueprint $table) {
                $table->unique(['type', 'slug'], 'categories_type_slug_unique');
            });
        } catch (\Throwable) {
            // Ignore if the composite unique already exists.
        }

        DB::table('categories')
            ->whereNull('type')
            ->orWhere('type', '')
            ->update(['type' => 'article']);

        if (Schema::hasTable('service_categories')) {
            $legacyCategories = DB::table('service_categories')->orderBy('id')->get();

            foreach ($legacyCategories as $legacyCategory) {
                $payload = [
                    'name' => $legacyCategory->name,
                    'slug' => $legacyCategory->slug,
                    'type' => 'service',
                    'description' => $legacyCategory->description,
                    'icon' => $legacyCategory->icon ?? null,
                    'color' => $legacyCategory->color ?? null,
                    'is_active' => (bool) $legacyCategory->is_active,
                    'position' => (int) $legacyCategory->position,
                    'created_at' => $legacyCategory->created_at,
                    'updated_at' => $legacyCategory->updated_at,
                    'deleted_at' => $legacyCategory->deleted_at,
                ];

                $newCategoryId = DB::table('categories')
                    ->where('type', 'service')
                    ->where('slug', $legacyCategory->slug)
                    ->value('id');

                if ($newCategoryId) {
                    DB::table('categories')->where('id', $newCategoryId)->update($payload);
                } else {
                    $newCategoryId = DB::table('categories')->insertGetId($payload);
                }

                DB::table('services')
                    ->where('category_id', $legacyCategory->id)
                    ->update(['category_id' => $newCategoryId]);
            }
        }

        Schema::table('services', function (Blueprint $table) {
            try {
                $table->dropForeign(['category_id']);
            } catch (\Throwable) {
                // Ignore if the FK was already removed.
            }

            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            try {
                $table->dropForeign(['category_id']);
            } catch (\Throwable) {
                // Ignore if the FK was already removed.
            }

            $table->foreign('category_id')->references('id')->on('service_categories')->nullOnDelete();
        });

        Schema::table('categories', function (Blueprint $table) {
            try {
                $table->dropUnique('categories_type_slug_unique');
            } catch (\Throwable) {
                // Ignore if the unique was not created.
            }

            try {
                $table->unique('slug');
            } catch (\Throwable) {
                // Ignore if the legacy unique already exists.
            }

            foreach (['deleted_at', 'position', 'is_active', 'color', 'icon', 'description', 'type'] as $column) {
                if (Schema::hasColumn('categories', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
