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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Model information
            $table->string('model_type')->nullable()->index(); // e.g., 'Service', 'User', 'Page'
            $table->unsignedBigInteger('model_id')->nullable()->index(); // ID of the audited model

            // Action and data
            $table->string('action')->index(); // 'create', 'update', 'delete', 'restore'
            $table->json('old_values')->nullable(); // Previous values (for update/delete)
            $table->json('new_values')->nullable(); // New values (for create/update)

            // User and IP information
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address')->nullable();

            // Metadata
            $table->boolean('is_sensitive')->default(false)->index(); // Flag for role/password changes
            $table->boolean('is_restoreable')->default(true); // Can this be restored?
            $table->text('reason')->nullable(); // Optional reason for the change or restoration

            // Timestamps
            $table->timestamp('created_at')->useCurrent()->index();

            // Composite index for fast queries
            $table->index(['model_type', 'model_id']); // Query by model
            $table->index(['user_id', 'created_at']); // Query by user activity timeline
            $table->index(['action', 'created_at']); // Query by action type

            // Foreign keys
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
