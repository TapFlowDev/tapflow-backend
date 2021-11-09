<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class Team extends Model
{
    /*
    type 0 => agency
    type 1 => team of freelancers
    */
   
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'group_id',
        'bio',
        'image',
        'attachment',
        'type',
        'link',
        'country',
        'employees_number'
    ];
}
