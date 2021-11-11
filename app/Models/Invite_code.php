<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invite_code extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'link_token',
        'user_id',
        'group_id',
        'expired',
        'email',
        'status'
    ];
}
