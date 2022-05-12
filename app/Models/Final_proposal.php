<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Final_proposal extends Model
{
    use HasFactory;
    protected $fillable =
    [
        "proposal_id",
        "team_id",
        "project_id",
        "price",
        "description",
        "hours",
        // "hourly_rate",
        "status",//1 => sent, 2=> rejected, 3=>accepted 
        "title",
        "starting_date",
        // "down_payment",//value 0=>no down payment 1=>there is down payment
        // "down_payment_value",//value 0=>no down payment 1=>there is down payment
        "user_id",
        "type"

    ];
}
