<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class Group extends Model
{
    /*
    type 1 => team(agency or team of freelancers)
    type 2 => company
    */
    
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'type'
    ];
}
