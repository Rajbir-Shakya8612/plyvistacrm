<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('pincode')->nullable();
            $table->string('status')->default('new'); // new, follow_up, confirmed, lost, shared
            $table->text('notes')->nullable();
            $table->decimal('expected_amount', 10, 2)->nullable();
            $table->date('follow_up_date')->nullable();
            $table->string('source')->nullable();
            $table->string('location')->nullable();
            $table->json('additional_info')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
}; 