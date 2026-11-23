<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('svg_path_id', 100)->nullable();

            $table->text('description')->nullable();
            $table->text('short_summary')->nullable();

            $table->foreignId('hero_image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->foreignId('map_image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->foreignId('thumbnail_id')->nullable()->constrained('media')->nullOnDelete();

            $table->string('color', 20)->nullable()->default('#ffcc00');
            $table->string('icon', 100)->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
