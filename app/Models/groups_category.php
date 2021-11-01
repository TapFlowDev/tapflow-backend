<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class groups_category extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_id',
        'category_id',
        'sub_category_id'
    ];
}
