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
        Schema::table('financial_plan_progress', function (Blueprint $table) {
            $table->foreignId('money_placing_id')->references('id')->on('money_placing') ->onDelete('cascade')
            ->after('id_financial_plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_plan_progress', function (Blueprint $table) {
            $table->dropForeign(['money_placing_id']);
            $table->dropColumn('money_placing_id');
        });
    }
};
