<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('address')->nullable();
            $table->string('speed')->nullable();
            $table->string('accuracy')->nullable();
            $table->timestamp('tracked_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_tracks');
    }
}; 