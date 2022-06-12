<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class System_Notification extends Model
{
    use  HasFactory, Notifiable;
    protected $fillable = [
        'title',
        'body',
        'receiver_id',
        'action',
        'action_id',
        'link'
    ];


}
