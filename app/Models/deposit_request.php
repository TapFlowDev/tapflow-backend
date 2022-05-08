<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class deposit_request extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'reference_number',
        'amount',
        'status',
    ];
}
