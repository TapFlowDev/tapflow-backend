<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing_info extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_id',
        'bank_name',
        'IBAN',
        'account_number',
        'country'
    ];
}
