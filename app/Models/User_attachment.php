<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_attachment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'attachment'
    ];
    public $timestamps = false;
}
