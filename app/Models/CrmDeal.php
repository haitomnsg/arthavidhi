<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmDeal extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'crm_contact_id', 'title', 'value', 'stage',
        'priority', 'expected_close_date', 'closed_date', 'description',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'expected_close_date' => 'date',
        'closed_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function contact()
    {
        return $this->belongsTo(CrmContact::class, 'crm_contact_id');
    }
}
