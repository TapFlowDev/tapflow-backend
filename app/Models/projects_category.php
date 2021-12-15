<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class projects_category extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'category_id',
        'sub_category_id'
    ];
    public $timestamps = false;
}
