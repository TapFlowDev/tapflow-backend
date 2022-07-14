<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class notification_settings extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    public $timestamps = false;
    protected $fillable=
    [
        "user_id",
        "notification_type",//1=>chat 2=>projects 3=>recommendations 4=>payment
        "email",
        "notification",
    ];
}
