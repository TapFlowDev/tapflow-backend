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
        "requirements_description",
        "days",
        "deleted",
        "status"
    ];
}
