<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Milestone extends Model
{
   
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable=
    [
        "project_id",
        "final_proposal_id",
        "name",
        "description",
        "hours",
        "price",
        "status",//1 => submitted, 2=> rejected, 3=>accepted
        "deliverables",
        "is_valid"
    ];
}
