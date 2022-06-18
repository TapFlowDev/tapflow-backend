<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency_service extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_id',
        'category_id'
    ];
    public $timestamps = false;
}
