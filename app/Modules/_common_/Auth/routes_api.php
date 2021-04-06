<?php

Route::post('login', 'LoginController@login')->name('api.login');
Route::post('register', 'RegisterController@register')->name('api.register');
Route::post('validate', 'RegisterController@validateData')->name('api.validateData');
Route::post('send-confirmation', 'RegisterController@sendConfirmation')->name('api.send-confirmation');
Route::post('send-reset-email', 'ForgotPasswordController@sendResetEmail')->name('api.send-reset-email');
Route::post('password-reset', 'ResetPasswordController@passwordReset')->name('api.password-reset');

Route::group(['middleware' => ['jwt']], function () {
    Route::post('logout', 'LoginController@logout')->name('api.logout');
    Route::get('refresh', 'LoginController@refresh')->name('api.refresh');
});