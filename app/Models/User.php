<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
// use Laravel\Cashier\Billable;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'dob',
        'gender',
        'type',
        'token',
        'name',
        'terms',
        'fcm_token',
        'LBU',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($password){
        $this->attributes['password'] = Hash::make($password);
    }

    public function isAdmin($user)
    {
        // $id = $user['id'];
        // $userData = $this->find($id);
        $adminType = $user['type'];
        if($adminType==0){
            return $user;
        } else{
            return null;
        }
    }
    public function isAgency($user)
    {
        // $id = $user['id'];
        // $userData = $this->find($id);
        $adminType = $user['type'];
        if($adminType==1){
            return $user;
        } else{
            return null;
        }
    }
    public function isClient($user)
    {
        // $id = $user['id'];
        // $userData = $this->find($id);
        $adminType = $user['type'];
        if($adminType==2){
            return $user;
        } else{
            return null;
        }
    }
}
