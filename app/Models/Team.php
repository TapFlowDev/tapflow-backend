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
        'link',
        'country',
        'employees_number',
        'field',
        'BA',
        'design',
        'minPerHour',
        'maxPerHour',
        'min_work_hour',
        'max_work_hour',
        'lead_time',
        'judgment',
        'years_of_experience',
        'response_time',
    ];
}
