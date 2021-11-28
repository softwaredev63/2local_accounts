<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\DestructiveAction;
use Laravel\Nova\Fields\ActionFields;
use Log;

/**
 * Class DeleteUsers
 * @package App\Nova\Actions
 */
class DeleteUsers extends DestructiveAction
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param ActionFields $fields
     * @param Collection $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $user) {
            Log::info('User wallet deleted. ID: ' . $user->wallet->id . ', Address:' . $user->wallet->address);
            $user->wallet->delete();
            Log::info('User deleted. ID: ' . $user->id . ', Email:' . $user->email);
            $user->delete();
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

    /**
     * @return string
     */
    public function name()
    {
        return "Delete users";
    }

    public function delete()
    {
        return true;
    }
}
