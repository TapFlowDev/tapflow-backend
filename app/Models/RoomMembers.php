<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomMembers extends Model
{

    use HasFactory;
    protected  $table='room_members';
    protected $fillable = [
        'room_id',
        'user_id'
    ];
    
}
