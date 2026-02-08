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
        'email',
        'phone',
        'address',
        'position',
        'department',
        'salary',
        'hire_date',
        'joining_date',
        'is_active',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'joining_date' => 'date',
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
