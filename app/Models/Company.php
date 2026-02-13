<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'phone',
        'email',
        'panNumber',
        'vatNumber',
        'logo',
        'settings',
        'category_level_labels',
    ];

    protected $casts = [
        'settings' => 'array',
        'category_level_labels' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
