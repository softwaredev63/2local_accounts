<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserExtraWallet extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_extra_wallets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'xlm_private_key',
        'xlm_public_key',
        'btc_private_key',
        'btc_public_key',
        'eth_private_key',
        'eth_public_key',
        'balance_l2l',
        'balance_xlm'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Returns the wallet user
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
