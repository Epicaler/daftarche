<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->string('direction', 10); // receivable (طلب) | payable (بدهی)
            $table->unsignedBigInteger('amount');
            $table->string('title')->nullable();
            $table->date('occurred_on');
            $table->date('due_on')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();

            $table->index(['direction', 'settled_at']);
        });

        Schema::create('debt_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('amount');
            $table->date('paid_on');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_payments');
        Schema::dropIfExists('debts');
    }
};
