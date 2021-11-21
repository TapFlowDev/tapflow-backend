<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group_member extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_id',
        'user_id',
        'privileges' // 1 => creator or admin , 2 => member
    ];
    public $timestamps = false;

}
