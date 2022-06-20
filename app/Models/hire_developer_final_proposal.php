<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hire_developer_final_proposal extends Model
{
    use HasFactory;
    protected $fillable = [
        'proposal_id',
        'team_id',
        'user_id',
        'starting_date',
        'notice_period',
        'resource_replacment',
        'trail_period',
        'payment_settlement',
        'additional_terms',
        'default_terms',
        'status',
        'deleted',
        'type',
    ];
}
