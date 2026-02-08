<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('name');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->string('blood_group')->nullable()->after('date_of_birth');
            $table->string('designation')->nullable()->after('position');
            $table->foreignId('shift_id')->nullable()->after('department')->constrained('shifts')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->after('shift_id')->constrained('departments')->nullOnDelete();
            $table->string('citizenship_number')->nullable()->after('address');
            $table->string('pan_number')->nullable()->after('citizenship_number');
            $table->string('photo')->nullable()->after('pan_number');
            $table->string('citizenship_front')->nullable()->after('photo');
            $table->string('citizenship_back')->nullable()->after('citizenship_front');
            $table->string('pan_card_image')->nullable()->after('citizenship_back');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn([
                'gender', 'date_of_birth', 'blood_group', 'designation',
                'shift_id', 'department_id', 'citizenship_number', 'pan_number',
                'photo', 'citizenship_front', 'citizenship_back', 'pan_card_image',
            ]);
        });
    }
};
