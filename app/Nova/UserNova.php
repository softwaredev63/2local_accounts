<?php

namespace App\Nova;

use App\Models\User;
use App\Nova\Actions\DownloadActiveUsers;
use App\Nova\Actions\LockUsers;
use App\Nova\Actions\RemoveUser2FA;
use App\Nova\Actions\ResetUsersPassword;
use App\Nova\Actions\ResetUsersSecretPhrase;
use App\Nova\Actions\UnlockUsers;
use App\Nova\Actions\DeleteUsers;
use App\Services\SecretPhraseService;
use App\Services\UserWalletService;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class UserNova extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'User';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'email', 'email_verified_at'
    ];

    /**
     * @return string
     */
    public static function label()
    {
        return 'Users';
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

            Text::make('Name', 'name')
                ->sortable(),

            Text::make('Email', 'email')
                ->sortable(),

//            Password::make('Password', 'password')
//                ->onlyOnForms(),

            HasOne::make('UserWalletNova','wallet')
                ->sortable(),

            Boolean::make('Locked', 'locked')
                ->sortable(),

            Boolean::make('Has changed password', 'has_changed_password')
                ->sortable(),

            Boolean::make('2FA Activated', 'two_factor_secret', function() {
                return !!$this->two_factor_secret;
            })
                ->sortable(),

            Date::make('Email verified at', 'email_verified_at')
                ->sortable()
                ->format('YYYY-MM-DD'),

            Number::make('Original 2LC quantity','l2l')
                ->sortable(),

            Boolean::make('Temporary secret phrase', 'is_temporary_secret_phrase')
                ->sortable(),

            BelongsTo::make('Affiliated by', 'affiliateBy', __CLASS__)
                ->hideFromIndex(),

            HasMany::make('Affiliated users', 'affiliates', __CLASS__),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        $userWalletService = new UserWalletService();
        $secretPhraseService = new SecretPhraseService();
        return [
            new RemoveUser2FA,
            new LockUsers,
            new UnlockUsers,
            new DownloadActiveUsers($userWalletService, $request),
            new ResetUsersPassword($userWalletService, $request),
            new ResetUsersSecretPhrase($userWalletService, $secretPhraseService),
            new DeleteUsers,
        ];
    }

    /**
     * It is needed for bulk actions (use update model)
     *
     * @param Request $request
     * @return bool
     */
    public function authorizedToUpdate(Request $request)
    {
        return "nova-api/{resource}/action" === $request->route()->uri();
    }
}
