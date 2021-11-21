<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Final_proposal extends Model
{
    use HasFactory;
    protected $fillable =
    [
        "propsal_id",
        "team_id",
        "project_id",
        "price_min",
        "price_max",
        "description",
        "days",
        "status",

    ];
}
