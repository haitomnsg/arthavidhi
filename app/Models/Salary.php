<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'employee_id',
        'month',
        'year',
        'basic_salary',
        'bonus',
        'deductions',
        'deduction_reason',
        'advance_deduction',
        'ssf_employee',
        'ssf_employer',
        'tds',
        'net_salary',
        'payment_date',
        'payment_method',
        'payment_reference',
        'status',
        'notes',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deductions' => 'decimal:2',
        'advance_deduction' => 'decimal:2',
        'ssf_employee' => 'decimal:2',
        'ssf_employer' => 'decimal:2',
        'tds' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date',
        'month' => 'integer',
        'year' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the month name.
     */
    public function getMonthNameAttribute(): string
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }

    /**
     * Get gross salary (basic + bonus).
     */
    public function getGrossSalaryAttribute(): float
    {
        return $this->basic_salary + $this->bonus;
    }

    /**
     * Get total deductions.
     */
    public function getTotalDeductionsAttribute(): float
    {
        return $this->deductions + $this->advance_deduction + $this->ssf_employee + $this->tds;
    }
}
