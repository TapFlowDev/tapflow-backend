<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable=
    [
        "user_id",
        "company_id",
        "bio",
        "attachment",
        "image",
        "country",
        "role",
        "experience",
    ];
}
