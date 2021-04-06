<?php

Route::group(['prefix' => 'permissions', 'middleware' => ['jwt']], function () {
    Route::get('/', 'PermissionsController@index')->name('permissions.index');
});