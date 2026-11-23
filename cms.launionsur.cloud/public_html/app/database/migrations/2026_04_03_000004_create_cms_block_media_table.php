<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cms_block_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_block_id')
                ->constrained('cms_blocks')
                ->cascadeOnDelete();
            $table->foreignId('media_id')
                ->constrained('media')
                ->cascadeOnDelete();
            $table->integer('position')->default(0);
            $table->text('caption')->nullable();
            $table->string('alt_text', 255)->nullable();
            $table->timestamps();

            // Indexes
            $table->index('cms_block_id');
            $table->unique(['cms_block_id', 'media_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_block_media');
    }
};
