<?php

namespace App\Policies;

use App\Models\AdminUser;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class AdminUserPolicy
 * @package App\Policies
 */
class AdminUserPolicy
{
    use HandlesAuthorization;

    public function viewAny(AdminUser $user): bool
    {
        return true;
    }

    public function view(AdminUser $user, $model): bool
    {
        return true;
    }

    public function create(AdminUser $user): bool
    {
        return true;
    }

    public function update(AdminUser $user, $model): bool
    {
        return true;
    }

    public function delete(AdminUser $user, $model): bool
    {
        return $user->id !== $model->id;
    }
}
