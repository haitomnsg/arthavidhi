<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'name', 'email', 'phone', 'company_name',
        'designation', 'address', 'type', 'source', 'status', 'notes',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function deals()
    {
        return $this->hasMany(CrmDeal::class);
    }

    public function crmNotes()
    {
        return $this->hasMany(CrmNote::class);
    }

    public function tasks()
    {
        return $this->hasMany(CrmTask::class);
    }
}
