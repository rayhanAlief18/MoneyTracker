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
        Schema::create('financial_plan_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_financial_plan')->constrained('financial_plans')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->decimal('presentase_progress', 15, 2);
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_plan_progress');
        // --- IGNORE ---
    }
};
