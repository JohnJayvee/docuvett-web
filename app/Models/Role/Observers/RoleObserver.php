<?php

namespace App\Models\Role\Observers;

use App\Events\UserRelatedSettingIsUpdated;
use App\Models\User\Queries\UserQueries;
use App\Models\Role\Role;

class RoleObserver
{
    /**
     * Listen to the Role updated event.
     *
     * @param Role $role
     *
     * @return void
     */
    public function updated(Role $role)
    {
        if ($role && !empty($role['id'])) {
            $user_ids = $role->users()->pluck('id')->toArray();
            foreach ($user_ids as $id) {
                broadcast(new UserRelatedSettingIsUpdated($id, UserQueries::getSpecificUser($id)));
            }
        }
    }
}