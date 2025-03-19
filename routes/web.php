<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('reboot', function() {
	Artisan::call('storage:link');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    file_put_contents(storage_path('logs/laravel.log'),'');
    Artisan::call('key:generate');
    Artisan::call('config:cache');
    return '<center><h1>System Rebooted!</h1></center>';
});

/*
Route::group(['middleware' => 'guest'], function(){
	Route::get('/', 'HomeController@login');
	
});
*/

Auth::routes();

// need to modify this routes
Route::get('hr/payroll/promotion-jobs', 'Hr\Recruitment\BenefitController@promotionJobs');
Route::get('hr/payroll/increment-jobs', 'Hr\Recruitment\BenefitController@incrementJobs');

Route::group(['middleware' => 'auth'], function(){

	Route::get('/clear-cache', 'HomeController@clear');
	Route::get('/mmr-report', 'Hr\ReportController@mmr');
	Route::get('/profile', 'Hr\ProfileController@showProfile');

	Route::get('hr/timeattendance/shift-jobs', 'Hr\TimeAttendance\ShiftRoasterController@shiftJobs');
	Route::get('hr/leave/leave_status_jobs', 'Hr\TimeAttendance\LeaveWorkerController@maternityLeaveCheck');
	Route::get('hr/leave/leave_status_update_jobs', 'Hr\TimeAttendance\LeaveWorkerController@LeaveStatusCheckAndUpdate');
	Route::get('/search-employee-result', 'SearchController@searchEmp');
	Route::get('dashboard', 'DashboardController@index');

	Route::get('user-dashboard/conversations', 'UserDashboardController@conversations');
	Route::get('user-dashboard/send-message', 'UserDashboardController@sendMessage');
	Route::get('user-dashboard/delete-message', 'UserDashboardController@deleteMessage');

  	//user dashboard
	Route::get('dashboard', 'UserDashboardController@index')->name('user-dashboard');
	Route::post('user-dashboard/events', 'UserDashboardController@eventSettings');
	Route::get('user-dashboard/conversations', 'UserDashboardController@conversations');
	Route::get('user-dashboard/send-message', 'UserDashboardController@sendMessage');
	Route::get('user-dashboard/delete-message', 'UserDashboardController@deleteMessage');

	Route::get('/user-search', 'UserDashboardController@userSearch');
	// employee search
	Route::get('/search', 'SearchController@search');
	Route::post('/search/suggestion', 'SearchController@suggestion');

});

Route::group(['middleware' => 'auth'], function(){
	Route::get('/', 'HomeController@index');

	Route::get('user/change-password', 'Hr\Adminstrator\UserController@password');
	Route::post('user/change-password', 'Hr\Adminstrator\UserController@changePassword');

	@include 'modules/pms.php';
    @include 'modules/my_project.php';
    @include 'modules/hr.php';
    @include 'modules/merchandising.php';
    @include 'modules/accounting.php';
});
@include 'modules/guest.php';

Route::get('test-check', 'TestXYZController@rfidUpdate');


