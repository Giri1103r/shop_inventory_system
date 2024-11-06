<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\Auth\LoginController as AuthLoginController;

use App\Http\Controllers\Admin\{LoginController, NotificationController, AdminController};
use App\Http\Controllers\{SettingsController, LocalizationController,TestController};

use App\Http\Controllers\Master\{UserLogController, UserPermissionController, UploadLogController, UserController, UserRoleController};
use App\Http\Controllers\Cron\CronController;

Route::get('cache', function () {
    Artisan::call('optimize:clear');
    return 'Routes cache cleared';
});

Route::get('migrate', function () {
    Artisan::call('migrate');
    return 'Table migrated';
});

Route::get('/seed', function () {
    Artisan::call('db:seed');
    return 'Seeding completed';
});

Route::get('/seed/{className}', function ($className) {
    Artisan::call('db:seed', ['--class' => $className]);
    return 'Seeding completed for ' . $className;
});


Route::get('queuehigh', [CronController::class, 'queueHigh']);
Route::get('queuedefault', [CronController::class, 'queueDefault']);
Route::get('queueemail', [CronController::class, 'queueEmail']);


Route::get('test', [TestController::class,  'index']);


Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

Auth::routes();



Route::middleware(['userlog'])->group(function () {


    /**
     * Localization
     */
    Route::get('lang/change/{lang}/{lang_id}', [LocalizationController::class, 'lang_change'])->name('changeLang');
    Route::get('theme/change', [SettingsController::class, 'theme_change'])->name('changetheme');


    Route::get('Account_Activate/{token}', [LoginController::class, 'Account_Activate']);
    Route::post('SubmitAccountActivate', [LoginController::class, 'SubmitAccountActivate']);

    Route::get('login', [LoginController::class, 'showLoginForm']);
    Route::post('logintry', [LoginController::class, 'authenticate']);
    Route::post('logout', [LoginController::class, 'logout']);
    Route::get('reset-password', [LoginController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('reset-password/store', [LoginController::class, 'resetPassword'])->name('password.reset.store');
    Route::post('/password/resend-otp', [LoginController::class, 'resendOTP'])->name('password.resend.otp');
    Route::get('password/forgot', [LoginController::class, 'forgotPassword']);
    Route::post('password/forgot/submit', [LoginController::class, 'sendOTP'])->name('resetpasswordSend');
    Route::get('password/otp', [LoginController::class, 'passwordOTP'])->name('otp.page');
    Route::post('password/otp/submit', [LoginController::class, 'passwordOTPSubmit']);
    Route::get('password/finalreset/form', [LoginController::class, 'passwordReset']);
    Route::post('password/finalreset/submit', [LoginController::class, 'passwordResetSubmit']);

    Route::middleware(['islogin', 'language'])->group(function () {

        Route::get('dashboard', [AdminController::class, 'index']);
        Route::get('home', [AdminController::class, 'index'])->name('home');
        Route::get('profile', [AdminController::class, 'profileView']);

        /**
         * User Access Log
         */

        Route::get('admin/master/userlog/list', [UserLogController::class, 'index']);
        Route::post('admin/master/userlog/list', [UserLogController::class, 'index']);
        Route::get('admin/master/userlog/export/excel', [UserLogController::class, 'ExportExcel']);

        Route::get('admin/master/role/list', [UserRoleController::class, 'index']);
        Route::post('admin/master/role/list', [UserRoleController::class, 'index']);
        Route::get('user/role/list/add', [UserRoleController::class, 'add']);
        Route::post('user/role/list/add/submit',  [UserRoleController::class, 'store']);
        Route::get('user/role/list/view/{id}', [UserRoleController::class, 'view']);
        Route::get('user/role/list/edit/{id}',  [UserRoleController::class, 'edit']);
        Route::post('user/role/list/edit/submit', [UserRoleController::class, 'update']);
        Route::post('user/role/list/unique', [UserRoleController::class, 'uniqueCheck']);
        Route::post('user/role/list/status', [UserRoleController::class, 'statusChange']);
        Route::post('user/role/list/delete',  [UserRoleController::class, 'delete']);
        Route::get('user/role/list/export/excel', [UserRoleController::class, 'exportExcel']);
        Route::get('user/role/list/export/pdf', [UserRoleController::class, 'exportPdf']);
        Route::get('user/role/list/import', [UserRoleController::class, 'import']);
        Route::post('user/role/list/import/submit', [UserRoleController::class, 'importSubmit']);
        Route::get('user/role/list/list/{companyId}', [UserRoleController::class, 'list']);
        Route::get('user/role/list/sampledownload', [UserRoleController::class, 'DownloadSample']);
        /**
         * User Permission
         */

        Route::get('admin/master/permission/list', [UserPermissionController::class, 'index']);
        Route::post('user/permission/get', [UserPermissionController::class, 'getUserPermission']);
        Route::post('user/permission/update', [UserPermissionController::class, 'updateUserPermission']);

        /**
         * File Upload Error Log
         */

        Route::get('uploadlog/list', [UploadLogController::class, 'index']);
        Route::post('uploadlog/list', [UploadLogController::class, 'index']);
        Route::get('uploadlog/list/{logid}', [UploadLogController::class, 'view']);
        Route::post('uploadlog/list/{logid}', [UploadLogController::class, 'view']);
        Route::get('uploadlog/download/{logid}', [UploadLogController::class, 'download']);
        Route::get('uploadlog/export/excel/{logid}', [UploadLogController::class, 'ExportExcel']);

        /**
         * Notification
         */

        Route::get('notification/list', [NotificationController::class, 'notificationList']);
        Route::post('notification/list', [NotificationController::class, 'notificationList']);
        Route::get('notification/read/{id}', [NotificationController::class, 'notificationRead']);
        Route::get('notification/readall', [NotificationController::class, 'notificationAllRead']);
        Route::get('notification/view/{id}', [NotificationController::class, 'notificationView']);
    });
});
