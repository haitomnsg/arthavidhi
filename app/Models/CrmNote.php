<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'crm_contact_id', 'content', 'created_by',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function contact()
    {
        return $this->belongsTo(CrmContact::class, 'crm_contact_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
