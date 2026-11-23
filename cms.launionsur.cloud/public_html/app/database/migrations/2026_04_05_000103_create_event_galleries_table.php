<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('event_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('media_id')->constrained()->cascadeOnDelete();

            // Gallery metadata
            $table->integer('position')->default(0);
            $table->string('caption')->nullable();

            $table->timestamps();

            // Ensure unique position per event
            $table->unique(['event_id', 'media_id']);
            $table->index(['event_id', 'position']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_galleries');
    }
};
