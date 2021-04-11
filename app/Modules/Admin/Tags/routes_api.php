<?php

Route::group(['prefix' => 'tags', 'middleware' => ['jwt', 'access.control']], function () {
    Route::get('/', 'TagsController@index')->name('tags.index');
    Route::post('/', 'TagsController@store')->name('tags.store');
    Route::put('/{tag}', 'TagsController@update')->name('tags.update');
    Route::delete('/{tag}', 'TagsController@destroy')->name('tags.destroy');
});