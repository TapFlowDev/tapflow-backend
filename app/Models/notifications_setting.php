<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notifications_setting extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'email_subject',
        'email_text',
        'email_template',
        'notification_title',
        'notification_text',
        'has_group_name',
    ];
}
