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
            $table->foreignId('id_debt')
                ->references('id')
                ->on('debt_records')
                ->onDelete('cascade')
                ->after('id'); // Assuming 'status' is the last column in the table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debt_request', function (Blueprint $table) {
            $table->dropForeign(['id_debt']);
            // Optionally, you can drop the column if needed
            // $table->dropColumn('id_debt');
        });
    }
};
