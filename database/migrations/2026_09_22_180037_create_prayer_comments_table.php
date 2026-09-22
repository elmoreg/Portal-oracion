<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prayer_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prayer_request_id')->constrained()->cascadeOnDelete();
            $table->string('author_name')->nullable();
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prayer_comments');
    }
};
