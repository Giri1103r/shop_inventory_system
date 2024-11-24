<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\Auth\LoginController as AuthLoginController;

use App\Http\Controllers\Admin\{LoginController, NotificationController, AdminController};
use App\Http\Controllers\{SettingsController, LocalizationController, TestController};

use App\Http\Controllers\Master\{UserLogController, UserPermissionController, UploadLogController, UserController, UserRoleController};
use App\Http\Controllers\Cron\CronController;
use App\Http\Controllers\Master\CompanyController;
use App\Http\Controllers\Master\LocationController;
use App\Http\Controllers\Master\UnitController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\WorkController;
use App\Http\Controllers\Master\EmployeeController;

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
Route::get('workmastertemp', [CronController::class, 'workMasterTemp']);
Route::get('worksave', [CronController::class, 'workSave']);

Route::get('employee_master_temp', [CronController::class, 'employeeMasterTemp']);
Route::get('employee_save', [CronController::class, 'EmployeeSave']);


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

        Route::get('dashboard', [AdminController::class, 'index'])->middleware('role:dashboard,view');
        Route::get('home', [AdminController::class, 'index'])->name('home');
        Route::get('profile', [AdminController::class, 'profileView']);

        /**
         * User Access Log
         */

        Route::get('admin/master/userlog/list', [UserLogController::class, 'index']);
        Route::post('admin/master/userlog/list', [UserLogController::class, 'index']);
        Route::get('admin/master/userlog/export/excel', [UserLogController::class, 'ExportExcel']);

        Route::get('administration/role/list', [UserRoleController::class, 'index']);
        Route::post('administration/role/list', [UserRoleController::class, 'index']);
        Route::get('administration/role/add', [UserRoleController::class, 'add']);
        Route::post('administration/role/add/submit',  [UserRoleController::class, 'store']);
        Route::get('administration/role/view/{id}', [UserRoleController::class, 'view']);
        Route::get('administration/role/edit/{id}',  [UserRoleController::class, 'edit']);
        Route::post('administration/role/edit/submit', [UserRoleController::class, 'update']);
        Route::post('administration/role/unique', [UserRoleController::class, 'uniqueCheck']);
        Route::post('administration/role/status', [UserRoleController::class, 'statusChange']);
        Route::post('administration/role/delete',  [UserRoleController::class, 'delete']);
        Route::get('administration/role/export/excel', [UserRoleController::class, 'exportExcel']);
        Route::get('administration/role/export/pdf', [UserRoleController::class, 'exportPdf']);
        Route::get('administration/role/import', [UserRoleController::class, 'import']);
        Route::post('administration/role/import/submit', [UserRoleController::class, 'importSubmit']);
        Route::get('administration/role/list/{companyId}', [UserRoleController::class, 'list']);
        Route::get('administration/role/sampledownload', [UserRoleController::class, 'DownloadSample']);
        /**
         * User Permission
         */

        Route::get('administration/permission/list', [UserPermissionController::class, 'index']);
        Route::post('administration/permission/get', [UserPermissionController::class, 'getUserPermission']);
        Route::post('administration/permission/update', [UserPermissionController::class, 'updateUserPermission']);

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

        /**
         * company master
         */
        Route::group(['prefix' => 'company'], function () {
            Route::get('/list', [CompanyController::class, 'index']);
            Route::post('/list', [CompanyController::class, 'index']);
            Route::get('/add', [CompanyController::class, 'add'])->middleware('role:company,add');
            Route::post('/add/submit', [CompanyController::class, 'store']);
            Route::get('/edit/{id}', [CompanyController::class, 'edit'])->middleware('role:company,edit');
            Route::post('/edit/submit', [CompanyController::class, 'update']);
            Route::get('/view/{id}', [CompanyController::class, 'view'])->middleware('role:company,view');
            Route::post('/delete', [CompanyController::class, 'delete'])->middleware('role:company,delete');
            Route::get('/export/excel', [CompanyController::class, 'exportExcel']);
            Route::get('/export/pdf', [CompanyController::class, 'exportPdf']);
            Route::get('/sampledownload', [CompanyController::class, 'DownloadSample']);
            Route::get('/import', [CompanyController::class, 'import'])->middleware('role:company,import');
            Route::post('/import/submit', [CompanyController::class, 'importSubmit']);
            Route::post('/status', [CompanyController::class, 'statusChange']);
            Route::post('/unique', [CompanyController::class, 'Uniquecheck']);
            Route::post('/passwordchange/{id}', [CompanyController::class, 'PasswordUpdate']);
            Route::post('/passwordchange/submit', [CompanyController::class, 'PasswordUpdateSubmit']);
        });

        /**
         * Location Master
         */

        Route::get('location/list', [LocationController::class, 'index']);
        Route::post('location/list', [LocationController::class, 'index']);
        Route::get('location/add', [LocationController::class, 'add'])->middleware('role:location,add');
        Route::post('location/add/submit',  [LocationController::class, 'store']);
        Route::get('location/view/{id}', [LocationController::class, 'view'])->middleware('role:location,view');
        Route::get('location/edit/{id}',  [LocationController::class, 'edit'])->middleware('role:location,edit');
        Route::post('location/edit/submit', [LocationController::class, 'update']);
        Route::post('location/unique', [LocationController::class, 'uniqueCheck']);
        Route::post('location/status', [LocationController::class, 'statusChange']);
        Route::post('location/delete',  [LocationController::class, 'delete'])->middleware('role:location,delete');
        Route::get('location/export/excel', [LocationController::class, 'exportExcel']);
        Route::get('location/export/pdf', [LocationController::class, 'exportPdf']);
        Route::get('location/import', [LocationController::class, 'import'])->middleware('role:location,import');
        Route::post('location/import/submit', [LocationController::class, 'importSubmit']);
        Route::get('location/alllist/{companyId}', [LocationController::class, 'alllist']);
        // Route::get('location/ajaxlist', [LocationController::class, 'list']);
        Route::get('location/ajaxlist/{locationId}/{id}', [LocationController::class, 'list']);

        Route::get('location/sampledownload', [LocationController::class, 'DownloadSample']);

        /**
         * unit master
         */
        Route::group(['prefix' => 'unit'], function () {
            Route::get('/list', [UnitController::class, 'index']);
            Route::post('/list', [UnitController::class, 'index']);
            Route::get('/add', [UnitController::class, 'add'])->middleware('role:unit,add');
            Route::post('/add/submit', [UnitController::class, 'store']);
            Route::get('/edit/{id}', [UnitController::class, 'edit'])->middleware('role:unit,edit');
            Route::post('/edit/submit', [UnitController::class, 'update']);
            Route::get('/view/{id}', [UnitController::class, 'view'])->middleware('role:unit,view');
            Route::post('/delete', [UnitController::class, 'delete'])->middleware('role:unit,delete');
            Route::get('/export/excel', [UnitController::class, 'exportExcel']);
            Route::get('/export/pdf', [UnitController::class, 'exportPdf']);
            Route::get('/sampledownload', [UnitController::class, 'DownloadSample']);
            Route::get('/import', [UnitController::class, 'import'])->middleware('role:unit,import');
            Route::post('/import/submit', [UnitController::class, 'importSubmit']);
            Route::post('/status', [UnitController::class, 'statusChange']);
            Route::post('/unique', [UnitController::class, 'Uniquecheck']);
            Route::get('/alllist/{locationId}', [UnitController::class, 'alllist']);
            Route::get('/ajaxlist/{locationId}/{id}', [UnitController::class, 'list']);

            // Route::get('/ajaxlist', [UnitController::class, 'list']);
        });

        /**
         *  Department master
         */
        Route::group(['prefix' => 'department'], function () {
            Route::get('/list', [DepartmentController::class, 'index']);
            Route::post('/list', [DepartmentController::class, 'index']);
            Route::get('/add', [DepartmentController::class, 'add'])->middleware('role:department,add');
            Route::post('/add/submit', [DepartmentController::class, 'store']);
            Route::get('/edit/{id}', [DepartmentController::class, 'edit'])->middleware('role:department,edit');
            Route::post('/edit/submit', [DepartmentController::class, 'update']);
            Route::get('/view/{id}', [DepartmentController::class, 'view'])->middleware('role:department,view');
            Route::post('/delete', [DepartmentController::class, 'delete'])->middleware('role:department,delete');
            Route::get('/export/excel', [DepartmentController::class, 'exportExcel']);
            Route::get('/export/pdf', [DepartmentController::class, 'exportPdf']);
            Route::get('/sampledownload', [DepartmentController::class, 'DownloadSample']);
            Route::get('/import', [DepartmentController::class, 'import'])->middleware('role:department,import');
            Route::post('/import/submit', [DepartmentController::class, 'importSubmit']);
            Route::post('/status', [DepartmentController::class, 'statusChange']);
            Route::post('/unique', [DepartmentController::class, 'Uniquecheck']);
            Route::get('/alllist/{unitId}', [DepartmentController::class, 'alllist']);
            Route::get('/ajaxlist/{unit_id}/{id}', [DepartmentController::class, 'list']);

            // Route::get('/ajaxlist', [DepartmentController::class, 'list']);
        });


        Route::group(['prefix' => 'work'], function () {
            Route::get('/list', [WorkController::class, 'index']);
            Route::post('/list', [WorkController::class, 'index']);
            Route::post('/add/submit', [WorkController::class, 'store']);
            Route::get('/edit/{id}', [WorkController::class, 'edit']);
            Route::post('/edit/submit', [WorkController::class, 'update']);
            Route::get('/view/{id}', [WorkController::class, 'view']);
            Route::get('/export/excel', [WorkController::class, 'exportExcel']);
            Route::get('/export/pdf', [WorkController::class, 'exportPdf']);
        });
        Route::group(['prefix' => 'employee'], function () {
            Route::get('/list', [EmployeeController::class, 'index']);
            Route::post('/list', [EmployeeController::class, 'index']);
            Route::post('/add/submit', [EmployeeController::class, 'store']);
            Route::get('/edit/{id}', [EmployeeController::class, 'edit']);
            Route::post('/edit/submit', [EmployeeController::class, 'update']);
            Route::get('/view/{id}', [EmployeeController::class, 'view']);
            Route::get('/export/excel', [EmployeeController::class, 'exportExcel']);
            Route::get('/export/pdf', [EmployeeController::class, 'exportPdf']);
         
        });
    });
});
