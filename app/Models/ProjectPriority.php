<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectPriority extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'priority_id',
        'sort'
    ];
    public $timestamps = false;
}
