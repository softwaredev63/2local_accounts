<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class UserWalletNova
 * @package App\Models
 * @author Bojte Szabolcs
 */
class UserWallet extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_wallets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'address',
        'private_key',
        'double_encrypted',
        'iv',
        'token_quantity',
        'token_sent',
        'locked',
        'transaction_hash',
        'balance_2lc',
        'phrases_created_at',
        'phrases_updated_at',
        'balance_bnb',
        'balance_locked_2lc'
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
