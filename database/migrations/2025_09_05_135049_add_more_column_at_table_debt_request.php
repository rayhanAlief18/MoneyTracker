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
        Schema::table('debt_request', function (Blueprint $table) {

            $table->string('jenis_hutang')->after('creditor_user_id');
            $table->string('keterangan')->after('jenis_hutang');
            $table->float('amount')->after('keterangan');
            $table->enum('status', ['Pending', 'Diterima (Belum Bayar)', 'Ditolak', 'Lunas'])->default('Pending')->after('amount');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debt_request', function (Blueprint $table) {
            $table->dropColumn(['jenis_hutang', 'keterangan', 'status', 'amount']);
        });
    }
};
