<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prayer_requests', function (Blueprint $table) {
            $table->unsignedInteger('prayed_count')->default(0)->after('is_public');
        });
    }

    public function down(): void
    {
        Schema::table('prayer_requests', function (Blueprint $table) {
            $table->dropColumn('prayed_count');
        });
    }
};
