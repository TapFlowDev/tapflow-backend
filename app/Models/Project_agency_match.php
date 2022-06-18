<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project_agency_match extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_id',
        'project_id'
    ];
    public $timestamps = false;
}
