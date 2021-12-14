<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class project_transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'milestone_id',
        'price',
        'after_amount',
    ];
  
}
