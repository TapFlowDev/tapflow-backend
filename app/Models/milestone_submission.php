<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class milestone_submission extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable=
    [
        'milestone_id',
        'project_id',
        'links',
        'file',
        'agency_comments',
    ];
}
