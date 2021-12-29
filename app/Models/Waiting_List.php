<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Waiting_List extends Model
{
     
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'waiting__lists';
    protected $fillable = [
        'email',
    ];
}
