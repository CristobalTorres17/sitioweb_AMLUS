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
        Schema::create('page_versions', function (Blueprint $table) {
            $table->id();

            // Reference
            $table->unsignedBigInteger('page_id');

            // Version metadata
            $table->string('title', 255);
            $table->text('content');
            $table->text('description')->nullable();

            // SEO
            $table->string('meta_description', 160)->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->unsignedBigInteger('og_image_media_id')->nullable();

            // Publishing state at time of version
            $table->boolean('was_published')->default(false);

            // Version tracking
            $table->integer('version_number')->default(1);
            $table->text('change_reason')->nullable();
            $table->string('restored_from')->nullable(); // e.g., "v5" if restored

            // Author
            $table->unsignedBigInteger('created_by')->nullable();

            // Timestamp
            $table->timestamps();

            // Foreign keys
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('og_image_media_id')->references('id')->on('media')->onDelete('set null');

            // Indexes
            $table->index('page_id');
            $table->index('version_number');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_versions');
    }
};
