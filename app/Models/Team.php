<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    /*
    type 0 => agency
    type 1 => team of freelancers
    */
    use HasFactory;
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
