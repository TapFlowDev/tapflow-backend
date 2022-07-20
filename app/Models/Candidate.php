<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;
    public $fillable = [
        'proposal_id',
        'agency_resource_id',
        'status', // 0=>new, 1=>accepted, 2=>rejected, 3=>interview
        'hourly_rate',
    ];
}
