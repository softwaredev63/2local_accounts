<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_wallet_id',
        'token_symbol',
        'status',
        'transaction_hash',
        'sender_address',
        'receiver_address',
        'blockchain_data',
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
    public function userWallet()
    {
        return $this->belongsTo(UserWallet::class);
    }
}
