<?php

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

// Controllers
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\RemarkController;
use App\Http\Controllers\DeviceController;

Route::get('/', function () {
    return view('device');
})->name('device');

Route::get('/user_management', function () {
    return view('user_management');
})->name('user_management');

Route::get('/remark', function () {
    return view('remark');
})->name('remark');

Route::get('/device', function () {
    return view('device');
})->name('device');

Route::controller(UserManagementController::class)->group(function () {
    // USER MANAGEMNET
    Route::get('/view_user', 'viewUser')->name('view_user');
    Route::get('/get_rapidx_user_active_in_systemone', 'getRapidxUserActiveInSystemOne')->name('get_rapidx_user_active_in_systemone');
    Route::get('/get_systemone_department', 'getSystemOneDepartment')->name('get_systemone_department');
    Route::get('/get_systemone_position', 'getSystemOnePosition')->name('get_systemone_position');
    Route::post('/user_create_update', 'userCreateUpdate')->name('user_create_update');
    Route::get('/get_user_info_by_id', 'getUserInfoById')->name('get_user_info_by_id');
    Route::post('/change_user_status', 'changeUserStatus')->name('change_user_status');

    // USER CLASSIFICATION
    Route::get('/view_user_classification', 'viewUserClassification')->name('view_user_classification');
    Route::get('/get_info_from_user_management', 'getInfoFromUserManagement')->name('get_info_from_user_management');
    Route::post('/create_user_classification', 'createUserClassification')->name('create_user_classification');
    Route::post('/remove_user_classification', 'removeUserClassification')->name('remove_user_classification');
});

Route::controller(RemarkController::class)->group(function () {
    Route::get('/view_remark', 'viewRemark')->name('view_remark');
    Route::post('/remark_create_update', 'remarkCreateUpdate')->name('remark_create_update');
    Route::get('/get_remark_info_by_id', 'getRemarkInfoById')->name('get_remark_info_by_id');
    Route::post('/change_remark_status', 'changeRemarkStatus')->name('change_remark_status');
});

Route::controller(DeviceController::class)->group(function () {
    Route::get('/view_device', 'viewDevice')->name('view_device');
    Route::post('/device_create_update', 'deviceCreateUpdate')->name('device_create_update');
    Route::get('/get_device_info_by_id', 'getDeviceInfoById')->name('get_device_info_by_id');
    Route::post('/change_device_status', 'changeDeviceStatus')->name('change_device_status');
    Route::get('/view_device_history', 'viewDeviceHistory')->name('view_device_history');
});
