<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

/* Messages channel authorization, when user login */
Broadcast::channel('communication-channel.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});