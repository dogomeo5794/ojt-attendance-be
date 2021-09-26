<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});




// Route::prefix('v1')->group(function() {
Route::group(['middleware' => 'api', 'prefix' => 'v1'], function ($router) {
    /**
     * Account Controllers
     */
    Route::post('/user-login', 'AccountController@userLogin');
    Route::post('/registration', 'AccountController@registerAccount');
    Route::post('/search-existing-office', 'AccountController@searchExistingCompany');
    Route::post('/check-fresh-app', 'AccountController@checkFreshApp');

    /**
     *  Student Controllers
     */
    Route::post('/create-student', 'StudentController@createStudent');
    // Route::get('/create-qr-code', 'StudentController@createQrCode');
    Route::get('/created-student-list', 'StudentController@createdStudentList');
    Route::post('/student-info', 'StudentController@collectStudentInfo');
    Route::post('/assigning-office', 'StudentController@assigningOffice');

    /**
     * Attendance Controller
     */
    Route::post('/checking-attendance', 'AttendanceController@checkingAttendance');
    
    
    // Route::post('/validate-init-reg', 'AccountController@validateInitReg');
    // Route::post('/complete-registration', 'AccountController@completeReg');
    // Route::post('/staff-login', 'AccountController@staffLogin');
    // Route::post('/create-staff-account', 'AccountController@createStaffAccount');

    // Route::get('/medicine-list', 'MedicineController@medicineList');
    // Route::post('/medicine-add', 'MedicineController@medicineAdd');
});


