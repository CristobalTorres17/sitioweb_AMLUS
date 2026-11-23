<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            // Add new fields if they don't exist
            if (!Schema::hasColumn('media', 'original_filename')) {
                $table->string('original_filename')->nullable()->after('filename');
            }
            if (!Schema::hasColumn('media', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('size');
            }
            if (!Schema::hasColumn('media', 'metadata')) {
                $table->json('metadata')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('media', 'deleted_at')) {
                $table->softDeletes()->after('metadata');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn('original_filename', 'is_active', 'metadata', 'deleted_at');
        });
    }
};
