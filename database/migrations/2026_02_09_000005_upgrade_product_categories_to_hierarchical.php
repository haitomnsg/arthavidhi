<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            // Self-referencing foreign key — THIS is the magic that enables unlimited nesting
            $table->foreignId('parent_id')->nullable()->after('company_id')
                ->constrained('product_categories')->onDelete('cascade');

            // URL-friendly slug (auto-generated from name)
            $table->string('slug')->nullable()->after('name');

            // Optional description for the category
            $table->text('description')->nullable()->after('slug');

            // Category image/icon
            $table->string('image')->nullable()->after('description');

            // Enable/disable category
            $table->boolean('is_active')->default(true)->after('image');

            // Control display order among siblings
            $table->integer('sort_order')->default(0)->after('is_active');

            // Depth level in tree (0 = root, 1 = first child, etc.)
            // This is denormalized for performance — avoids recursive queries for depth
            $table->integer('level')->default(0)->after('sort_order');

            // Full path of ancestor IDs (e.g., "1/5/12") — for fast ancestor/descendant queries
            $table->string('path')->nullable()->after('level');

            // Indexes for performance
            $table->index('parent_id');
            $table->index('slug');
            $table->index('is_active');
            $table->index('sort_order');
            $table->index('level');
        });
    }

    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'parent_id', 'slug', 'description', 'image',
                'is_active', 'sort_order', 'level', 'path',
            ]);
        });
    }
};
