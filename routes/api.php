<?php

use App\Http\Controllers\Api\{LoginController, NotificationController, AdminController};
use App\Http\Controllers\Api\MasterController;
use App\Http\Controllers\Api\Permit\SafetyPermitController;
use App\Http\Controllers\Api\Ppemanagement\PpeExemptionController;
use App\Http\Controllers\Api\Ppemanagement\PpemanagementController;
use App\Http\Controllers\Api\Ppemanagement\PperequestController;
use App\Http\Controllers\Api\Trainig\TrainingSheducleController;
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

        Route::post('notification', [NotificationController::class, 'notification']);
        Route::post('notification/update', [NotificationController::class, 'notificationUpdate']);

        /**
         * Master Routes
         */

         Route::post('/master/employee/dropdown', [MasterController::class, 'employee']);
         Route::post('/master/worker/dropdown', [MasterController::class, 'worker']);
         Route::post('/master/company/dropdown', [MasterController::class, 'company']);
         Route::post('/master/location/dropdown', [MasterController::class, 'location']);
         Route::post('/master/unit/dropdown', [MasterController::class, 'unit']);
         Route::post('/master/department/dropdown', [MasterController::class, 'department']);


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

        Route::group(['prefix' => 'ptw/safetypermit'], function () {
            Route::post('/list', [SafetyPermitController::class, 'list']);
            Route::post('/store', [SafetyPermitController::class, 'store']);
            Route::post('/view', [SafetyPermitController::class, 'view']);
            Route::post('/approval', [SafetyPermitController::class, 'ehsapproval']);
            Route::post('/qrcode', [SafetyPermitController::class, 'qrcode']);
            Route::post('/reassignEmployeeList', [SafetyPermitController::class, 'getReassignEmployee']);


        });
        Route::group(['prefix' => 'trainng/master/dropdown/'], function () {
            Route::post('/topiclist', [TrainingSheducleController::class, 'topiclist']);
            Route::post('/venulist', [TrainingSheducleController::class, 'venulist']);

        });
        Route::group(['prefix' => 'trainng/training-schedule'], function () {
            Route::post('/list', [TrainingSheducleController::class, 'list']);
            Route::post('/view', [TrainingSheducleController::class, 'view']);
            Route::post('/attendance-recoder', [TrainingSheducleController::class, 'storeAttendance']);
            Route::post('/post-assessment', [TrainingSheducleController::class, 'endTrainingStore']);

        });
    });
});
