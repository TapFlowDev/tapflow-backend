<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable=
    [
        "user_id",
        "company_id",
        "team_id",
        "name",
        "budget_type",
        "min",
        "max",
        "description",
        "days",
        "deleted",
        "status",
        'BA',
        'design',
        'type', // 0,1 project based, 2 monthly, 3 hire developers
        'budget_id',
        'interview',
        'start_project',
        'verified',
        'visible',
    ];
}
