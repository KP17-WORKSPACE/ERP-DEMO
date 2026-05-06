<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|Route::group(['middleware' => ['auth', 'verified', 'admin'], 'prefix' => 'knowledgebase'], function () {

*/
Route::group(['middleware' => ['CheckDashboardMiddleware', 'XSS'], 'prefix' => 'project'], function () {


    Route::get('/category', 'ProjectController@InfixProjectCategoryList')->name('InfixProjectCategoryList');
    Route::get('/category/edit/{id}', 'ProjectController@InfixProjectCategoryEdit')->name('InfixProjectCategoryEdit');
    Route::DELETE('/category/delete/{id}', 'ProjectController@InfixProjectCategoryDelete')->name('InfixProjectCategoryDelete');
    Route::post('/category/add', 'ProjectController@InfixProjectCategoryStore')->name('InfixProjectCategoryStore');
    Route::post('/category/update', 'ProjectController@InfixProjectCategoryUpdate')->name('InfixProjectCategoryUpdate');


    /*START TEAM ROUTES */
    Route::get('/team', 'ProjectController@InfixTeamList')->name('InfixTeamList');
    Route::post('/team/add', 'ProjectController@InfixTeamStore')->name('InfixTeamStore');
    Route::get('/team/edit/{id}', 'ProjectController@InfixTeamEdit')->name('InfixTeamEdit');
    Route::post('/team/update', 'ProjectController@InfixTeamUpdate')->name('InfixTeamUpdate');
    Route::get('/team/delete/{id}', 'ProjectController@InfixTeamDelete')->name('InfixTeamDelete');
    /*END TEAM ROUTES */




    /*START PROJECT ROUTES */
    Route::get('/project-list', 'ProjectController@InfixProjectList')->name('InfixProjectList');
    Route::post('/project-store', 'ProjectController@InfixProjectStore')->name('InfixProjectStore');

    Route::get('/project-delete/{id}', 'ProjectController@InfixProjectDelete')->name('InfixProjectDelete');
    Route::get('/project-edit/{id}', 'ProjectController@InfixProjectEdit')->name('InfixProjectEdit');
    Route::post('/project-update', 'ProjectController@InfixProjectUpdate')->name('InfixProjectUpdate');
    /*END PROJECT ROUTES */

    /*START TEAM ROUTES */
    Route::get('/project-team', 'ProjectController@InfixProjectTeamList')->name('InfixProjectTeamList');
    Route::post('/project-team/add', 'ProjectController@InfixProjectTeamStore')->name('InfixProjectTeamStore');

    Route::get('/project-complete/{id}', 'ProjectController@InfixProjectComplete')->name('InfixProjectComplete');
    Route::get('/project-incomplete/{id}', 'ProjectController@InfixProjectIncomplete')->name('InfixProjectIncomplete');

    /*END TEAM ROUTES */

    /*START TASK ROUTES */
    Route::get('/project-task/{id}', 'ProjectController@InfixProjectTaskList')->name('InfixProjectTaskList');
    Route::post('/project-task/add', 'ProjectController@InfixProjectTaskStore')->name('InfixProjectTaskStore');
    Route::post('/project-task/update', 'ProjectController@InfixProjectTaskUpdate')->name('InfixProjectTaskUpdate');
    Route::get('/project-task/task-complete/{id}', 'ProjectController@InfixProjectTaskComplete')->name('InfixProjectTaskComplete');

    Route::get('/project-task/task-edit/{id}', 'ProjectController@InfixProjectTaskEdit')->name('InfixProjectTaskEdit');
    Route::get('/project-task/task-delete/{id}', 'ProjectController@InfixProjectTaskDelete')->name('InfixProjectTaskDelete');

    Route::get('/project-task/get-imcomplete-task', 'ProjectController@InfixProjectTaskImcomplete')->name('InfixProjectTaskImcomplete');
    Route::post('download-task-attachment', 'ProjectController@downloadTaskFile');


    Route::get('/my-task', 'ProjectController@InfixMyTaskList')->name('InfixMyTaskList');
    Route::get('/my-projects', 'ProjectController@InfixMyProjectList')->name('InfixMyProjectList');

    /*END TASK ROUTES */
});
