<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class proposal extends Model
{
    
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'team_id',
        'project_id',
        'price',
        'days',
        'why_us',
    ];
}
