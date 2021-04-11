<?php

Route::group(['prefix' => 'tags', 'middleware' => ['jwt', 'access.control']], function () {
    Route::get('/autocomplete', 'TagsController@getAutocomplete')->name('tags.autocomplete');
});