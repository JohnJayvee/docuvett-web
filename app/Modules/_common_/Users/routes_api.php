<?php

Route::group(['prefix' => 'users', 'middleware' => ['jwt']], function () {
    Route::get('/current', 'UsersController@getCurrentUser')->name('users.current');
});
