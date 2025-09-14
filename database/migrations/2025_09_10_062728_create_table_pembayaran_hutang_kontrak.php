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
        Schema::create('debt_request_payment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_request_id')->references('id')->on('debt_request');
            $table->enum('status',['Pembayaran Diajukan','Lunas']);
            $table->string('bukti_bayar')->nullable();
            $table->integer('money_placing_save')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debt_request_payment');
    }
};
