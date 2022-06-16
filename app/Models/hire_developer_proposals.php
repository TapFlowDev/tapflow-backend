<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hire_developer_proposals extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'team_id',
        'user_id',
        'details',
        'status',
        'deleted',
    ];
}
