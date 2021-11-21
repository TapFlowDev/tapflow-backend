<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groups_attachment extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_id',
        'attachment'
    ];
    public $timestamps = false;
}
