<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Countries extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'countries';
    protected $fillable = [
        'name',
        'code',
        'flag',
        'score',
    ];
    public $timestamps = false;
}
