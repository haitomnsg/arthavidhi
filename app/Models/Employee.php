<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'employee_id',
        'name',
        'gender',
        'date_of_birth',
        'blood_group',
        'email',
        'phone',
        'address',
        'citizenship_number',
        'pan_number',
        'photo',
        'citizenship_front',
        'citizenship_back',
        'pan_card_image',
        'position',
        'designation',
        'department',
        'shift_id',
        'department_id',
        'salary',
        'hire_date',
        'joining_date',
        'is_active',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'joining_date' => 'date',
        'date_of_birth' => 'date',
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function departmentModel()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function salaryAdvances()
    {
        return $this->hasMany(SalaryAdvance::class);
    }
}
