<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'jabatan_id',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function getRoleNameAttribute()
    {
        return $this->role->role_name;
    }


    public function managerBidang(){
        return $this->hasOne("\App\Models\UserManagerBidang","user_id","id");
    }

    public function getBidangAttribute(){
        return $this->managerBidang->bidang;
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Jabatan');
    }


    public function fcmToken(){
        return $this->hasMany('App\Models\FcmToken');
    }

    public function notification(){
        return $this->hasMany('App\Models\Notification');
    }
}
