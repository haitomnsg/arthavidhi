<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'crm_contact_id', 'title', 'description',
        'due_date', 'priority', 'status',
    ];

    protected $casts = [
        'due_date' => 'date',
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
