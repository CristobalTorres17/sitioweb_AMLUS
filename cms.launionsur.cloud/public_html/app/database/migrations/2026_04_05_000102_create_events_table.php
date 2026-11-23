<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();

            // Event metadata
            $table->dateTime('event_date');
            $table->dateTime('event_date_end')->nullable();
            $table->string('location')->nullable();
            $table->string('organizer')->nullable();

            // Featured image
            $table->foreignId('featured_image_id')->nullable()->constrained('media')->nullOnDelete();

            // Status
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();

            // Author
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();

            // SEO & Visibility
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_featured')->default(false);

            // Metadata
            $table->json('metadata')->nullable(); // Attendee count, location link, etc.

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('event_date');
            $table->index('is_featured');
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};
