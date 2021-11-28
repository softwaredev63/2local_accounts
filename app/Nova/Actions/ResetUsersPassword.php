<?php

namespace App\Nova\Actions;

use App\Exports\ResetPasswordFromNovaResponse;
use App\Imports\ResetPasswordFromNova;
use App\Services\PasswordService;
use App\Services\UserWalletService;
use Brightspot\Nova\Tools\DetachedActions\DetachedAction;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Actions\Actionable;
use Illuminate\Notifications\Notifiable;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Password;
use Maatwebsite\Excel\Facades\Excel;
use Laravel\Fortify\Rules\Password as PasswordRule;

/**
 * Class ResetUsersPassword
 * @package App\Nova\Actions
 */
class ResetUsersPassword extends DetachedAction
{
    use InteractsWithQueue, Queueable;
    use Actionable, Notifiable;

    /**
     * @var UserWalletService
     */
    protected $userWalletService;

    /**
     * @var Request
     */
    protected $request;

    /**
     * BalanceController constructor.
     * @param UserWalletService $userWalletService
     * @param Request $request
     */
    public function __construct(UserWalletService $userWalletService, Request $request)
    {
        $this->userWalletService = $userWalletService;
        $this->request = $request;
    }
    /**
     * Perform the action on the given models.
     *
     * @param ActionFields $fields
     * @param Collection $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        try {
            $file = $fields->file;
            $newPassword = $fields->password;
            $now = date('Y-m-d-H-i');
            $fileName = "reset_users_password_{$now}.csv";
            if(!$newPassword || !$file) {
                throw new Exception('All fields are required!');
            }
            $ResetPasswordFromNova = new ResetPasswordFromNova($newPassword, new PasswordService());
            ($ResetPasswordFromNova)->import($file);

            Excel::store(new ResetPasswordFromNovaResponse($ResetPasswordFromNova->getImportResult()), "tmp/{$fileName}", 'public');

            return Action::download(route('download-file-nova', ["fileName" => $fileName]), $fileName);
        } catch (Exception $exception) {
            Action::danger($exception->getMessage());
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [

            Password::make('Password', 'password')
                ->rules('required', 'string', new PasswordRule),

            File::make('Users list (.csv)', 'file')
            ->rules('required'),
        ];
    }
}
