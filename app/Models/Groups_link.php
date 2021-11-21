<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groups_link extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_id',
        'link'
    ];
    public $timestamps = false;
}
