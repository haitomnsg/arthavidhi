<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->integer('month'); // 1-12
            $table->integer('year');
            $table->decimal('basic_salary', 12, 2);
            $table->decimal('bonus', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->string('deduction_reason')->nullable();
            $table->decimal('advance_deduction', 12, 2)->default(0);
            $table->decimal('ssf_employee', 12, 2)->default(0); // SSF employee contribution (1%)
            $table->decimal('ssf_employer', 12, 2)->default(0); // SSF employer contribution (2%)
            $table->decimal('tds', 12, 2)->default(0); // Tax Deduction at Source
            $table->decimal('net_salary', 12, 2);
            $table->date('payment_date')->nullable();
            $table->string('payment_method')->nullable(); // cash, bank_transfer, cheque
            $table->string('payment_reference')->nullable(); // cheque number, transaction id
            $table->enum('status', ['pending', 'paid', 'hold'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
