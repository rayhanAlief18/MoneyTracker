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
        Schema::create('debt_records', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2);
            $table->string('nama_pemberi_hutang');
            $table->string('keterangan')->nullable();
            $table->date('tanggal_hutang');
            $table->date('tanggal_rencana_bayar')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debt_records');
        // --- IGNORE ---
    }
};
