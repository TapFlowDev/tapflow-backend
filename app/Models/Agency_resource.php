<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency_resource extends Model
{
    use HasFactory;
    public $fillable = [
        'team_id',
        'user_id',
        'name',
        'seniority',
        'country',
        'hourly_rate',
        'cv',
    ];
}
