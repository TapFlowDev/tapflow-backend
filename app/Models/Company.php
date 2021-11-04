<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $table = 'companies';
    protected $fillable = [
        'group_id',
        'bio',
        'image',
        'attachment',
        'link',
        'country',
        'employees_number',
        'field'
    ];
}
