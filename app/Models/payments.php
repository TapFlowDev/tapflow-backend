<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payments extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'company_id',
        'agency_id',
        'project_id',
        'milestone_id',
        'milestone_price',
        'tapflow_fee',
        'total_price',
        'agency_total_price',
        'status',// 0 faild, 1 success withdraw from client, 2 success deposit for agency 
        'type',
    ];
}
