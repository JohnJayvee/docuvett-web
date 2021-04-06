<?php

namespace App\Models\User\Observers;

use App\Events\UserRelatedSettingIsUpdated;
use App\Models\Role\Role;
use App\Models\User\Queries\UserQueries;
use App\Models\User\User;

class UserObserver
{
    /**
     * Listen to the User created event.
     *
     * @param User $user
     *
     * @return void
     */
    public function created(User $user)
    {
        if ($user && !empty($user['id'])) {
            $rawRoles = config('laratrust_seeder.role_structure');
            $roles = array_keys($rawRoles);
            $role = Role::whereName(in_array('customer', $roles) ? 'customer' : $roles[0])->first();

            if($role) {
                $user->syncRoles([$role->id]);
            }
        }
    }

    /**
     * Listen to the User updated event.
     *
     * @param User $user
     *
     * @return void
     */
    public function updated(User $user)
    {
        if ($user && !empty($user['id'])) {
            broadcast(new UserRelatedSettingIsUpdated($user->id, UserQueries::getSpecificUser($user->id)));
        }
    }
}