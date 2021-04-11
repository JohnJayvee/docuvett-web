<?php

Route::group(['prefix' => 'audits', 'middleware' => ['jwt', 'access.control']], function () {
    Route::get('/', 'AuditsController@index')->name('audits.index');
    Route::get('/{auditableType}/{auditableId}', 'AuditsController@getByAuditableType')->name('audits.get-by-auditable-type');
});