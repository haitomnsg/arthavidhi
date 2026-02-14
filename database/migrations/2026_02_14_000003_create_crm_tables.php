<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->string('designation')->nullable();
            $table->text('address')->nullable();
            $table->string('type')->default('lead'); // lead, prospect, customer, partner
            $table->string('source')->nullable(); // website, referral, social, direct, other
            $table->string('status')->default('active'); // active, inactive
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('crm_deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('crm_contact_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->decimal('value', 12, 2)->default(0);
            $table->string('stage')->default('lead'); // lead, qualified, proposal, negotiation, won, lost
            $table->string('priority')->default('medium'); // low, medium, high
            $table->date('expected_close_date')->nullable();
            $table->date('closed_date')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('crm_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('crm_contact_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->string('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('crm_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('crm_contact_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_tasks');
        Schema::dropIfExists('crm_notes');
        Schema::dropIfExists('crm_deals');
        Schema::dropIfExists('crm_contacts');
    }
};
