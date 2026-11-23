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
        Schema::create('media_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('media_id');
            $table->unsignedBigInteger('media_tags_id');
            $table->timestamps();

            $table->unique(['media_id', 'media_tags_id']);
            $table->foreign('media_id')->references('id')->on('media')->cascadeOnDelete();
            $table->foreign('media_tags_id')->references('id')->on('media_tags')->cascadeOnDelete();

            $table->index('media_id');
            $table->index('media_tags_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_tag');
    }
};
