<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class wallets_transaction extends Model
{
    use HasFactory;
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'amount',
        'wallet_id',
        'type',//1=>deposit 2=>withdraw
    ];
}
