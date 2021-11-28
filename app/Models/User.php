<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'api_token',
        'has_changed_password',
        'locked',
        'l2l',
        'affiliate_by',
        'affiliate_code',
        'google_id',
        'email_verified_at',
        'user_type',
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

    /**
     * Checks if the user has wallet
     *
     * @return bool
     */
    public function hasWallet()
    {
        return $this->hasOne(UserWallet::class)->first() ? true : false;
    }

    public function wallet()
    {
        return $this->hasOne(UserWallet::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function extraWallet()
    {
        return $this->hasOne(UserExtraWallet::class);
    }

    public function affiliateBy()
    {
        return $this->belongsTo(__CLASS__, 'affiliate_by');
    }

    public function affiliates()
    {
        return $this->hasMany(__CLASS__, 'affiliate_by');
    }
}
