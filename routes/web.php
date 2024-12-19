<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\Auth\LoginController as AuthLoginController;

use App\Http\Controllers\Admin\{LoginController, NotificationController, AdminController};
use App\Http\Controllers\{SettingsController, LocalizationController, TestController};

use App\Http\Controllers\Master\{PpeTypeController, PpeTypeMasterController, UserLogController, UserPermissionController, UploadLogController, UserController, UserRoleController};
use App\Http\Controllers\Cron\CronController;
use App\Http\Controllers\Master\CompanyController;
use App\Http\Controllers\Master\TopicController;
use App\Http\Controllers\Master\TrainingMatrixController;
use App\Http\Controllers\Master\TrainingCalendarController;
use App\Http\Controllers\Master\TrainingScheduleController;
use App\Http\Controllers\Master\NominationProcessController;
use App\Http\Controllers\Master\VenueController;
use App\Http\Controllers\Master\LocationController;
use App\Http\Controllers\Master\UnitController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\WorkController;
use App\Http\Controllers\Master\EmployeeController;
use App\Http\Controllers\Master\ProtectiveEquipController;
use App\Http\Controllers\Master\EquipInvalveController;
use App\Http\Controllers\Master\SafeWorkController;
use App\Http\Controllers\Master\PrecautionController;
use App\Http\Controllers\Master\ChecklistController;
use App\Http\Controllers\Master\TypeofWorkController;
use App\Http\Controllers\Ppemanagement\PpeExemptionController;
use App\Http\Controllers\Master\WorkerLogController;
use App\Http\Controllers\Master\EmployeeLogController;
use App\Http\Controllers\Ppemanagement\PpeRequestController;
use App\Http\Controllers\Ppemanagement\PpeStockInventoryController;
use App\Http\Controllers\Safetypermit\SafetyPermitController;

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

Route::get('queueCompanyImport', [CronController::class, 'queueCompanyImport']);
Route::get('queuelocationimport', [CronController::class, 'queuelocationimport']);
Route::get('queueunitimport', [CronController::class, 'queueunitimport']);
Route::get('queueDepartmentuplodimport', [CronController::class, 'queueDepartmentuplodimport']);



Route::get('stockitem', [CronController::class, 'storeItem']);
Route::get('workmastertemp', [CronController::class, 'workMasterTemp']);
Route::get('worksave', [CronController::class, 'workSave']);
Route::get('employee_master_temp', [CronController::class, 'employeeMasterTemp']);
Route::get('employee_save', [CronController::class, 'EmployeeSave']);
Route::get('permit_expiry', [CronController::class, 'permitExpiry']);


Route::get('test', [TestController::class,  'index']);




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
        Route::post('logintry', [LoginController::class, 'authenticate'])->middleware('loginattempt');
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
            Route::get('administration/users/add', [UserController::class, 'add']);
            Route::post('administration/users/add/submit',  [UserController::class, 'store']);
            Route::get('administration/users/view/{id}', [UserController::class, 'view']);
            Route::get('administration/users/edit/{id}',  [UserController::class, 'edit']);
            Route::post('administration/users/edit/submit', [UserController::class, 'update']);
            Route::post('administration/users/unique', [UserController::class, 'uniqueCheck']);
            Route::post('administration/users/status', [UserController::class, 'statusChange']);
            Route::post('administration/users/delete',  [UserController::class, 'delete']);
            Route::get('administration/users/export/excel', [UserController::class, 'exportExcel']);
            Route::get('administration/users/export/pdf', [UserController::class, 'exportPdf']);
            Route::get('administration/users/import', [UserController::class, 'import']);
            Route::post('administration/users/import/submit', [UserController::class, 'importSubmit']);
            Route::get('administration/users/list/{companyId}', [UserController::class, 'list']);
            Route::get('administration/users/sampledownload', [UserController::class, 'DownloadSample']);
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
            Route::get('location/ajax-list/{companyId}/{id}', [LocationController::class, 'list']);
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
                Route::get('/ajax-list/{locationId}/{id}', [UnitController::class, 'list']);
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
                Route::get('/ajax-list/{unit_id}/{id}', [DepartmentController::class, 'list']);
                Route::get('/multiple-ajax-list/{unit_id}', [DepartmentController::class, 'multipleList']);
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
                Route::post('/status', [WorkController::class, 'statusChange']);
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
                Route::post('/status', [EmployeeController::class, 'statusChange']);
                Route::post('/unique', [EmployeeController::class, 'Uniquecheck']);
            });

            Route::group(['prefix' => 'ptw/protectiveequipmentmaster'], function () {
                Route::get('/list', [ProtectiveEquipController::class, 'index']);
                Route::post('/list', [ProtectiveEquipController::class, 'index']);
                Route::get('/add', [ProtectiveEquipController::class, 'add']);
                Route::post('/add/submit', [ProtectiveEquipController::class, 'store']);
                Route::get('/edit/{id}', [ProtectiveEquipController::class, 'edit']);
                Route::post('/edit/submit', [ProtectiveEquipController::class, 'update']);
                Route::get('/view/{id}', [ProtectiveEquipController::class, 'view']);
                Route::post('/delete', [ProtectiveEquipController::class, 'delete']);
                Route::get('/export/excel', [ProtectiveEquipController::class, 'exportExcel']);
                Route::get('/export/pdf', [ProtectiveEquipController::class, 'exportPdf']);
                Route::get('/sampledownload', [ProtectiveEquipController::class, 'DownloadSample']);
                Route::get('/import', [ProtectiveEquipController::class, 'import']);
                Route::post('/import/submit', [ProtectiveEquipController::class, 'importSubmit']);
                Route::post('/status', [ProtectiveEquipController::class, 'statusChange']);
                Route::post('/unique', [ProtectiveEquipController::class, 'Uniquecheck']);
            });



            Route::group(['prefix' => 'ptw/equipinvolvemaster'], function () {
                Route::get('/list', [EquipInvalveController::class, 'index']);
                Route::post('/list', [EquipInvalveController::class, 'index']);
                Route::get('/add', [EquipInvalveController::class, 'add']);
                Route::post('/add/submit', [EquipInvalveController::class, 'store']);
                Route::get('/edit/{id}', [EquipInvalveController::class, 'edit']);
                Route::post('/edit/submit', [EquipInvalveController::class, 'update']);
                Route::get('/view/{id}', [EquipInvalveController::class, 'view']);
                Route::post('/delete', [EquipInvalveController::class, 'delete']);
                Route::get('/export/excel', [EquipInvalveController::class, 'exportExcel']);
                Route::get('/export/pdf', [EquipInvalveController::class, 'exportPdf']);
                Route::get('/sampledownload', [EquipInvalveController::class, 'DownloadSample']);
                Route::get('/import', [EquipInvalveController::class, 'import']);
                Route::post('/import/submit', [EquipInvalveController::class, 'importSubmit']);
                Route::post('/status', [EquipInvalveController::class, 'statusChange']);
                Route::post('/unique', [EquipInvalveController::class, 'Uniquecheck']);
            });


            Route::group(['prefix' => 'ptw/safeworkmaster'], function () {
                Route::get('/list', [SafeWorkController::class, 'index']);
                Route::post('/list', [SafeWorkController::class, 'index']);
                Route::get('/add', [SafeWorkController::class, 'add']);
                Route::post('/add/submit', [SafeWorkController::class, 'store']);
                Route::get('/edit/{id}', [SafeWorkController::class, 'edit']);
                Route::post('/edit/submit', [SafeWorkController::class, 'update']);
                Route::get('/view/{id}', [SafeWorkController::class, 'view']);
                Route::post('/delete', [SafeWorkController::class, 'delete']);
                Route::get('/export/excel', [SafeWorkController::class, 'exportExcel']);
                Route::get('/export/pdf', [SafeWorkController::class, 'exportPdf']);
                Route::get('/sampledownload', [SafeWorkController::class, 'DownloadSample']);
                Route::get('/import', [SafeWorkController::class, 'import']);
                Route::post('/import/submit', [SafeWorkController::class, 'importSubmit']);
                Route::post('/status', [SafeWorkController::class, 'statusChange']);
                Route::post('/unique', [SafeWorkController::class, 'Uniquecheck']);
            });

            /**
             * Topic master
             */
            Route::group(['prefix' => 'topic'], function () {
                Route::get('/list', [TopicController::class, 'index'])->middleware('role:topic,view');
                Route::post('/list', [TopicController::class, 'index'])->middleware('role:topic,view');
                Route::get('/add', [TopicController::class, 'add'])->middleware('role:topic,add');
                Route::post('/add/submit', [TopicController::class, 'store']);
                Route::get('/edit/{id}', [TopicController::class, 'edit'])->middleware('role:topic,edit');
                Route::post('/edit/submit', [TopicController::class, 'update']);
                Route::get('/view/{id}', [TopicController::class, 'view'])->middleware('role:topic,view');
                Route::post('/delete', [TopicController::class, 'delete'])->middleware('role:topic,delete');
                Route::get('/export/excel', [TopicController::class, 'exportExcel']);
                Route::get('/export/pdf', [TopicController::class, 'exportPdf']);
                Route::get('/sampledownload', [TopicController::class, 'DownloadSample']);
                Route::get('/import', [TopicController::class, 'import'])->middleware('role:topic,import');
                Route::post('/import/submit', [TopicController::class, 'importSubmit']);
                Route::post('/status', [TopicController::class, 'statusChange']);
                Route::post('/unique', [TopicController::class, 'Uniquecheck']);
                Route::get('/sampledownload', [TopicController::class, 'DownloadSample']);

            });

            /**
             * Venue master
             */
            Route::group(['prefix' => 'venue'], function () {
                Route::get('/list', [VenueController::class, 'index'])->middleware('role:venue,view');
                Route::post('/list', [VenueController::class, 'index'])->middleware('role:venue,view');
                Route::get('/add', [VenueController::class, 'add'])->middleware('role:venue,add');
                Route::post('/add/submit', [VenueController::class, 'store']);
                Route::get('/edit/{id}', [VenueController::class, 'edit'])->middleware('role:venue,edit');
                Route::post('/edit/submit', [VenueController::class, 'update']);
                Route::get('/view/{id}', [VenueController::class, 'view'])->middleware('role:venue,view');
                Route::post('/delete', [VenueController::class, 'delete'])->middleware('role:venue,delete');
                Route::get('/export/excel', [VenueController::class, 'exportExcel']);
                Route::get('/export/pdf', [VenueController::class, 'exportPdf']);
                Route::get('/sampledownload', [VenueController::class, 'DownloadSample']);
                Route::get('/import', [VenueController::class, 'import'])->middleware('role:venue,import');
                Route::post('/import/submit', [VenueController::class, 'importSubmit']);
                Route::post('/status', [VenueController::class, 'statusChange']);
                Route::post('/unique', [VenueController::class, 'Uniquecheck']);
            });

            /**
             * Training Matrix
             */
            Route::group(['prefix' => 'training_matrix'], function () {
                Route::get('/list', [TrainingMatrixController::class, 'index'])->middleware('role:training_matrix,view');
                Route::post('/list', [TrainingMatrixController::class, 'index'])->middleware('role:training_matrix,view');
                Route::get('/add', [TrainingMatrixController::class, 'add'])->middleware('role:training_matrix,add');
                Route::post('/add/submit', [TrainingMatrixController::class, 'store']);
                Route::get('/edit/{id}', [TrainingMatrixController::class, 'edit'])->middleware('role:training_matrix,edit');
                Route::post('/edit/submit', [TrainingMatrixController::class, 'update']);
                Route::get('/view/{id}', [TrainingMatrixController::class, 'view'])->middleware('role:training_matrix,view');
                Route::post('/delete', [TrainingMatrixController::class, 'delete'])->middleware('role:training_matrix,delete');
                Route::get('/export/excel', [TrainingMatrixController::class, 'exportExcel']);
                Route::get('/export/pdf', [TrainingMatrixController::class, 'exportPdf']);
                Route::get('/sampledownload', [TrainingMatrixController::class, 'DownloadSample']);
                Route::get('/import', [TrainingMatrixController::class, 'import'])->middleware('role:training_matrix,import');
                Route::post('/import/submit', [TrainingMatrixController::class, 'importSubmit']);
                Route::post('/status', [TrainingMatrixController::class, 'statusChange']);
                Route::get('/topic/ajax-list/{topicId}/{trainerId}', [TrainingMatrixController::class, 'Uniquecheck']);
                Route::get('/topic/ajax-list', [TrainingMatrixController::class, 'uniquecheckTrainingMatrix']);



            });

            /**
             * Training  Schedule
             */
            Route::group(['prefix' => 'training_schedule'], function () {
                Route::get('/list', [TrainingScheduleController::class, 'index'])->middleware('role:training_schedule,view');
                Route::post('/list', [TrainingScheduleController::class, 'index'])->middleware('role:training_schedule,view');
                Route::get('/add', [TrainingScheduleController::class, 'add'])->middleware('role:training_schedule,add');
                Route::post('/add/submit', [TrainingScheduleController::class, 'store']);
                Route::get('/edit/{id}', [TrainingScheduleController::class, 'edit'])->middleware('role:training_schedule,edit');
                Route::post('/edit/submit', [TrainingScheduleController::class, 'update']);
                Route::get('/view/{id}', [TrainingScheduleController::class, 'view'])->middleware('role:training_schedule,view');
                Route::get('/export/excel', [TrainingScheduleController::class, 'exportExcel']);
                Route::get('/export/pdf', [TrainingScheduleController::class, 'exportPdf']);
                Route::post('/delete', [TrainingScheduleController::class, 'delete'])->middleware('role:training_schedule,delete');
                Route::get('/sampledownload', [TrainingScheduleController::class, 'DownloadSample']);
                Route::get('/import', [TrainingScheduleController::class, 'import'])->middleware('role:training_schedule,import');
                Route::post('/import/submit', [TrainingScheduleController::class, 'importSubmit']);
                Route::post('/status', [TrainingScheduleController::class, 'statusChange']);
                Route::get('/topic/ajax-list', [TrainingScheduleController::class, 'Uniquecheck']);
                Route::get('/nominationProcess/{id}', [TrainingScheduleController::class, 'nominationProcess']);
                Route::get('/start/{id}', [TrainingScheduleController::class, 'startTraining']);
                Route::get('/end/{id}', [TrainingScheduleController::class, 'endTraining']);
                Route::get('/attendance/{id}', [TrainingScheduleController::class, 'attendance']);
                Route::post('/attendance/submit', [TrainingScheduleController::class, 'storeAttendance']);
                Route::post('/attendance/unique', [TrainingScheduleController::class, 'checkUniqueAttendanceDate']);
                Route::post('/nomination_process/unique', [TrainingScheduleController::class, 'checkUniqueNomination']);
                Route::get('/pdf/{id}', [TrainingScheduleController::class, 'exportViewPdf']);


            });


            /**
             * Training Calendar
             */

            Route::prefix('training_calendar')->group(function () {
                Route::get('/list', [TrainingCalendarController::class, 'index'])->middleware('role:training_calendar,view');
                Route::get('/fetch/schedule', [TrainingCalendarController::class, 'trainingShow']);
                Route::put('/update/{id}', [TrainingCalendarController::class, 'updateEvent']);
            });


            /**
             * Nomination Process
             */
            Route::group(['prefix' => 'nomination_process'], function () {
                Route::get('/list', [NominationProcessController::class, 'index']);
                Route::post('/list', [NominationProcessController::class, 'index']);
                Route::post('/add/submit', [NominationProcessController::class, 'store']);
                Route::get('/export/excel', [NominationProcessController::class, 'exportExcel']);
                Route::get('/export/pdf', [NominationProcessController::class, 'exportPdf']);
                Route::get('/sampledownload', [NominationProcessController::class, 'DownloadSample']);
                Route::get('/import/{training_schedule_id}/{trainer_id}', [NominationProcessController::class, 'import']);
                Route::post('/import/submit', [NominationProcessController::class, 'importSubmit']);
                Route::post('/status', [NominationProcessController::class, 'statusChange']);
                Route::post('/unique', [NominationProcessController::class, 'Uniquecheck']);
                Route::delete('/delete/{id}', [NominationProcessController::class, 'delete']);
                Route::get('/view/{id}', [NominationProcessController::class, 'view']);
                Route::get('fetchEmployeeDetails/{emp_id}', [NominationProcessController::class, 'fetchEmployeeDetails']);
            });



            Route::group(['prefix' => 'ptw/precautionmaster'], function () {
                Route::get('/list', [PrecautionController::class, 'index']);
                Route::post('/list', [PrecautionController::class, 'index']);
                Route::get('/add', [PrecautionController::class, 'add']);
                Route::post('/add/submit', [PrecautionController::class, 'store']);
                Route::get('/edit/{id}', [PrecautionController::class, 'edit']);
                Route::post('/edit/submit', [PrecautionController::class, 'update']);
                Route::get('/view/{id}', [PrecautionController::class, 'view']);
                Route::post('/delete', [PrecautionController::class, 'delete']);
                Route::get('/export/excel', [PrecautionController::class, 'exportExcel']);
                Route::get('/export/pdf', [PrecautionController::class, 'exportPdf']);
                Route::get('/sample_download', [PrecautionController::class, 'DownloadSample']);
                Route::get('/import', [PrecautionController::class, 'import']);
                Route::post('/import/submit', [PrecautionController::class, 'importSubmit']);
                Route::post('/status', [PrecautionController::class, 'statusChange']);
                Route::post('/unique', [PrecautionController::class, 'Uniquecheck']);
            });



            Route::group(['prefix' => 'ptw/checklistmaster'], function () {
                Route::get('/list', [ChecklistController::class, 'index']);
                Route::post('/list', [ChecklistController::class, 'index']);
                Route::get('/add', [ChecklistController::class, 'add']);
                Route::post('/add/submit', [ChecklistController::class, 'store']);
                Route::get('/edit/{id}', [ChecklistController::class, 'edit']);
                Route::post('/edit/submit', [ChecklistController::class, 'update']);
                Route::get('/view/{id}', [ChecklistController::class, 'view']);
                Route::post('/delete', [ChecklistController::class, 'delete']);
                Route::get('/export/excel', [ChecklistController::class, 'exportExcel']);
                Route::get('/export/pdf', [ChecklistController::class, 'exportPdf']);
                Route::get('/sample_download', [ChecklistController::class, 'DownloadSample']);
                Route::get('/import', [ChecklistController::class, 'import']);
                Route::post('/import/Submit', [ChecklistController::class, 'importSubmit']);
                Route::post('/status', [ChecklistController::class, 'statusChange']);
                Route::post('/unique', [ChecklistController::class, 'Uniquecheck']);
            });


            Route::group(['prefix' => 'ptw/typeofworkmaster'], function () {
                Route::get('/list', [TypeofWorkController::class, 'index']);
                Route::post('/list', [TypeofWorkController::class, 'index']);
                Route::get('/add', [TypeofWorkController::class, 'add']);
                Route::post('/add/submit', [TypeofWorkController::class, 'store']);
                Route::get('/edit/{id}', [TypeofWorkController::class, 'edit']);
                Route::post('/edit/submit', [TypeofWorkController::class, 'update']);
                Route::get('/view/{id}', [TypeofWorkController::class, 'view']);
                Route::post('/delete', [TypeofWorkController::class, 'delete']);
                Route::get('/export/excel', [TypeofWorkController::class, 'exportExcel']);
                Route::get('/export/pdf', [TypeofWorkController::class, 'exportPdf']);
                Route::get('/sample_download', [TypeofWorkController::class, 'DownloadSample']);
                Route::get('/import', [TypeofWorkController::class, 'import']);
                Route::post('/import/Submit', [TypeofWorkController::class, 'importSubmit']);
                Route::post('/status', [TypeofWorkController::class, 'statusChange']);
                Route::post('/unique', [TypeofWorkController::class, 'Uniquecheck']);
            });


            Route::group(['prefix' => 'ppe_type'], function () {

                Route::get('/list', [PpeTypeController::class, 'index']);
                Route::post('/list', [PpeTypeController::class, 'index']);
                Route::get('/add', [PpeTypeController::class, 'add']);
                Route::post('/add/submit', [PpeTypeController::class, 'store']);
                Route::get('/view/{id}', [PpeTypeController::class, 'view']);
                Route::get('/edit/{id}', [PpeTypeController::class, 'edit']);
                Route::post('/edit/submit', [PpeTypeController::class, 'update']);
                Route::post('/status', [PpeTypeController::class, 'statusChange']);
                Route::post('/delete', [PpeTypeController::class, 'delete']);
                Route::post('/unique', [PpeTypeController::class, 'Uniquecheck']);
                Route::get('/sample_download', [PpeTypeController::class, 'DownloadSample']);
                Route::get('/import', [PpeTypeController::class, 'import']);
                Route::post('/import/Submit', [PpeTypeController::class, 'importSubmit']);
                Route::get('/export/excel', [PpeTypeController::class, 'exportExcel']);
                Route::get('/export/pdf', [PpeTypeController::class, 'exportPdf']);
            });



            Route::get('ppe_ppetype_master/list', [PpeTypeMasterController::class, 'index']);
            Route::post('ppe_ppetype_master/list', [PpeTypeMasterController::class, 'index']);
            Route::get('ppe_ppetype_master/add', [PpeTypeMasterController::class, 'add']);
            Route::post('ppe_ppetype_master/add/submit', [PpeTypeMasterController::class, 'store']);
            Route::get('ppe_ppetype_master/view/{id}', [PpeTypeMasterController::class, 'view']);
            Route::get('ppe_ppetype_master/edit/{id}', [PpeTypeMasterController::class, 'edit']);
            Route::post('ppe_ppetype_master/edit/submit', [PpeTypeMasterController::class, 'update']);
            Route::post('ppe_ppetype_master/status', [PpeTypeMasterController::class, 'statusChange']);
            Route::post('ppe_ppetype_master/delete', [PpeTypeMasterController::class, 'delete']);
            Route::get('ppe_ppetype_master/export/excel', [PpeTypeMasterController::class, 'exportExcel']);
            Route::get('ppe_ppetype_master/export/pdf', [PpeTypeMasterController::class, 'exportPdf']);
            Route::get('ppe_ppetype_master/ajax-list', [PpeTypeMasterController::class, 'list']);
            Route::get('ppe_ppetype_master/ajax-ppename', [PpeTypeMasterController::class, 'PPEnamelist']);
            Route::get('ppe_ppetype_master/unique', [PpeTypeMasterController::class, 'Uniquecheck']);
            Route::get('ppe_ppetype_master/ppename/unique', [PpeTypeMasterController::class, 'PPEUniquecheck']);





            Route::group(['prefix' => 'ppe_request'], function () {

                Route::get('/list', [PpeRequestController::class, 'index']);
                Route::post('/list', [PpeRequestController::class, 'index']);
                Route::get('/add', [PpeRequestController::class, 'add']);
                Route::post('/add/submit', [PpeRequestController::class, 'store']);
                Route::post('/status', [PpeRequestController::class, 'statusChange']);
                Route::post('/delete', [PpeRequestController::class, 'delete']);
                Route::get('/view/{id}', [PpeRequestController::class, 'view']);
                Route::get('/hodapproval/view/{id}', [PpeRequestController::class, 'hodApprovalview']);
                Route::post('/hodapprovereject/submit', [PpeRequestController::class, 'storehodapproval']);
                Route::get('/ehsapproval/view/{id}', [PpeRequestController::class, 'ehsApprovalview']);
                Route::post('/ehsapprovereject/submit', [PpeRequestController::class, 'storeehsapproval']);
                Route::get('smapproval/submit/{item_code}/{action}', [PpeRequestController::class, 'smapproval']);
                Route::get('/statuslog/{id}', [PpeRequestController::class, 'statuslog']);
                Route::get('/edit/{id}', [PpeRequestController::class, 'edit']);
                Route::post('/edit/submit', [PpeRequestController::class, 'update']);
                Route::get('/export/excel', [PpeRequestController::class, 'exportExcel']);
                Route::get('/export/pdf', [PpeRequestController::class, 'exportPdf']);
                Route::get('/generalpdf/{id}', [PpeRequestController::class, 'pdf']);

            });

            Route::group(['prefix' => 'ppe_stock_inventory'], function () {

                Route::get('/list', [PpeStockInventoryController::class, 'index']);
                Route::post('/list', [PpeStockInventoryController::class, 'index']);
                Route::post('/status', [PpeStockInventoryController::class, 'statusChange']);
                Route::get('/view/{id}', [PpeStockInventoryController::class, 'view']);
                Route::get('/edit/{id}', [PpeStockInventoryController::class, 'edit']);
                Route::post('/status', [PpeStockInventoryController::class, 'statusChange']);
                Route::post('/edit/submit', [PpeStockInventoryController::class, 'update']);
                Route::get('/export/excel', [PpeStockInventoryController::class, 'exportExcel']);
                Route::get('/export/pdf', [PpeStockInventoryController::class, 'exportPdf']);
            });

            Route::group(['prefix' => 'safetypermit'], function () {

                Route::get('/list', [SafetyPermitController::class, 'index']);
                Route::post('/list', [SafetyPermitController::class, 'index']);
                Route::get('/add', [SafetyPermitController::class, 'add']);
                Route::post('/add/submit', [SafetyPermitController::class, 'store']);
                Route::post('/status', [SafetyPermitController::class, 'statusChange']);
                Route::post('/delete', [SafetyPermitController::class, 'delete']);
                Route::get('/view/{id}', [SafetyPermitController::class, 'view']);
                Route::get('/approvereject/{id}', [SafetyPermitController::class, 'approvereject']);
                Route::post('/ehsverification/submit', [SafetyPermitController::class, 'ehsverification']);
                Route::post('/ehsapproval/submit', [SafetyPermitController::class, 'ehsapproval']);
                Route::post('/plantheadapproval/submit', [SafetyPermitController::class, 'plantheadapproval']);
                Route::get('/permitExtension/{id}', [SafetyPermitController::class, 'permitExtension']);
                Route::post('/permitExtension/submit', [SafetyPermitController::class, 'permitExtensionsubmit']);
                Route::get('/edit/{id}', [SafetyPermitController::class, 'edit']);
                Route::post('/edit/submit', [SafetyPermitController::class, 'update']);
                Route::get('/export/excel', [SafetyPermitController::class, 'exportExcel']);
                Route::get('/export/pdf', [SafetyPermitController::class, 'exportPdf']);
                Route::get('/generalpdf/{id}', [SafetyPermitController::class, 'generalpdf']);
                Route::get('/getprotectivechecklist/{workId}', [SafetyPermitController::class, 'getprotectivechecklist']);
                Route::get('/getequipmentinvolved/{workId}', [SafetyPermitController::class, 'getequipmentinvolved']);
                Route::get('/getprecaution/{workId}', [SafetyPermitController::class, 'getprecaution']);
                Route::get('/getchecklist/{workId}', [SafetyPermitController::class, 'getchecklist']);
                Route::get('/getinstruction/{workId}', [SafetyPermitController::class, 'getinstruction']);
                Route::get('/employeename', [SafetyPermitController::class, 'employeename']);
                Route::get('/reassignemployeename', [SafetyPermitController::class, 'reassignemployeename']);
                Route::get('/employeeid', [SafetyPermitController::class, 'employeeid']);
                Route::get('/fetchEmployeeDetails/{emp_id}', [SafetyPermitController::class, 'fetchEmployeeDetails']);
                Route::get('/qr/pdf/{id}',[SafetyPermitController::class,'permitQRPDF']);
                Route::get('/join/{id}',[SafetyPermitController::class,'permit_join']);

            });
            Route::group(['prefix' => 'ppe_exemption'], function () {

                Route::get('/list', [PpeExemptionController::class, 'index']);
                Route::post('/list', [PpeExemptionController::class, 'index']);
                Route::get('/add', [PpeExemptionController::class, 'add']);
                Route::post('/add/submit', [PpeExemptionController::class, 'store']);
                Route::post('/status', [PpeExemptionController::class, 'statusChange']);
                Route::post('/delete', [PpeExemptionController::class, 'delete']);
                Route::get('/view/{id}', [PpeExemptionController::class, 'view']);
                Route::get('/approval/view/{id}', [PpeExemptionController::class, 'ApprovalReject']);
                Route::post('/approvereject/submit', [PpeExemptionController::class, 'storeapprovereject']);
                Route::get('/edit/{id}', [PpeExemptionController::class, 'edit']);
                Route::post('/edit/submit', [PpeExemptionController::class, 'update']);
                Route::get('/export/excel', [PpeExemptionController::class, 'exportExcel']);
                Route::get('/export/pdf', [PpeExemptionController::class, 'exportPdf']);
                Route::get('/generalpdf/{id}', [PpeExemptionController::class, 'pdf']);

            });
        });
    });
});
