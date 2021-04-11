<?php

Route::group(['prefix' => 'roles', 'middleware' => ['jwt', 'access.control']], function () {
    Route::get('/', 'RolesController@index')->name('roles.index');
    Route::get('/autocomplete', 'RolesController@autocomplete')->name('roles.autocomplete');
    Route::get('/{role}', 'RolesController@get')->name('roles.read');
    Route::post('/', 'RolesController@store')->name('roles.create');
    Route::put('/{role}', 'RolesController@update')->name('roles.update');
    Route::delete('/{role}', 'RolesController@destroy')->name('roles.delete');
});