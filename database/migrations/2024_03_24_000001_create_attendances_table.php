<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('check_in_time');
            $table->time('check_out_time')->nullable();
            $table->string('check_in_location')->nullable();
            $table->string('check_out_location')->nullable();
            $table->string('status')->default('absent'); // present, late, absent
            $table->text('check_in_photo')->nullable();
            $table->text('check_out_photo')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
}; 