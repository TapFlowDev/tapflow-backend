<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    /*
    type 1 => team(agency or team of freelancers)
    type 2 => company
    */
    use HasFactory;
    protected $fillable = [
        'name',
        'admin_id',
        'type'
    ];
}
