<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class resources extends Model
{
    use HasFactory;
    protected $fillable = [
        'contract_id',
        'name',
        'job_function',
        'duration',
        'hours',
        'rate',
        'starting_date',
        'duration_in_days',
        'image',
        'user_id',
        'status',
        'deleted',
    ];
}
