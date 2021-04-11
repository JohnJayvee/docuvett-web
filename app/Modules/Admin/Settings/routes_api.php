<?php

Route::group(['prefix' => 'settings', 'middleware' => ['jwt', 'access.control']], function () {
    Route::get('/', 'SettingsController@index')->name('settings.index');
    Route::post('/', 'SettingsController@store')->name('settings.create');
    Route::get('show', 'SettingsController@show')->name('settings.show');
    Route::post('/refactor-registrations', 'SettingsController@refactorRegistrations')->name('settings.refactor-registrations');
    Route::post('/split-names', 'SettingsController@splitNames')->name('settings.split-names');
    Route::post('/refactor-corrs', 'SettingsController@refactorCorrs')->name('settings.refactor-corrs');
});