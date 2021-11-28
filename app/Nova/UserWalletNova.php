<?php

namespace App\Nova;

use App\Models\UserWallet;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Carlson\NovaLinkField\Link;

class UserWalletNova extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = UserWallet::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'User';

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['user'];

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'user_id', 'address', 'token_quantity', 'balance_2lc','token_sent', 'locked', 'transaction_hash'
    ];

    public static function label()
    {
        return 'Users Wallet';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('User','user', 'App\Nova\UserNova')
                ->sortable(),

            Link::make('Address', 'address')
                ->sortable()
                ->details([
                    'href' => function() {
                        return "https://bscscan.com/address/{$this->address}";
                    },
                    'text' => function () {
                        return $this->address;
                    },
                    'newTab' => true,
                ]),

            Number::make('Original 2LC quantity','token_quantity')
                ->sortable(),

            Number::make('2LC balance','balance_2lc')
                ->sortable(),

            Number::make('Locked 2LC balance','balance_locked_2lc')
                ->sortable(),

            Boolean::make('Token sent', 'token_sent')
                ->sortable(),

            Boolean::make('Locked', 'locked')
                ->sortable(),

            Text::make('Transaction hash', 'transaction_hash')
                ->sortable(),
        ];
    }

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        return $this->user ? $this->user->name : '';
    }
}
