<?php

use App\Http\Controllers\Api\{LoginController, NotificationController, AdminController};
use App\Http\Controllers\Api\MasterController;
use App\Http\Controllers\Api\Ppemanagement\PpeExemptionController;
use App\Http\Controllers\Api\Ppemanagement\PpemanagementController;
use App\Http\Controllers\Api\Ppemanagement\PperequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');





Route::middleware('api')->prefix('v1')->group(function () {


    Route::post('login', [LoginController::class, 'login']);

    Route::post('password/forgot', [LoginController::class, 'forgotPassword']);
    Route::post('password/otp', [LoginController::class, 'passwordOtp']);
    Route::post('password/change', [LoginController::class, 'passwordChange']);

    Route::middleware(['auth:api', 'extendAccesstokenexpiration'])->group(function () {

        Route::get('user/profile', [LoginController::class, 'userProfile']);
        Route::get('dashboard', [LoginController::class, 'index']);
        Route::post('logout', [LoginController::class, 'logout']);

        /**
         * Master Routes
         */

         Route::post('/master/employee/dropdown', [MasterController::class, 'employee']);
         Route::post('/master/worker/dropdown', [MasterController::class, 'worker']);

         Route::group(['prefix' => 'ppe/master'], function () {
            Route::post('/ppetype', [PpemanagementController::class, 'ppetype']);
            Route::post('/ppemaster', [PpemanagementController::class, 'ppemaster']);
        });
        Route::group(['prefix' => 'ppe/master'], function () {
            Route::post('/ppetype', [PpemanagementController::class, 'ppetype']);
            Route::post('/ppemaster', [PpemanagementController::class, 'ppemaster']);
            Route::post('/ppestock', [PpemanagementController::class, 'ppestock']);

        });
        Route::group(['prefix' => 'ppe/pperequest'], function () {
            Route::post('/list', [PperequestController::class, 'list']);
            Route::post('/store', [PperequestController::class, 'store']);
            Route::post('/view', [PperequestController::class, 'view']);
            Route::post('/hodapproval', [PperequestController::class, 'hodapproval']);
            Route::post('/ehsapproval', [PperequestController::class, 'ehsapproval']);
            Route::post('/smapproval', [PperequestController::class, 'smapproval']);


        });
        Route::group(['prefix' => 'ppe/ppeexemption'], function () {
            Route::post('/list', [PpeExemptionController::class, 'list']);
            Route::post('/store', [PpeExemptionController::class, 'store']);
            Route::post('/view', [PpeExemptionController::class, 'view']);
            Route::post('/approval', [PpeExemptionController::class, 'approvereject']);

        });
    });
});
