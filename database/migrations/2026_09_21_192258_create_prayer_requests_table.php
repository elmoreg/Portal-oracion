<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prayer_requests', function (Blueprint $table) {
            $table->id();
            $table->string('public_token', 64)->unique();
            $table->string('requester_name')->nullable();
            $table->text('content');
            $table->string('status')->default('pending');
            $table->string('ip_address', 45)->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('country_name')->nullable();
            $table->boolean('is_answered')->default(false);
            $table->timestamp('answered_at')->nullable();
            $table->text('answer_note')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('country_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prayer_requests');
    }
};
