<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Company extends Model
{
   
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'companies';
    protected $fillable = [
        'group_id',
        'bio',
        'image',
        'link',
        'country',
        'employees_number',
        'field',//targets (Early-Stage Startups) category type =>3
        'sector',//(Crypto / Blockchain ) category type =>4
    ];
}
