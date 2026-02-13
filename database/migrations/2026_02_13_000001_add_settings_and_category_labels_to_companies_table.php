<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->json('settings')->nullable()->after('logo');
            $table->json('category_level_labels')->nullable()->after('settings');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['settings', 'category_level_labels']);
        });
    }
};
