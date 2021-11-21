<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class Freelancer extends Model
{
    
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'user_id',
        'bio',
        'hourly_rate',
        'experience',
        'image',
        'country',
        'tools',
    ];
}
