<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Contact information
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();

            // Profile information
            $table->text('bio')->nullable();
            $table->string('position')->nullable(); // e.g., "Administrator", "Editor"
            $table->string('department')->nullable(); // e.g., "Comunicaciones"

            // Media
            $table->foreignId('avatar_id')->nullable()->constrained('media')->nullOnDelete();

            // Preferences
            $table->json('preferences')->nullable(); // User settings, theme, etc.

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
};
