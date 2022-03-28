<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class wallet extends Model
{
    use HasFactory;
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'reference_id',
        'balance',
        'type',// type = 1 => group, type = 2 => freelancer
    ];
}
