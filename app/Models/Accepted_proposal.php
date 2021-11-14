<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
class Accepted_proposal extends Model
{
    use HasFactory ,HasApiTokens ;

    protected $fillable=
    [
        
        "team_id",
        "project_id",
        "proposal_id",
        "price",
        "days",
        
    ];
}
      