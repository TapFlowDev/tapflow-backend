<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class Requirement extends Model
{
  
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $fillable=
    [
        "project_id",
        "user_id",
        "description"
    ];
}
