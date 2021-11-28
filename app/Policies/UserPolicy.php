<?php

namespace App\Policies;

use App\Models\AdminUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
        return false;
    }

    public function update(AdminUser $user, $model): bool
    {
        return false;
    }

    public function delete(AdminUser $user, $model): bool
    {
        return true;
    }
}
