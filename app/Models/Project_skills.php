<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project_skills extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'skill_id'
    ];
    public $timestamps = false;

}
