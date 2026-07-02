<?php


use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Cron\CronController;
use App\Http\Controllers\Admin\Master\WorkerLogController;
use App\Http\Controllers\Admin\Master\EmployeeLogController;








use App\Http\Controllers\{SettingsController, LocalizationController, TestController};

use App\Http\Controllers\Admin\{LoginController, NotificationController, AdminController, BlockedController};
use App\Http\Controllers\Admin\Master\CategoryController;
use App\Http\Controllers\Admin\Master\DepartmentController;
use App\Http\Controllers\Admin\Master\ManufacturerController;
use App\Http\Controllers\Admin\Master\MedicineController;
use App\Http\Controllers\Admin\Master\SupplierController;
use App\Http\Controllers\Admin\Master\TaxController;
use App\Http\Controllers\Admin\Master\uomController;
use App\Http\Controllers\Admin\Master\WarehouseController;
use App\Http\Controllers\Master\{UserLogController, UserPermissionController, UploadLogController, UserController, UserRoleController};
use App\Http\Controllers\PWAController;


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


Route::get('cron/master/work/all-details-temp', [CronController::class, 'workMasterAllDetailsTemp']);
Route::get('cron/master/workmastertemp', [CronController::class, 'workMasterTemp']);
Route::get('cron/master/worker/temp-custom-details', [CronController::class, 'workMasterTempCustom']);
Route::get('cron/master/worksave', [CronController::class, 'workSave']);
Route::get('cron/master/employee/all-details-temp', [CronController::class, 'employeeMasterTempAllDetails']);
Route::get('cron/master/employee/temp-details', [CronController::class, 'employeeMasterTemp']);
Route::get('cron/master/employee/temp-custom-details', [CronController::class, 'employeeMasterTempCustom']);
Route::get('cron/master/employee/temp-custom-token-details', [CronController::class, 'employeeMasterTempCustomToken']);
Route::get('cron/master/employee_save', [CronController::class, 'EmployeeSave']);


Route::get('test', [TestController::class,  'index']);


Route::get('manifest', [PWAController::class, 'manifestJson']);

Route::middleware(['securityheader'])->group(function () {

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
        Route::post('password/reset-password/submit', [LoginController::class, 'passwordResetSubmit']);

        Route::middleware(['islogin', 'language'])->group(function () {

            Route::get('dashboard', [AdminController::class, 'index'])->middleware('role:dashboard,view');
            Route::get('home', [AdminController::class, 'index'])->name('home');
            Route::get('profile', [AdminController::class, 'profileView']);
            Route::post('profile/image/update', [AdminController::class, 'profileUpdate']);
            Route::post('profile/signature-upload/update', [AdminController::class, 'signatureUpload']);

            Route::post('profile/update', [AdminController::class, 'Update']);
            Route::post('profile/password/update', [AdminController::class, 'changeProfilePassword']);






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


            Route::get('administration/users/list', [UserController::class, 'index']);
            Route::post('administration/users/list', [UserController::class, 'index']);
            Route::get('administration/users/export/excel', [UserController::class, 'exportExcel']);
            Route::get('administration/users/export/pdf', [UserController::class, 'exportPdf']);
            /**
             * User Permission
             */

            Route::get('administration/permission/list', [UserPermissionController::class, 'index']);
            Route::post('administration/permission/get', [UserPermissionController::class, 'getUserPermission']);
            Route::post('administration/permission/update', [UserPermissionController::class, 'updateUserPermission']);


            Route::get('blocked', [BlockedController::class, 'blocked']);
            Route::post('blocked-save', [BlockedController::class, 'blockedSave']);

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
             *Employee Error Log
             */

            Route::get('employeelog/list', [EmployeeLogController::class, 'index']);
            Route::post('employeelog/list', [EmployeeLogController::class, 'index']);
            /**
             * Worker Error Log
             */

            Route::get('workerlog/list', [WorkerLogController::class, 'index']);
            Route::post('workerlog/list', [WorkerLogController::class, 'index']);

            /**
             * Notification
             */

            Route::get('notification/list', [NotificationController::class, 'notificationList']);
            Route::post('notification/list', [NotificationController::class, 'notificationList']);
            Route::get('notification/read/{id}', [NotificationController::class, 'notificationRead']);
            Route::get('notification/readall', [NotificationController::class, 'notificationAllRead']);
            Route::get('notification/view/{id}', [NotificationController::class, 'notificationView']);

            /**
             *  master
             */


            /**
             *  Category master
             */


            Route::prefix('master/category')->controller(CategoryController::class)->group(function () {
                Route::get('list', 'index');
                Route::post('list', 'index');

                Route::get('add', 'add');
                Route::post('add/submit', 'store');

                Route::get('edit/{id}', 'edit');
                Route::post('edit/submit', 'update');
                Route::post('unique', 'Uniquecheck');

                Route::get('view/{id}', 'view');
                Route::post('delete', 'delete');

                Route::get('export/excel', 'exportExcel');
                Route::get('export/pdf', 'exportPdf');

                Route::get('import', 'import');
                Route::post('import/submit', 'importSubmit');

                Route::post('status', 'statusChange');
            });
            Route::prefix('master/manufacture')->controller(ManufacturerController::class)->group(function () {
                Route::get('list', 'index');
                Route::post('list', 'index');

                Route::get('add', 'add');
                Route::post('add/submit', 'store');

                Route::get('edit/{id}', 'edit');
                Route::post('edit/submit', 'update');

                Route::get('view/{id}', 'view');
                Route::post('delete', 'delete');
                Route::post('unique', 'Uniquecheck');

                Route::get('export/excel', 'exportExcel');
                Route::get('export/pdf', 'exportPdf');

                Route::get('import', 'import');
                Route::post('import/submit', 'importSubmit');

                Route::post('status', 'statusChange');
            });
            Route::prefix('master/uom')->controller(uomController::class)->group(function () {
                Route::get('list', 'index');
                Route::post('list', 'index');

                Route::get('add', 'add');
                Route::post('add/submit', 'store');

                Route::get('edit/{id}', 'edit');
                Route::post('edit/submit', 'update');
                Route::post('unique', 'Uniquecheck');

                Route::get('view/{id}', 'view');
                Route::post('delete', 'delete');

                Route::get('export/excel', 'exportExcel');
                Route::get('export/pdf', 'exportPdf');

                Route::get('import', 'import');
                Route::post('import/submit', 'importSubmit');

                Route::post('status', 'statusChange');
            });
            Route::prefix('master/tax')->controller(TaxController::class)->group(function () {
                Route::get('list', 'index');
                Route::post('list', 'index');

                Route::get('add', 'add');
                Route::post('add/submit', 'store');

                Route::get('edit/{id}', 'edit');
                Route::post('edit/submit', 'update');

                Route::get('view/{id}', 'view');
                Route::post('delete', 'delete');
                Route::post('unique', 'Uniquecheck');

                Route::get('export/excel', 'exportExcel');
                Route::get('export/pdf', 'exportPdf');

                Route::get('import', 'import');
                Route::post('import/submit', 'importSubmit');

                Route::post('status', 'statusChange');
            });
            Route::prefix('master/medicine')->controller(MedicineController::class)->group(function () {
                Route::get('list', 'index');
                Route::post('list', 'index');

                Route::get('add', 'add');
                Route::post('add/submit', 'store');

                Route::get('edit/{id}', 'edit');
                Route::post('edit/submit', 'update');
                Route::post('unique', 'Uniquecheck');

                Route::get('view/{id}', 'view');
                Route::post('delete', 'delete');

                Route::get('export/excel', 'exportExcel');
                Route::get('export/pdf', 'exportPdf');

                Route::get('import', 'import');
                Route::post('import/submit', 'importSubmit');

                Route::post('status', 'statusChange');
            });
          






























            // OHC Management



















        });
    });
});
