<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class withdrawl_request extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'group_id',
        'billing_info_id',
        'wallet_transactiond_id',
        'amount',
        'invoice',
        'type',// 1 => manual transfer
        'status',// 0 => pending, 1 => success, 2 => fail or denied 
    ];
}
