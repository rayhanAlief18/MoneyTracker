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
            $table->foreignId('money_placing_id')->constrained('money_placing')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debt_records', function (Blueprint $table) {
            $table->dropForeign(['money_placing_id']);
            $table->dropColumn('money_placing_id');
        });
    }
};
