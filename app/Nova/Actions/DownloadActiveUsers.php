<?php

namespace App\Nova\Actions;

use App\Exports\ActiveUsersExport;
use App\Services\UserWalletService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Brightspot\Nova\Tools\DetachedActions\DetachedAction;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class DownloadActiveUsers
 * @package App\Nova\Actions
 */
class DownloadActiveUsers extends DetachedAction
{
    use InteractsWithQueue, Queueable;

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
     * Get the displayable label of the button.
     *
     * @return string
     */
    public function label()
    {
        return __('Download all active users');
    }

    /**
     * Extra CSS classes to apply to detached action button.
     *
     * @var array
     */
    public $extraClasses = [];

    /**
     * The icon type.
     *
     * @var string
     */
    public $icon = '';

    /**
     * {@inheritdoc}
     */
    public function handle(ActionFields $fields)
    {
        try {
            $fileName = sprintf('active_users_%s.xls', date('Y-m-d-H-i'));

            Excel::store(new ActiveUsersExport(new UserWalletService()), "tmp/{$fileName}", 'public');

            return Action::download(route('download-file-nova', ["fileName" => $fileName]), $fileName);
        } catch (Exception $exception) {
            return Action::danger($exception->getMessage());
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [];
    }
}
