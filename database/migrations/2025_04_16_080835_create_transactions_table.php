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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('promo_id')->nullable()->constrained('promos')->cascadeOnDelete()->nullable();
            $table->string('booking_transaction_id');
            $table->unsignedInteger('grand_total')->nullable();
            $table->unsignedInteger('down_payment')->nullable();
            $table->unsignedInteger('remaining_payment')->nullable();
            $table->enum('booking_status', ['pending', 'paid', 'rented', 'finished', 'cancelled'])->default('pending');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->unsignedInteger('duration');
            $table->text('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
