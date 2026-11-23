<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cms_block_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_block_id')
                ->constrained('cms_blocks')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('name')->nullable();
            $table->longText('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->json('data')->nullable();
            $table->integer('revision_number')->default(1);
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('cms_block_id');
            $table->index('created_at');
            $table->unique(['cms_block_id', 'revision_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_block_revisions');
    }
};
