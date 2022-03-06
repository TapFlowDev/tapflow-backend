<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form_options extends Model
{
    use HasFactory;
    protected $fillable = [
        'label',
        'field_type',
        'required',
        'type'
    ];
}
