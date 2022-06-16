<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency_project_type extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_id',
        'project_type'
    ];
    public $timestamps = false;
}
