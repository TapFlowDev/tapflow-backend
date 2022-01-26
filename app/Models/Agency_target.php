<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency_target extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_id',
        'category_id'
    ];
}
