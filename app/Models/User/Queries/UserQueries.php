<?php

namespace App\Models\User\Queries;

use App\Libraries\SentimentAnalysis\SentimentAnalysis;
use App\Libraries\Utils\Utils;
use App\Models\Connection\Connection;
use App\Models\MessagesIncoming\Queries\MessagesIncomingQueries;
use App\Models\Taskboard\Taskboard;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class UserQueries
{
    /**
     * @return array
     */
    public static function getCurrentUser()
    {
        $curUser = Utils::getCurrentUser();

        return self::prepareResponse($curUser);
    }

    /**
     * @param User $curUser
     *
     * @return array
     */
    private static function prepareResponse($curUser)
    {
        $user = [];
        if ($curUser) {
            $user = User
                ::whereId($curUser->id)
                ->with(['companies'])
                ->first()
                ->toArray();

            $user += [
                'roleList'       => $curUser->roles()->pluck('name')->toArray(),
                'permissionList' => $curUser->allPermissions()->pluck('name')->toArray(),
            ];
        }

        return $user;
    }

    /**
     * @param $id
     *
     * @return array
     */
    public static function getSpecificUser($id)
    {
        $curUser = User::find($id);

        return self::prepareResponse($curUser);
    }

    /**
     * @param $data
     *
     * @return array
     */
    public static function getErrors($data)
    {
        $errors = [];

        return $errors;
    }

    public static function getUsersWithToken()
    {
        $result = collect([]);
        $users = User::all();
        foreach ($users as $user) {
            $fileName = md5($user->id);
            $file = storage_path( "app/gsuite/tokens/{$fileName}.json" );
            if (file_exists($file)) {
                $result->add($user);
            }
        }

        return $result;
    }
}
