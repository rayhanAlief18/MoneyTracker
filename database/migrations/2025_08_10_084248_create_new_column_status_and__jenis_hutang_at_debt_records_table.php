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
        Schema::table('debt_records', function (Blueprint $table) {
            $table->enum('status', ['Belum bayar', 'Lunas','Meminta Persetujuan'])->default('Belum bayar')->after('amount');
            $table->enum('jenis_hutang',['Kontrak','Individu'])->default('Individu')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debt_records', function (Blueprint $table) {
            $table->dropColumn(['status', 'jenis_hutang']);
        });
    }
};
