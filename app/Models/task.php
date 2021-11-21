<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class Task extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'milestone_id',
        'name',
        'description',
        'status',
    ];
}
