<?php

Route::prefix('pms')->namespace('Myproject')->as('my_project.')->group(function (){
    Route::get('/dashboard-grid', 'ProjectController@grid')->name('grid');
    Route::get('/status/{id}', 'ProjectController@projectStatus')->name('status');
    Route::get('/gantt-chart/{project}', 'ProjectController@ganttChart')->name('gantt-chart');
    Route::get('/gantt-chart-data/{project}', 'ProjectController@ganttChartsDatas')->name('gantt-chart-data');
    Route::get('/status-wise-project-chart', 'ProjectController@statusWiseProjectChart')->name('status-wise-project-chart');
    Route::resource('/my-project', 'ProjectController');
    Route::get('/my-project-department/{project}', 'ProjectController@projectDepartment')->name('departments');
    Route::delete('/my-project-deliverable/{deliverable}', 'ProjectController@deliverableDestroy')->name('deliverable-destroy');

    Route::prefix('/step')->group(function (){
        Route::get('/{step}', 'ProjectController@fromStepControl');
    });

    Route::get('/project-detail/{project}', 'ProjectController@showProjectTable')->name('detail');

    Route::get('/modal-show/{deliverable}', 'ProjectController@subDeliverableModalShow')->name('modal-show');
    Route::post('/sub-deliverable/{deliverable}', 'ProjectController@storeSubDeliverable')->name('sub-deliverable.store');
    Route::get('/sub-deliverable/{subDeliverable}', 'ProjectController@showSubDeliverable')->name('sub-deliverable.show');
    Route::put('/sub-deliverable/{subDeliverable}', 'ProjectController@updateSubDeliverable')->name('sub-deliverable.update');
    Route::delete('/sub-deliverable/{subDeliverable}', 'ProjectController@destroySubDeliverable')->name('sub-deliverable.destroy');

    Route::get('/modal-show-task/{subDeliverable}', 'ProjectController@taskModalShow')->name('modal-show-task');
    Route::post('/task/{subDeliverable}', 'ProjectController@storeTask')->name('task.store');
    Route::get('/task/{task}', 'ProjectController@showTask')->name('task.show');
    Route::put('/task/{task}', 'ProjectController@updateTask')->name('task.update');
    Route::delete('/task/{task}', 'ProjectController@destroyTask')->name('task.destroy');

    Route::post('/insert-action/{task}', 'ProjectController@taskUpdateAction')->name('insert-action');
    Route::post('/insert-action-project/{project}', 'ProjectController@projectUpdateAction')->name('insert-project-action');

    Route::get('/project-report/{project}', 'ProjectController@projectReport')
            ->name('project-report');

    Route::as('day-setup.')->prefix('/day-setup')->group(function (){
        Route::get('/', 'WeekDayController@index')->name('index');
        Route::post('/', 'WeekDayController@store')->name('store');
    });

    Route::as('holiday-setup.')->prefix('/holiday-setup')->group(function (){
        Route::get('/', 'HolidayController@index')->name('index');
        Route::get('/holidays', 'HolidayController@getHolidays')->name('holidays');
        Route::get('/holiday', 'HolidayController@getHoliday')->name('holiday');
        Route::post('/holiday', 'HolidayController@storeHoliday')->name('store-holiday');
        Route::delete('/holiday', 'HolidayController@destroyHoliday')->name('destroy-holiday');
    });

    Route::get('/pmo', 'ProjectController@pmo')->name('pmo');
    Route::get('/pmo/{id}', 'ProjectController@getUserAsDepartment');
    Route::post('/pmo', 'ProjectController@assignUserWithPermission');
});