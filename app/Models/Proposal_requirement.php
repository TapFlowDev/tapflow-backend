<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal_requirement extends Model
{
    use HasFactory;
    protected $fillable = [
        'proposal_id', // hire developer requirments
        'requirement_id',
        'hourly_rate',
    ];
    public $timestamps = false;
}
