<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class groups_category extends Model
{
    
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'group_id',
        'category_id',
        'sub_category_id'
    ];
}
