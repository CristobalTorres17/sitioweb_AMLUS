<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Migration intentionally left as a no-op to preserve `image_path`
        // for legacy tests and API expectations. If the project intends
        // to remove `image_path` permanently, perform a coordinated
        // migration and update tests/controllers accordingly.
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            // Add the column back if rolling back
            $table->string('image_path')->nullable();
        });
    }
};
