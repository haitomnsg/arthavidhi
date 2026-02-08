<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            if (!Schema::hasColumn('bills', 'status')) {
                $table->string('status')->default('active')->after('payment_status'); // active, cancelled
            }
            if (!Schema::hasColumn('bills', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('bills', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn(['status', 'cancellation_reason', 'cancelled_at']);
        });
    }
};
