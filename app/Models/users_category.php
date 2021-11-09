<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class users_category extends Model
{
   
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'user_id',
        'category_id',
        'sub_category_id'
    ];
}
