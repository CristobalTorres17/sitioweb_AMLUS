<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cms_block_page', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_block_id')
                ->constrained('cms_blocks')
                ->cascadeOnDelete();
            $table->string('page'); // Page slug
            $table->integer('position')->default(0);
            $table->timestamps();

            // Indexes
            $table->unique(['cms_block_id', 'page']);
            $table->index('page');
            $table->index(['page', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_block_page');
    }
};
