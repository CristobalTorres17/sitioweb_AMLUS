<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_collections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable()->index();

            // Tree structure — self-referential FK
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->foreign('parent_id')
                ->references('id')
                ->on('media_collections')
                ->nullOnDelete();

            // Optional polymorphic link to the entity this folder "belongs" to.
            // e.g. collectable_type = 'App\Models\Event', collectable_id = 3
            // This lets us auto-create sub-folders when an Event/Article is saved.
            $table->nullableMorphs('collectable');

            $table->text('description')->nullable();
            $table->string('color', 7)->default('#6366f1');
            $table->string('icon', 50)->default('folder');
            $table->unsignedInteger('sort_order')->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_collections');
    }
};
