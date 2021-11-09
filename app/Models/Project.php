<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable=
    [
        "company_id",
        "team_id",
        "name",
        "description",
        "budget",
        "days",
        "hide_requirements",
        "deleted"
    ];
}
