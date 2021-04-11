<?php

Route::group(['prefix' => 'users', 'middleware' => ['jwt', 'access.control']], function () {
    Route::get('/', 'UsersController@index')->name('users.index');
    Route::get('/{user}', 'UsersController@get')->name('users.get');
    Route::post('/', 'UsersController@store')->name('users.store');
    Route::post('/login-as', 'UsersController@loginAs')->name('users.login-as');
    Route::put('/{user}', 'UsersController@update')->name('users.update');
    Route::delete('/{user}', 'UsersController@destroy')->name('users.destroy');
    Route::get('/customers', 'UsersController@customers')->name('users.customers');
});
