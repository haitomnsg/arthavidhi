<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'title')) {
                $table->string('title')->after('company_id')->default('');
            }
            if (!Schema::hasColumn('expenses', 'receipt')) {
                $table->string('receipt')->nullable()->after('reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['title', 'receipt']);
        });
    }
};
