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
        Schema::create('debt_request', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debtor_user_id')->constrained('users');
            $table->foreignId('creditor_user_id')->constrained('users');
            $table->date('debt_date');
            $table->date('due_date');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debt_request', function (Blueprint $table) {
            $table->dropForeign(['debtor_user_id']);
            $table->dropForeign(['creditor_user_id']);
            $table->dropColumn(['id', 'debtor_user_id', 'creditor_user_id', 'amount', 'description', 'due_date', 'status', 'created_at', 'updated_at']);
        });
        
    }
};
