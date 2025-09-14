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
            $table->dropForeign('debt_request_id_debt_foreign');  
            $table->dropColumn('id_debt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debt_request', function (Blueprint $table) {
            $table->integer('id_debt')->nullable();
            $table->foreign('id_debt')->references('id')->on('debt_records')->onDelete('set null');
        });
    }
};
