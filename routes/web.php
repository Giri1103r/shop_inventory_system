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
use App\Http\Controllers\OhcManagement\Master\CertifiedFirstAiderController;
use App\Http\Controllers\OhcManagement\Master\EmployeecumPatientController;
use App\Http\Controllers\OhcManagement\Master\FirstAidLocationController;
use App\Http\Controllers\OhcManagement\Master\MedicineController;
use App\Http\Controllers\OhcManagement\Master\VendorController;
use App\Http\Controllers\OhcManagement\MedicineIssuanceController;
use App\Http\Controllers\OhcManagement\MedicineReceivingController;
use App\Http\Controllers\OhcManagement\MedicineRequisitionController;
use App\Http\Controllers\OhcManagement\MedicineStockController;
use App\Http\Controllers\OhcManagement\Opd\FirstAidController;
use App\Http\Controllers\OhcManagement\Opd\PrescribetoPatientController;
use App\Http\Controllers\OhcManagement\Opd\RoadsideFirstAidController;
use App\Http\Controllers\Ppemanagement\DashboardController;
use App\Http\Controllers\Ppemanagement\PpeRequestController;
use App\Http\Controllers\Ppemanagement\PpeStockInventoryController;
use App\Http\Controllers\Safetypermit\SafetyPermitController;
use App\Http\Controllers\Training\TrainingController;
use App\Http\Controllers\IMS\Master\IncidentTypeController;
use App\Http\Controllers\IMS\Master\HiraController;
use App\Http\Controllers\IMS\Incident\InitialIncidentController;
use App\Http\Controllers\IMS\Incident\AccidentReportController;
use App\Http\Controllers\OhcManagement\MedicineFirstAidController;
use App\Http\Controllers\OhcManagement\DiscardController;
use App\Http\Controllers\OhcManagement\Report\InventoryController;
use App\Http\Controllers\OhcManagement\Report\MedicineExpireController;
use App\Http\Controllers\OhcManagement\Report\MonthlyInventoryController;

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

Route::get('cron/training/nomination-process/import', [CronController::class, 'queueNominationProcessImport']);
Route::get('cron/training/training-schedule/import', [CronController::class, 'queueTrainingScheduleImport']);
Route::get('cron/training/training-matrix/import', [CronController::class, 'queueTrainingMatrixImport']);
Route::get('cron/training/master/topic/import', [CronController::class, 'queueTopicImport']);
Route::get('cron/training/master/venue/import', [CronController::class, 'queueVenueImport']);
Route::get('queueCompanyImport', [CronController::class, 'queueCompanyImport']);
Route::get('queuelocationimport', [CronController::class, 'queuelocationimport']);
Route::get('queueunitimport', [CronController::class, 'queueunitimport']);
Route::get('queueDepartmentuplodimport', [CronController::class, 'queueDepartmentuplodimport']);

Route::get('cron/safetypermit/protectiveequipmentmaster/import', [CronController::class, 'queueProtectiveequipmentmasterImport']);
Route::get('cron/safetypermit/equipinvolvemaster/import', [CronController::class, 'queueEquipinvolvemasterImport']);
Route::get('cron/safetypermit/safeworkmaster/import', [CronController::class, 'queueSafeworkmasterImport']);
Route::get('cron/safetypermit/precautionmaster/import', [CronController::class, 'queuePrecautionmasterImport']);
Route::get('cron/safetypermit/checklistmaster/import', [CronController::class, 'queueChecklistmasterImport']);



Route::get('stockitem', [CronController::class, 'storeItem']);
Route::get('expireexemptionstatus', [CronController::class, 'ExpireExemption']);
Route::get('updateStockitem', [CronController::class, 'updateItem']);
Route::get('cron/master/work/all-details-temp', [CronController::class, 'workMasterAllDetailsTemp']);
Route::get('cron/master/workmastertemp', [CronController::class, 'workMasterTemp']);
Route::get('cron/master/worksave', [CronController::class, 'workSave']);
Route::get('cron/master/employee/all-details-temp', [CronController::class, 'employeeMasterTempAllDetails']);
Route::get('cron/master/employee/temp-details', [CronController::class, 'employeeMasterTemp']);
Route::get('cron/master/employee_save', [CronController::class, 'EmployeeSave']);
Route::get('permit_expiry', [CronController::class, 'permitExpiry']);
Route::get('permit_close', [CronController::class, 'permitClose']);
Route::get('stockrequest', [CronController::class, 'stockrequest']);
Route::get('stockupdate', [CronController::class, 'stockupdate']);

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

            Route::get('training/dashboard', [TrainingController::class, 'index']);
            Route::get('training/trainingStatus', [TrainingController::class, 'getTrainingStatus']);
            Route::get('training/dashboard/department', [TrainingController::class, 'getDepartment']);
            Route::get('training/dashboard/monthwisetraining', [TrainingController::class, 'getmonthwiseTraining']);
            Route::get('training/dashboard/trainingStatusCount', [TrainingController::class, 'gettrainingStatusCount']);

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
                Route::get('/passwordchange/{id}', [EmployeeController::class, 'PasswordUpdate']);
                Route::post('/passwordchange/submit', [EmployeeController::class, 'PasswordUp`dateSubmit']);
                Route::post('/company-ajax', [EmployeeController::class, 'companyajax']);
                Route::get('/ajax-list/{unit_id}/{id}', [EmployeeController::class, 'list']);
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
                Route::get('/ajax-list/{unit_id}/{id}', [VenueController::class, 'list']);
                Route::get('/alllist/{unitId}', [VenueController::class, 'alllist']);
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
                Route::get('/ehs_approval/{id}', [TrainingScheduleController::class, 'vpApproval']);
                Route::post('/ehs_approval/submit', [TrainingScheduleController::class, 'vpApprovalStore']);
                Route::get('/nominationProcess/{id}', [TrainingScheduleController::class, 'nominationProcess']);
                Route::get('/start/{id}', [TrainingScheduleController::class, 'startTraining']);
                Route::get('/end/{id}', [TrainingScheduleController::class, 'endTraining']);
                Route::post('/training_end/submit', [TrainingScheduleController::class, 'endTrainingStore']);
                Route::get('/attendance/{id}', [TrainingScheduleController::class, 'attendance']);
                Route::post('/attendance/submit', [TrainingScheduleController::class, 'storeAttendance']);
                Route::post('/attendance/unique', [TrainingScheduleController::class, 'checkUniqueAttendanceDate']);
                Route::post('/nomination_process/unique', [TrainingScheduleController::class, 'checkUniqueNomination']);
                Route::get('/pdf/{id}', [TrainingScheduleController::class, 'exportViewPdf']);
                Route::get('/certificate/{training_schedule_id}/{id}', [TrainingScheduleController::class, 'certificateView']);
                Route::get('/filterAttendance', [TrainingScheduleController::class, 'filterAttendance'])->name('training_schedule.filterAttendance');
            });
            Route::get('training/feedback_approve/{id}', [TrainingScheduleController::class, 'adminApprove']);
            Route::post('training/feedback_approve/submit', [TrainingScheduleController::class, 'adminfeedbackApprove']);
            Route::get('training/feedback_link/{id}/{training_schedule_id}', [TrainingScheduleController::class, 'feedbackLinkPage']);
            Route::post('training/feedback_link/submit', [TrainingScheduleController::class, 'feedbackLinkSubmit']);
            Route::get('training/worker/feedback_link/{training_schedule_id}', [TrainingScheduleController::class, 'workerFeedbackLinkPage']);
            Route::post('training/worker/feedback_link/submit', [TrainingScheduleController::class, 'workerFeedbackLinkSubmit']);
            Route::post('training/workerId/unique', [TrainingScheduleController::class, 'checkUniqueworkerId']);


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
                Route::get('/fetchEmployeeDetails/{emp_id}', [NominationProcessController::class, 'fetchEmployeeDetails']);
                Route::get('/fetchEmployeeOrWorkerList/{type}/{deptID}/{training_schedule_id}', [NominationProcessController::class, 'fetchEmployeeOrWorkerList']);
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
                Route::get('/checkuserDepartment', [PpeRequestController::class, 'checkDepartmentrequest']);
                Route::post('/storemanger/issued', [PpeRequestController::class, 'storemanagerapproval']);
                Route::get('/fetchEmployeeDetails/{emp_id}', [PpeRequestController::class, 'fetchEmployeeDetails']);
                Route::get('/employeeid', [PpeRequestController::class, 'employeeid']);
                Route::get('/employee', [PpeRequestController::class, 'employee']);
                Route::get('/employeename', [PpeRequestController::class, 'employeename']);
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
                Route::get('/ajax-list', [PpeStockInventoryController::class, 'list']);
            });

            Route::group(['prefix' => 'safetypermit'], function () {

                Route::get('/list', [SafetyPermitController::class, 'index']);
                Route::post('/list', [SafetyPermitController::class, 'index']);
                Route::get('/add', [SafetyPermitController::class, 'add']);
                Route::post('/add/submit', [SafetyPermitController::class, 'store']);
                Route::post('/status', [SafetyPermitController::class, 'statusChange']);
                Route::post('/delete', [SafetyPermitController::class, 'delete']);
                Route::post('/close', [SafetyPermitController::class, 'close']);
                Route::get('/view/{id}', [SafetyPermitController::class, 'view']);
                Route::get('/approvereject/{id}', [SafetyPermitController::class, 'approvereject']);
                Route::post('/ehsverification/submit', [SafetyPermitController::class, 'ehsverification']);
                Route::post('/ehsapproval/submit', [SafetyPermitController::class, 'ehsapproval']);
                Route::post('/plantheadapproval/submit', [SafetyPermitController::class, 'plantheadapproval']);
                Route::get('/permitExtension/{id}', [SafetyPermitController::class, 'permitExtension']);
                Route::post('/permitExtension/submit', [SafetyPermitController::class, 'permitExtensionsubmit']);
                Route::post('/permitextensionapproval/submit', [SafetyPermitController::class, 'permitextensionapproval']);
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
                Route::get('/workername', [SafetyPermitController::class, 'workername']);
                Route::get('/visitorid', [SafetyPermitController::class, 'visitorid']);
                Route::get('/workerid', [SafetyPermitController::class, 'workerid']);
                Route::get('/visitorname', [SafetyPermitController::class, 'visitorname']);
                Route::get('/reassignemployeename', [SafetyPermitController::class, 'reassignemployeename']);
                Route::get('/employeeid', [SafetyPermitController::class, 'employeeid']);
                Route::get('/fetchEmployeeDetails/{emp_id}', [SafetyPermitController::class, 'fetchEmployeeDetails']);
                Route::get('/qr/pdf/{id}', [SafetyPermitController::class, 'permitQRPDF']);
                Route::get('/join/{id}', [SafetyPermitController::class, 'permit_join']);
                Route::get('/dashboard', [SafetyPermitController::class, 'dashboard']);
                Route::get('/dashboard/unitwiseptw', [SafetyPermitController::class, 'unitwiseptw']);
                Route::get('/dashboard/monthwiseptw', [SafetyPermitController::class, 'monthwiseptw']);
                Route::get('/dashboard/getpermitstatus', [SafetyPermitController::class, 'getPermitStatus']);
                Route::post('/statusExpire', [SafetyPermitController::class, 'ExpireStatus']);
                Route::post('/deleteworkmaninvolved/{rowId}', [SafetyPermitController::class, 'deleteworkmaninvolved']);
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
                Route::get('/ajax-list', [PpeExemptionController::class, 'list']);
            });


            Route::get('ppe-dashboard', [DashboardController::class, 'login']);
            Route::get('ppe-dashboard/ppeExemption', [DashboardController::class, 'ppeexemptiondata']);
            Route::get('ppe-dashboard/ppeRequestList', [DashboardController::class, 'ppeRequestList']);
            Route::get('ppe-dashboard/getshoerequeststatus', [DashboardController::class, 'getshoerequeststatus']);
            Route::get('ppe-dashboard/getExemptionstatus', [DashboardController::class, 'getExemptionstatus']);
            Route::get('ppe-dashboard/getmonthwiseRequest', [DashboardController::class, 'getmonthwiseRequest']);
            Route::get('ppe-dashboard/getmonthwiseExemption', [DashboardController::class, 'getmonthwiseExemption']);


            // OHC Management

            Route::group(['prefix' => 'ohc/medicine'], function () {
                Route::get('/list', [MedicineController::class, 'index']);
                Route::post('/list', [MedicineController::class, 'index']);
                Route::get('/add', [MedicineController::class, 'add']);
                Route::post('/add/submit', [MedicineController::class, 'store']);
                Route::get('/edit/{id}', [MedicineController::class, 'edit']);
                Route::post('/edit/submit', [MedicineController::class, 'update']);
                Route::get('/view/{id}', [MedicineController::class, 'view']);
                Route::post('/delete', [MedicineController::class, 'delete']);
                Route::get('/export/excel', [MedicineController::class, 'exportExcel']);
                Route::get('/export/pdf', [MedicineController::class, 'exportPdf']);
                Route::get('/sampledownload', [MedicineController::class, 'DownloadSample']);
                Route::get('/import', [MedicineController::class, 'import']);
                Route::post('/import/submit', [MedicineController::class, 'importSubmit']);
                Route::post('/status', [MedicineController::class, 'statusChange']);
                Route::post('/unique', [MedicineController::class, 'Uniquecheck']);
                Route::post('/hsn-unique', [MedicineController::class, 'hsnNumber']);
                Route::get('/approval/view/{id}', [MedicineController::class, 'approval']);
                Route::post('/approval/submit', [MedicineController::class, 'approvalsubmit']);
            });

            Route::group(['prefix' => 'ohc/vendor'], function () {
                Route::get('/list', [VendorController::class, 'index']);
                Route::post('/list', [VendorController::class, 'index']);
                Route::get('/add', [VendorController::class, 'add']);
                Route::post('/add/submit', [VendorController::class, 'store']);
                Route::get('/edit/{id}', [VendorController::class, 'edit']);
                Route::post('/edit/submit', [VendorController::class, 'update']);
                Route::get('/view/{id}', [VendorController::class, 'view']);
                Route::post('/delete', [VendorController::class, 'delete']);
                Route::get('/export/excel', [VendorController::class, 'exportExcel']);
                Route::get('/export/pdf', [VendorController::class, 'exportPdf']);
                Route::get('/sampledownload', [VendorController::class, 'DownloadSample']);
                Route::get('/import', [VendorController::class, 'import']);
                Route::post('/import/submit', [VendorController::class, 'importSubmit']);
                Route::post('/status', [VendorController::class, 'statusChange']);
                Route::post('/unique', [VendorController::class, 'Uniquecheck']);
            });
            Route::group(['prefix' => 'ohc/employee-cum-patient'], function () {
                Route::get('/list', [EmployeecumPatientController::class, 'index']);
                Route::post('/list', [EmployeecumPatientController::class, 'index']);
                Route::get('/add', [EmployeecumPatientController::class, 'add']);
                Route::post('/add/submit', [EmployeecumPatientController::class, 'store']);
                Route::get('/edit/{id}', [EmployeecumPatientController::class, 'edit']);
                Route::post('/edit/submit', [EmployeecumPatientController::class, 'update']);
                Route::get('/view/{id}', [EmployeecumPatientController::class, 'view']);
                Route::post('/delete', [EmployeecumPatientController::class, 'delete']);
                Route::get('/export/excel', [EmployeecumPatientController::class, 'exportExcel']);
                Route::get('/export/pdf', [EmployeecumPatientController::class, 'exportPdf']);
                Route::get('/sampledownload', [EmployeecumPatientController::class, 'DownloadSample']);
                Route::get('/import', [EmployeecumPatientController::class, 'import']);
                Route::post('/import/submit', [EmployeecumPatientController::class, 'importSubmit']);
                Route::post('/status', [EmployeecumPatientController::class, 'statusChange']);
                Route::post('/unique', [EmployeecumPatientController::class, 'Uniquecheck']);
                Route::get('/employeeid', [EmployeecumPatientController::class, 'employeeid']);
                Route::get('/employeename', [EmployeecumPatientController::class, 'employeename']);
            });
            Route::group(['prefix' => 'ohc/first-aid-location'], function () {
                Route::get('/list', [FirstAidLocationController::class, 'index']);
                Route::post('/list', [FirstAidLocationController::class, 'index']);
                Route::get('/add', [FirstAidLocationController::class, 'add']);
                Route::post('/add/submit', [FirstAidLocationController::class, 'store']);
                Route::get('/edit/{id}', [FirstAidLocationController::class, 'edit']);
                Route::post('/edit/submit', [FirstAidLocationController::class, 'update']);
                Route::get('/view/{id}', [FirstAidLocationController::class, 'view']);
                Route::post('/delete', [FirstAidLocationController::class, 'delete']);
                Route::get('/export/excel', [FirstAidLocationController::class, 'exportExcel']);
                Route::get('/export/pdf', [FirstAidLocationController::class, 'exportPdf']);
                Route::get('/sampledownload', [FirstAidLocationController::class, 'DownloadSample']);
                Route::get('/import', [FirstAidLocationController::class, 'import']);
                Route::post('/import/submit', [FirstAidLocationController::class, 'importSubmit']);
                Route::post('/status', [FirstAidLocationController::class, 'statusChange']);
                Route::post('/unique', [FirstAidLocationController::class, 'Uniquecheck']);
                Route::get('/employeename', [FirstAidLocationController::class, 'employeename']);
            });

            Route::group(['prefix' => 'ohc/certified-first-aider'], function () {
                Route::get('/list', [CertifiedFirstAiderController::class, 'index']);
                Route::post('/list', [CertifiedFirstAiderController::class, 'index']);
                Route::get('/add', [CertifiedFirstAiderController::class, 'add']);
                Route::post('/add/submit', [CertifiedFirstAiderController::class, 'store']);
                Route::get('/edit/{id}', [CertifiedFirstAiderController::class, 'edit']);
                Route::post('/edit/submit', [CertifiedFirstAiderController::class, 'update']);
                Route::get('/view/{id}', [CertifiedFirstAiderController::class, 'view']);
                Route::post('/delete', [CertifiedFirstAiderController::class, 'delete']);
                Route::get('/export/excel', [CertifiedFirstAiderController::class, 'exportExcel']);
                Route::get('/export/pdf', [CertifiedFirstAiderController::class, 'exportPdf']);
                Route::get('/sampledownload', [CertifiedFirstAiderController::class, 'DownloadSample']);
                Route::get('/import', [CertifiedFirstAiderController::class, 'import']);
                Route::post('/import/submit', [CertifiedFirstAiderController::class, 'importSubmit']);
                Route::post('/status', [CertifiedFirstAiderController::class, 'statusChange']);
                Route::post('/unique', [CertifiedFirstAiderController::class, 'Uniquecheck']);
            });


            // Medicine Stock Data

            Route::group(['prefix' => 'ohc/medicine-stock-inventory'], function () {
                Route::get('/list', [MedicineStockController::class, 'index']);
                Route::post('/list', [MedicineStockController::class, 'index']);
                Route::get('/add', [MedicineStockController::class, 'add']);
                Route::post('/add/submit', [MedicineStockController::class, 'store']);
                Route::get('/edit/{id}', [MedicineStockController::class, 'edit']);
                Route::post('/edit/submit', [MedicineStockController::class, 'update']);
                Route::get('/view/{id}', [MedicineStockController::class, 'view']);
                Route::post('/delete', [MedicineStockController::class, 'delete']);
                Route::get('/export/excel', [MedicineStockController::class, 'exportExcel']);
                Route::get('/export/pdf', [MedicineStockController::class, 'exportPdf']);
                Route::post('/status', [MedicineStockController::class, 'statusChange']);
                Route::get('/quantity', [MedicineStockController::class, 'quantity']);
                Route::post('/threshold-limit', [MedicineStockController::class, 'thresholdlimit']);
                Route::post('/unique', [MedicineStockController::class, 'Uniquecheck']);
                Route::get('/ajax-list/{unit_id}', [MedicineStockController::class, 'list']);
                Route::get('/stocklist/{medicine_id}', [MedicineStockController::class, 'stocklist']);
                Route::get('/approval/view/{id}', [MedicineStockController::class, 'approvalview']);
                Route::post('/requestapproval/submit/', [MedicineStockController::class, 'requestsubmit']);
            });

            // Medicine Receiving




            // Medicine Receiving
            Route::group(['prefix' => 'ohc/medicine-receiving-form'], function () {
                Route::get('/list', [MedicineReceivingController::class, 'index']);
                Route::post('/list', [MedicineReceivingController::class, 'index']);
                Route::get('/add', [MedicineReceivingController::class, 'add']);
                Route::post('/add/submit', [MedicineReceivingController::class, 'store']);
                Route::get('/edit/{id}', [MedicineReceivingController::class, 'edit']);
                Route::post('/edit/submit', [MedicineReceivingController::class, 'update']);
                Route::get('/view/{id}', [MedicineReceivingController::class, 'view']);
                Route::post('/delete', [MedicineReceivingController::class, 'delete']);
                Route::get('/ajax-list/{unit_id}', [MedicineReceivingController::class, 'list']);
                Route::get('/export/excel', [MedicineReceivingController::class, 'exportExcel']);
                Route::get('/export/pdf', [MedicineReceivingController::class, 'exportPdf']);
                Route::post('/status', [MedicineReceivingController::class, 'statusChange']);
                Route::post('/unique', [MedicineReceivingController::class, 'Uniquecheck']);
                Route::post('/hsn-number', [MedicineReceivingController::class, 'hsnnumber']);
                Route::post('/medicine-list', [MedicineReceivingController::class, 'medicinelist']);
                Route::get('medicineapproval/view/{id}', [MedicineReceivingController::class, 'approvalview']);
                Route::post('requestapproval/submit', [MedicineReceivingController::class, 'requestsubmit']);
                Route::post('ehsapproval/submit', [MedicineReceivingController::class, 'ehssubmit']);
                Route::post('ehsheadapproval/submit', [MedicineReceivingController::class, 'ehsheadsubmit']);
                // Route::post('stockapproval/submit', [MedicineReceivingController::class, 'stockclosesubmit']);
                Route::get('generalpdf/{id}', [MedicineReceivingController::class, 'generalpdf']);
                Route::post('/checkExistmedicineId', [MedicineReceivingController::class, 'checkExistmedicineId']);
                Route::post('/close', [MedicineReceivingController::class, 'stockclosesubmit']);

            });
            // Medicine Requistion
            Route::group(['prefix' => 'ohc/medicine-requisition'], function () {
                Route::get('/list', [MedicineRequisitionController::class, 'index']);
                Route::post('/list', [MedicineRequisitionController::class, 'index']);
                Route::get('/add', [MedicineRequisitionController::class, 'add']);
                Route::post('/add/submit', [MedicineRequisitionController::class, 'store']);
                Route::get('/edit/{id}', [MedicineRequisitionController::class, 'edit']);
                Route::post('/edit/submit', [MedicineRequisitionController::class, 'update']);
                Route::get('/view/{id}', [MedicineRequisitionController::class, 'view']);
                Route::post('/delete', [MedicineRequisitionController::class, 'delete']);
                Route::get('/export/excel', [MedicineRequisitionController::class, 'exportExcel']);
                Route::get('/export/pdf', [MedicineRequisitionController::class, 'exportPdf']);
                Route::post('/status', [MedicineRequisitionController::class, 'statusChange']);
                Route::get('/quantity/{quantity_id}', [MedicineRequisitionController::class, 'quantity']);
                Route::get('approval/view/{id}', [MedicineRequisitionController::class, 'approvalview']);
                Route::post('approval/submit', [MedicineRequisitionController::class, 'apporvalsubmit']);
                Route::get('generalpdf/{id}', [MedicineRequisitionController::class, 'generalpdf']);
                Route::get('stockdata', [MedicineRequisitionController::class, 'stockdata']);
            });

            Route::group(['prefix' => 'ohc/medicine-issuance'], function () {
                Route::get('/list', [MedicineIssuanceController::class, 'index']);
                Route::post('/list', [MedicineIssuanceController::class, 'index']);
                Route::get('/add', [MedicineIssuanceController::class, 'add']);
                Route::post('/add/submit', [MedicineIssuanceController::class, 'store']);
                Route::get('/edit/{id}', [MedicineIssuanceController::class, 'edit']);
                Route::post('/edit/submit', [MedicineIssuanceController::class, 'update']);
                Route::get('/view/{id}', [MedicineIssuanceController::class, 'view']);
                Route::post('/delete', [MedicineIssuanceController::class, 'delete']);
                Route::get('/export/excel', [MedicineIssuanceController::class, 'exportExcel']);
                Route::get('/export/pdf', [MedicineIssuanceController::class, 'exportPdf']);
                Route::post('/status', [MedicineIssuanceController::class, 'statusChange']);
                Route::get('/quantity/{quantity_id}', [MedicineIssuanceController::class, 'quantity']);
                Route::get('/editquantity/{quantity_id}', [MedicineIssuanceController::class, 'editquantity']);

                Route::post('/delete/{id}', [MedicineIssuanceController::class, 'delete']);
                Route::get('add/{id}', [MedicineIssuanceController::class, 'issue']);
                Route::post('/issue/submit', [MedicineIssuanceController::class, 'issuestore']);
                Route::get('/medicine-details/{unit_id}/{id}', [MedicineIssuanceController::class, 'medicineDetails']);
            });

            // opd

            Route::group(['prefix' => 'ohc/prescribe-to-patient'], function () {
                Route::get('/list', [PrescribetoPatientController::class, 'index']);
                Route::post('/list', [PrescribetoPatientController::class, 'index']);
                Route::get('/add', [PrescribetoPatientController::class, 'add']);
                Route::post('/add/submit', [PrescribetoPatientController::class, 'store']);
                Route::get('/edit/{id}', [PrescribetoPatientController::class, 'edit']);
                Route::post('/edit/submit', [PrescribetoPatientController::class, 'update']);
                Route::get('/view/{id}', [PrescribetoPatientController::class, 'view']);
                Route::get('/view/{id}', [PrescribetoPatientController::class, 'view']);
                Route::get('/generalpdf/{id}', [PrescribetoPatientController::class, 'medicineslip']);
                Route::get('/fetchemployeename', [PrescribetoPatientController::class, 'fetchemployeename']);
                Route::get('/emp-details/{emp_id}', [PrescribetoPatientController::class, 'employeedetails']);
                Route::get('/first-aider-number', [PrescribetoPatientController::class, 'firstaidernumber']);
                Route::get('/firstaider', [PrescribetoPatientController::class, 'firstaider']);
                Route::post('/delete/{id}', [PrescribetoPatientController::class, 'delete']);
                Route::post('/close', [PrescribetoPatientController::class, 'close']);
                Route::get('/export/excel', [PrescribetoPatientController::class, 'exportExcel']);
                Route::get('/export/pdf', [PrescribetoPatientController::class, 'exportPdf']);
                Route::post('/status', [PrescribetoPatientController::class, 'statusChange']);
                Route::get('/quantity', [PrescribetoPatientController::class, 'quantity']);
                Route::get('/employeename', [PrescribetoPatientController::class, 'employeename']);

            });

            Route::group(['prefix' => 'ohc/first-aid'], function () {
                Route::get('/list', [FirstAidController::class, 'index']);
                Route::post('/list', [FirstAidController::class, 'index']);
                Route::get('/add', [FirstAidController::class, 'add']);
                Route::post('/add/submit', [FirstAidController::class, 'store']);
                Route::get('/edit/{id}', [FirstAidController::class, 'edit']);
                Route::post('/edit/submit', [FirstAidController::class, 'update']);
                Route::get('/view/{id}', [FirstAidController::class, 'view']);
                Route::post('/delete', [FirstAidController::class, 'delete']);
                Route::get('/export/excel', [FirstAidController::class, 'exportExcel']);
                Route::get('/export/pdf', [FirstAidController::class, 'exportPdf']);
                Route::post('/status', [FirstAidController::class, 'statusChange']);
                Route::get('/quantity', [FirstAidController::class, 'quantity']);
                Route::get('/employeename', [FirstAidController::class, 'employeename']);
                Route::get('/emp-details/{emp_id}', [FirstAidController::class, 'employeedetails']);


            });

            Route::group(['prefix' => 'ohc/roadside-first-aid'], function () {
                Route::get('/list', [RoadsideFirstAidController::class, 'index']);
                Route::post('/list', [RoadsideFirstAidController::class, 'index']);
                Route::get('/add', [RoadsideFirstAidController::class, 'add']);
                Route::post('/add/submit', [RoadsideFirstAidController::class, 'store']);
                Route::get('/edit/{id}', [RoadsideFirstAidController::class, 'edit']);
                Route::post('/edit/submit', [RoadsideFirstAidController::class, 'update']);
                Route::get('/view/{id}', [RoadsideFirstAidController::class, 'view']);
                Route::post('/delete', [RoadsideFirstAidController::class, 'delete']);
                Route::get('/export/excel', [RoadsideFirstAidController::class, 'exportExcel']);
                Route::get('/export/pdf', [RoadsideFirstAidController::class, 'exportPdf']);
                Route::post('/status', [RoadsideFirstAidController::class, 'statusChange']);
                Route::get('/quantity', [RoadsideFirstAidController::class, 'quantity']);
                Route::post('/unique', [RoadsideFirstAidController::class, 'Uniquecheck']);
                Route::get('/generalpdf/{id}', [RoadsideFirstAidController::class, 'incidentreport']);


            });
            Route::group(['prefix' => 'ohc/roadside-first-aid'], function () {
                Route::get('/list', [RoadsideFirstAidController::class, 'index']);
                Route::post('/list', [RoadsideFirstAidController::class, 'index']);
                Route::get('/add', [RoadsideFirstAidController::class, 'add']);
                Route::post('/add/submit', [RoadsideFirstAidController::class, 'store']);
                Route::get('/edit/{id}', [RoadsideFirstAidController::class, 'edit']);
                Route::post('/edit/submit', [RoadsideFirstAidController::class, 'update']);
                Route::get('/view/{id}', [RoadsideFirstAidController::class, 'view']);
                Route::post('/delete', [RoadsideFirstAidController::class, 'delete']);
                Route::get('/export/excel', [RoadsideFirstAidController::class, 'exportExcel']);
                Route::get('/export/pdf', [RoadsideFirstAidController::class, 'exportPdf']);
                Route::post('/status', [RoadsideFirstAidController::class, 'statusChange']);
                Route::get('/quantity', [RoadsideFirstAidController::class, 'quantity']);
            });



            Route::group(['prefix' => 'ohc/medicine-first-aid'], function () {
                Route::get('/list', [MedicineFirstAidController::class, 'index']);
                Route::post('/list', [MedicineFirstAidController::class, 'index']);
                Route::get('/add', [MedicineFirstAidController::class, 'add']);
                Route::post('/add/submit', [MedicineFirstAidController::class, 'store']);
                Route::get('/edit/{id}', [MedicineFirstAidController::class, 'edit']);
                Route::post('/edit/submit', [MedicineFirstAidController::class, 'update']);
                Route::get('/view/{id}', [MedicineFirstAidController::class, 'view']);
                Route::post('/delete', [MedicineFirstAidController::class, 'delete']);
                Route::get('/export/excel', [MedicineFirstAidController::class, 'exportExcel']);
                Route::get('/export/pdf', [MedicineFirstAidController::class, 'exportPdf']);
                Route::post('/status', [MedicineFirstAidController::class, 'statusChange']);
                Route::get('/quantity/{quantity_id}', [MedicineFirstAidController::class, 'quantity']);
                Route::post('/delete', [MedicineFirstAidController::class, 'delete']);
                Route::get('add/{id}', [MedicineFirstAidController::class, 'issue']);
                Route::post('/issue/submit', [MedicineFirstAidController::class, 'issuestore']);
                Route::post('/delete/{row_id}', [MedicineFirstAidController::class, 'delete']);
                Route::get('/editquantity/{quantity_id}', [MedicineFirstAidController::class, 'editquantity']);

                Route::get('/medicine-details/{unit_id}/{id}', [MedicineFirstAidController::class, 'medicineDetails']);
            });

            // discard
            Route::group(['prefix' => 'ohc/discard'], function () {
                Route::get('/list', [DiscardController::class, 'index']);
                Route::post('/list', [DiscardController::class, 'index']);
                Route::get('/add', [DiscardController::class, 'add']);
                Route::post('/add/submit', [DiscardController::class, 'store']);
                Route::get('/edit/{id}', [DiscardController::class, 'edit']);
                Route::post('/edit/submit', [DiscardController::class, 'update']);
                Route::get('/view/{id}', [DiscardController::class, 'view']);
                Route::post('/delete', [DiscardController::class, 'delete']);
                Route::get('/export/excel', [DiscardController::class, 'exportExcel']);
                Route::get('/export/pdf', [DiscardController::class, 'exportPdf']);
                Route::post('/status', [DiscardController::class, 'statusChange']);
                Route::get('/quantity/{quantity_id}', [DiscardController::class, 'quantity']);
                Route::post('/delete', [DiscardController::class, 'delete']);
                Route::get('add/{id}', [DiscardController::class, 'issue']);
                Route::post('/issue/submit', [DiscardController::class, 'issuestore']);
                Route::get('/medicine-details/{unit_id}/{id}', [DiscardController::class, 'medicineDetails']);
            });

            // Inventory Report
            Route::group(['prefix' => 'ohc/inventory-tabular-view'], function () {
                Route::get('/list', [InventoryController::class, 'index']);
                Route::post('/list', [InventoryController::class, 'index']);
            });

            Route::group(['prefix' => 'ohc/medicine-expire-report'], function () {
                Route::get('/list', [MedicineExpireController::class, 'index']);
                Route::post('/list', [MedicineExpireController::class, 'index']);
                Route::get('/export/excel', [MedicineExpireController::class, 'exportExcel']);
                Route::get('/export/pdf', [MedicineExpireController::class, 'exportPdf']);
            });
            Route::group(['prefix' => 'ohc/monthly-inventory'], function () {
                Route::get('/list', [MonthlyInventoryController::class, 'index']);
                Route::post('/list', [MonthlyInventoryController::class, 'index']);
            });
            Route::group(['prefix' => 'incident/type-master'], function () {
                Route::get('/list', [IncidentTypeController::class, 'index']);
                Route::post('/list', [IncidentTypeController::class, 'index']);
                Route::get('/add', [IncidentTypeController::class, 'add']);
                Route::post('/add/submit', [IncidentTypeController::class, 'store']);
                Route::get('/edit/{id}', [IncidentTypeController::class, 'edit']);
                Route::post('/edit/submit', [IncidentTypeController::class, 'update']);
                Route::get('/view/{id}', [IncidentTypeController::class, 'view']);
                Route::post('/delete', [IncidentTypeController::class, 'delete']);
                Route::get('/export/excel', [IncidentTypeController::class, 'exportExcel']);
                Route::get('/export/pdf', [IncidentTypeController::class, 'exportPdf']);
                Route::get('/sample_download', [IncidentTypeController::class, 'DownloadSample']);
                Route::get('/import', [IncidentTypeController::class, 'import']);
                Route::post('/import/Submit', [IncidentTypeController::class, 'importSubmit']);
                Route::post('/status', [IncidentTypeController::class, 'statusChange']);
                Route::post('/unique', [IncidentTypeController::class, 'Uniquecheck']);
            });
            Route::group(['prefix' => 'incident/hira-master'], function () {
                Route::get('/list', [HiraController::class, 'index']);
                Route::post('/list', [HiraController::class, 'index']);
                Route::get('/add', [HiraController::class, 'add']);
                Route::post('/add/submit', [HiraController::class, 'store']);
                Route::get('/edit/{id}', [HiraController::class, 'edit']);
                Route::post('/edit/submit', [HiraController::class, 'update']);
                Route::get('/view/{id}', [HiraController::class, 'view']);
                Route::post('/delete', [HiraController::class, 'delete']);
                Route::get('/export/excel', [HiraController::class, 'exportExcel']);
                Route::get('/export/pdf', [HiraController::class, 'exportPdf']);
                Route::get('/sample_download', [HiraController::class, 'DownloadSample']);
                Route::get('/import', [HiraController::class, 'import']);
                Route::post('/import/Submit', [HiraController::class, 'importSubmit']);
                Route::post('/status', [HiraController::class, 'statusChange']);
                Route::post('/unique', [HiraController::class, 'Uniquecheck']);
            });

            Route::group(['prefix' => 'incident/initial-incident'], function () {
                Route::get('/list', [InitialIncidentController::class, 'index']);
                Route::post('/list', [InitialIncidentController::class, 'index']);
                Route::get('/add', [InitialIncidentController::class, 'add']);
                Route::post('/add/submit', [InitialIncidentController::class, 'store']);
                Route::get('/edit/{id}', [InitialIncidentController::class, 'edit']);
                Route::post('/edit/submit', [InitialIncidentController::class, 'update']);
                Route::get('/view/{id}', [InitialIncidentController::class, 'view']);
                Route::post('/delete', [InitialIncidentController::class, 'delete']);
                Route::get('/export/excel', [InitialIncidentController::class, 'exportExcel']);
                Route::get('/export/pdf', [InitialIncidentController::class, 'exportPdf']);
                Route::get('/sample_download', [InitialIncidentController::class, 'DownloadSample']);
                Route::get('/import', [InitialIncidentController::class, 'import']);
                Route::post('/import/Submit', [InitialIncidentController::class, 'importSubmit']);
                Route::post('/status', [InitialIncidentController::class, 'statusChange']);
                Route::post('/unique', [InitialIncidentController::class, 'Uniquecheck']);
                Route::get('/employeename', [InitialIncidentController::class, 'employeename']);
                Route::get('/fetchEmployeeDetails/{emp_id}', [InitialIncidentController::class, 'fetchEmployeeDetails']);
                Route::post('/deleteEvidence/{id}', [InitialIncidentController::class, 'deleteEvidence']);
                Route::get('/review/{id}', [InitialIncidentController::class, 'review']);
                Route::post('/ehs_head_review/submit', [InitialIncidentController::class, 'ehsHeadReviewSubmit']);
                Route::get('/teamMembers', [InitialIncidentController::class, 'teamMembers']);
                Route::get('/investigation/{incident_id}', [InitialIncidentController::class, 'investigation']);
                Route::post('/savehira', [InitialIncidentController::class, 'saveHira']);
                Route::post('/investigation/submit', [InitialIncidentController::class, 'investigationSubmit']);
                Route::get('/gethiradetails/{hira_id}', [InitialIncidentController::class, 'gethiradetails']);
                Route::get('/existingHira/{incident_id}', [InitialIncidentController::class, 'existingHira']);
                Route::get('/existingMOC/{incident_id}', [InitialIncidentController::class, 'existingMOC']);
                Route::get('/riskAnalysis/{incident_id}', [InitialIncidentController::class, 'riskAnalysis']);
                Route::post('/riskAnalysis/submit', [InitialIncidentController::class, 'riskAnalysisSubmit']);
                Route::post('/ehs_head_verify/submit', [InitialIncidentController::class, 'ehsHeadVerifySubmit']);
            });


            Route::group(['prefix' => 'incident/accidentReport'], function () {
                Route::get('/list', [AccidentReportController::class, 'index']);
                Route::post('/list', [AccidentReportController::class, 'index']);
                Route::get('/add', [AccidentReportController::class, 'add']);
                Route::post('/add/submit', [AccidentReportController::class, 'store']);
                Route::get('/edit/{id}', [AccidentReportController::class, 'edit']);
                Route::post('/edit/submit', [AccidentReportController::class, 'update']);
                Route::get('/view/{id}', [AccidentReportController::class, 'view']);
                Route::post('/delete', [AccidentReportController::class, 'delete']);
                Route::get('/export/excel', [AccidentReportController::class, 'exportExcel']);
                Route::get('/export/pdf', [AccidentReportController::class, 'exportPdf']);
                Route::get('/sample_download', [AccidentReportController::class, 'DownloadSample']);
                Route::get('/import', [AccidentReportController::class, 'import']);
                Route::post('/import/Submit', [AccidentReportController::class, 'importSubmit']);
                Route::post('/status', [AccidentReportController::class, 'statusChange']);
                Route::post('/unique', [AccidentReportController::class, 'Uniquecheck']);
                Route::get('/getEmployeeDetails/{emp_id}', [AccidentReportController::class, 'getEmployeeDetails']);
                Route::get('/fetchEmployeeDetails/{emp_code}', [AccidentReportController::class, 'fetchEmployeeDetails']);
                Route::get('/investigation/{accident_id}', [AccidentReportController::class, 'investigation']);
                Route::post('/investigation/submit', [AccidentReportController::class, 'investigationSubmit']);
                Route::get('/review/{id}', [AccidentReportController::class, 'review']);
                Route::post('/ehs_head_review/submit', [AccidentReportController::class, 'ehsHeadReviewSubmit']);
                Route::get('/getemployeename', [AccidentReportController::class, 'employeename']);
                Route::get('/fetchPersonDetails/{id}/{type}', [AccidentReportController::class, 'fetchPersonDetails']);
                Route::get('/fetchEmployeeOrWorkerList/{type}', [AccidentReportController::class, 'fetchEmployeeOrWorkerList']);
                Route::post('/addInjury', [AccidentReportController::class, 'addInjury']);
                Route::post('/deletebodayparts', [AccidentReportController::class, 'deletebodayparts']);

            });


        });
    });
});
