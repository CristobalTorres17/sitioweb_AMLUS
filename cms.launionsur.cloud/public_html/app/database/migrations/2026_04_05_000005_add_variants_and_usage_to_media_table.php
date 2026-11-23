<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            if (!Schema::hasColumn('media', 'variants')) {
                $table->json('variants')->nullable()->after('path');
            }
            if (!Schema::hasColumn('media', 'usage_count')) {
                $table->integer('usage_count')->default(0)->after('is_active');
            }
        });
    }

    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            if (Schema::hasColumn('media', 'variants')) {
                $table->dropColumn('variants');
            }
            if (Schema::hasColumn('media', 'usage_count')) {
                $table->dropColumn('usage_count');
            }
        });
    }
};
