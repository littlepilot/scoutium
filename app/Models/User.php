<?php

namespace App\Models;

use App\Events\UserSignedUp;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'inviting_user_id');
    }

    public function invitation()
    {
        return $this->hasOne(Invitation::class, 'invited_user_id');
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function defaultWallet()
    {
        return $this->hasOne(Wallet::class)->where('default', true);
    }
}
