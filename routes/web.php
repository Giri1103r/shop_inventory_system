<?php


use App\Models\KPI\HSCInputs;

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Cron\CronController;

use App\Http\Controllers\Master\UnitController;
use App\Http\Controllers\Master\WorkController;
use App\Http\Controllers\Master\TopicController;
use App\Http\Controllers\Master\VenueController;
use App\Http\Controllers\KPI\HSCInputsController;
use App\Http\Controllers\Master\CompanyController;
use App\Http\Controllers\IMS\Master\HiraController;
use App\Http\Controllers\Master\EmployeeController;
use App\Http\Controllers\Master\LocationController;
use App\Http\Controllers\Master\SafeWorkController;
use App\Http\Controllers\KPI\KpiDashboardController;
use App\Http\Controllers\Master\ChecklistController;
use App\Http\Controllers\Master\WorkerLogController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\PrecautionController;
use App\Http\Controllers\Master\TypeofWorkController;
use App\Http\Controllers\Training\TrainingController;
use App\Http\Controllers\Master\EmployeeLogController;
use App\Http\Controllers\Master\EquipInvalveController;
use App\Http\Controllers\Inspection\MSDS\MSDSController;
use App\Http\Controllers\Inspection\RRAA\RRAAController;
use App\Http\Controllers\MSDS\Master\ChemicalController;
use App\Http\Controllers\Master\TrainingMatrixController;
use App\Http\Controllers\OhcManagement\DiscardController;
use App\Models\Inspection\Fire\MonthlyPhysicalInspection;
use App\Http\Controllers\Master\ProtectiveEquipController;
use App\Http\Controllers\IMS\Master\IncidentTypeController;
use App\Http\Controllers\Inspection\Fire\HoseBoxController;
use App\Http\Controllers\Master\TrainingCalendarController;
use App\Http\Controllers\Master\TrainingScheduleController;
use App\Http\Controllers\Ppemanagement\DashboardController;
use App\Http\Controllers\Master\NominationProcessController;
use App\Http\Controllers\Ppemanagement\PpeRequestController;
use App\Http\Controllers\Inspection\Fire\FireAlarmController;
use App\Http\Controllers\KPI\Master\LeadingLaggingController;
use App\Http\Controllers\Safetypermit\SafetyPermitController;
use App\Http\Controllers\Inspection\Fire\FirePreNocController;
use App\Http\Controllers\Inspection\Ohc\SafetyPettyController;
use App\Http\Controllers\OhcManagement\OhcDashboardController;
use App\Http\Controllers\Ppemanagement\PpeExemptionController;
use App\Http\Controllers\IMS\Incident\AccidentReportController;
use App\Http\Controllers\OhcManagement\Master\VendorController;
use App\Http\Controllers\OhcManagement\MedicineStockController;
use App\Http\Controllers\IMS\Incident\InitialIncidentController;
use App\Http\Controllers\Inspection\Fire\HoseReelHoseController;
use App\Http\Controllers\Inspection\Fire\FirePumpHouseController;
use App\Http\Controllers\Inspection\Ohc\FirstAiderlistController;
use App\Http\Controllers\Inspection\Ohc\FirstAidRecordController;
use App\Http\Controllers\Inspection\Ohc\FloorStretcherController;
use App\Http\Controllers\OhcManagement\Master\MedicineController;
use App\Http\Controllers\Inspection\Audit\AuditAnalysisController;
use App\Http\Controllers\Inspection\Fire\IsolationValveController;
use App\Http\Controllers\Inspection\GembaWalk\GembaWalkController;
use App\Http\Controllers\Inspection\Ohc\Master\FirstAidController;
use App\Http\Controllers\Inspection\Ohc\WeeklyAmbulanceController;
use App\Http\Controllers\OhcManagement\MedicineFirstAidController;
use App\Http\Controllers\OhcManagement\MedicineIssuanceController;
use App\Http\Controllers\OhcManagement\Report\InventoryController;
use App\Http\Controllers\Inspection\Audit\InterUnitAuditController;
use App\Http\Controllers\Inspection\Fire\SprinklarSystemController;
use App\Http\Controllers\Inspection\Master\ChecklistTypeController;
use App\Http\Controllers\OhcManagement\MedicineReceivingController;


use App\Http\Controllers\Ppemanagement\PpeStockInventoryController;
use App\Http\Controllers\IMS\Incident\InitialFireIncidentController;
use App\Http\Controllers\Inspection\Audit\AuditAssessmentController;
use App\Http\Controllers\Inspection\Fire\FireExtinguisherController;
use App\Http\Controllers\Inspection\Fire\HooterInspectionController;
use App\Http\Controllers\Inspection\Ohc\WeeklyFirstAidBoxController;
use App\Http\Controllers\Auth\LoginController as AuthLoginController;
use App\Http\Controllers\Inspection\Audit\MonthlyAuditPlanController;
use App\Http\Controllers\Inspection\Ohc\MonthlyFirstAidboxController;
use App\Http\Controllers\OhcManagement\MedicineRequisitionController;
use App\Http\Controllers\Inspection\Audit\Master\TaskMasterController;
use App\Http\Controllers\Inspection\Fire\DetectorInspectionController;
use App\Http\Controllers\Inspection\Fire\PASystemInspectionController;
use App\Http\Controllers\Inspection\Master\ChecklistSubTypeController;
use App\Http\Controllers\Inspection\Ohc\DailyVitalEquipmentController;
use App\Http\Controllers\Inspection\Safety\Master\EquipmentController;
use App\Http\Controllers\OhcManagement\Opd\RoadsideFirstAidController;
use App\Http\Controllers\Inspection\Ohc\FirstAidBagChecklistController;
use App\Http\Controllers\Inspection\Ohc\MonthlyMedicineStoreController;
use App\Http\Controllers\OhcManagement\Report\MedicineExpireController;
use App\Http\Controllers\Inspection\Environment\LuxMonitoringController;
use App\Http\Controllers\Inspection\Fire\CertifiedFireFighterController;
use App\Http\Controllers\Inspection\Fire\FireSafetyEquipmentsController;
use App\Http\Controllers\Inspection\Fire\MonthlyFirePumpHouseController;
use App\Http\Controllers\Inspection\Fire\SandBucketInspectionController;
use App\Http\Controllers\Inspection\Safety\ForkLiftInspectionController;
use App\Http\Controllers\OhcManagement\Master\HospitalDetailsController;
use App\Http\Controllers\OhcManagement\Opd\PrescribetoPatientController;
use App\Http\Controllers\OhcManagement\Report\YearlyInventoryController;
use App\Http\Controllers\Inspection\Fire\FireModularInspectionController;
use App\Http\Controllers\Inspection\Fire\HydrantRiserInspectionContoller;
use App\Http\Controllers\Inspection\Ohc\MedicalRequisitionSlipController;
use App\Http\Controllers\Inspection\Safety\FireSafetyEquipmentController;
use App\Http\Controllers\OhcManagement\Master\FirstAidLocationController;
use App\Http\Controllers\OhcManagement\Report\MonthlyInventoryController;
use App\Http\Controllers\Inspection\Fire\CoTypeFireExtinguisherController;
use App\Http\Controllers\Inspection\Master\ChecklistSubTypeDataController;
use App\Http\Controllers\Inspection\Fire\FireMockDrillInspectionController;
use App\Http\Controllers\Inspection\Ohc\CurrentNewExtCodeDialingController;
use App\Http\Controllers\Inspection\Safety\OHSPlantSummaryReportController;
use App\Http\Controllers\Inspection\Safety\SafetyWalkObservationController;
use App\Http\Controllers\OhcManagement\Master\EmployeecumPatientController;
use App\Http\Controllers\OhcManagement\MedicalFitnessCertificateController;
use App\Http\Controllers\Inspection\Fire\EmergencyLightInspectionController;
use App\Http\Controllers\OhcManagement\Master\CertifiedFirstAiderController;
use App\Http\Controllers\Inspection\Fire\MonthlyPhysicalInspectionController;
use App\Http\Controllers\Inspection\Ohc\DailyDepartmentFirstAidBoxController;
use App\Http\Controllers\Inspection\Ohc\FirstAidMedicineInspectionController;
use App\Http\Controllers\Inspection\Ohc\OccupationHealthInspectionController;
use App\Http\Controllers\Inspection\Ohc\PhysicalMedicalExaminationController;
use App\Http\Controllers\Inspection\Safety\SafetyGalleryInsepctionController;
use App\Http\Controllers\Inspection\Audit\Master\ComplianceCategoryController;
use App\Http\Controllers\Inspection\Environment\WorkNoiseMonitoringController;
use App\Http\Controllers\Inspection\Ohc\HealthInstrumentCalibrationController;
use App\Http\Controllers\Inspection\Ohc\OHCHygieneCleaningChecklistController;
use App\Http\Controllers\Inspection\Safety\MonthlyEyeWashInspectionController;
use App\Http\Controllers\Inspection\Safety\MonthlyForkLiftInspectionController;
use App\Http\Controllers\Inspection\Environment\WorkZoneAirMonitoringController;

use App\Http\Controllers\Inspection\Fire\ChecklistObservationFollowupController;
use App\Http\Controllers\Inspection\Environment\AmbientNoiseMonitoringController;
use App\Http\Controllers\Inspection\Fire\CartridgeTypeFireExtinguisherController;
use App\Http\Controllers\Inspection\Environment\AmbientAirMonitoringYearlyController;
use App\Http\Controllers\Inspection\Ohc\EmergencyBuyerFirstAidBagChecklistController;
use App\Http\Controllers\Inspection\Ohc\MedicalRequisitionSlipSecurityGateController;
use App\Http\Controllers\{SettingsController, LocalizationController, TestController};
use App\Http\Controllers\Inspection\Environment\DgSetStackEmissionMonitoringController;
use App\Http\Controllers\OhcManagement\Opd\FirstAidController as OpdFirstAidController;
use App\Http\Controllers\Admin\{LoginController, NotificationController, AdminController, BlockedController};
use App\Http\Controllers\KPI\LeadingLaggingDashboardController;
use App\Http\Controllers\Master\{PpeTypeController, PpeTypeMasterController, UserLogController, UserPermissionController, UploadLogController, UserController, UserRoleController};

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

Route::get('cron/ohc/medicine-issuance/import', [CronController::class, 'queueMedicineIsuuanceImport']);
Route::get('cron/ohc/medicine-requisition/import', [CronController::class, 'queueMedicineRequisitionImport']);


Route::get('cron/safetypermit/protectiveequipmentmaster/import', [CronController::class, 'queueProtectiveequipmentmasterImport']);
Route::get('cron/safetypermit/equipinvolvemaster/import', [CronController::class, 'queueEquipinvolvemasterImport']);
Route::get('cron/safetypermit/safeworkmaster/import', [CronController::class, 'queueSafeworkmasterImport']);
Route::get('cron/safetypermit/precautionmaster/import', [CronController::class, 'queuePrecautionmasterImport']);

Route::get('cron/safetypermit/checklistmaster/import', [CronController::class, 'queueChecklistmasterImport']);
Route::get('cron/inspection/ohc/current_new_code/import', [CronController::class, 'currentNextCodeImport']);
Route::get('cron/inspection/safety/equipment/import', [CronController::class, 'equipmentimport']);
Route::get('cron/inspection/ohc/first-aid-equipment/import', [CronController::class, 'firstAidEquipmentImport']);
Route::get('cron/inspection/audit/task-master/import', [CronController::class, 'task']);



Route::get('stockitem', [CronController::class, 'storeItem']);
Route::get('expireexemptionstatus', [CronController::class, 'ExpireExemption']);
Route::get('updateStockitem', [CronController::class, 'updateItem']);
Route::get('cron/master/work/all-details-temp', [CronController::class, 'workMasterAllDetailsTemp']);
Route::get('cron/master/workmastertemp', [CronController::class, 'workMasterTemp']);
Route::get('cron/master/worker/temp-custom-details', [CronController::class, 'workMasterTempCustom']);
Route::get('cron/master/worksave', [CronController::class, 'workSave']);
Route::get('cron/master/employee/all-details-temp', [CronController::class, 'employeeMasterTempAllDetails']);
Route::get('cron/master/employee/temp-details', [CronController::class, 'employeeMasterTemp']);
Route::get('cron/master/employee/temp-custom-details', [CronController::class, 'employeeMasterTempCustom']);
Route::get('cron/master/employee/temp-custom-token-details', [CronController::class, 'employeeMasterTempCustomToken']);
Route::get('cron/master/employee_save', [CronController::class, 'EmployeeSave']);
Route::get('permit_expiry', [CronController::class, 'permitExpiry']);
Route::get('permit_close', [CronController::class, 'permitClose']);

Route::get('cron/ohc/stockrequest', [CronController::class, 'stockrequest']);
Route::get('cron/ohc/stockupdate', [CronController::class, 'stockupdate']);
Route::get('cron/ohc/prevoiusmonthstock', [CronController::class, 'prevoiusmonthstock']);

Route::get('test', [TestController::class,  'index']);

Route::get('incident/initial-incident/body-part/{randomId}/{rowId}/{injury_person_type}/{injured_person_id}', [InitialIncidentController::class, 'empBodyPartUrl']);

Route::get('incident/initial-incident/body-part/edit/{randomId}/{rowId}', [InitialIncidentController::class, 'editempBodyPartUrl']);

Route::get('incident/initial-incident/api/getbodyEmpdetails', [InitialIncidentController::class, 'apigetbodyEmpdetails']);
Route::post('incident/initial-incident/addInjury/api', [InitialIncidentController::class, 'addInjury_api']);


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

            Route::get('training/dashboard', [TrainingController::class, 'index']);
            Route::get('training/trainingStatus', [TrainingController::class, 'getTrainingStatus']);
            Route::get('training/dashboard/department', [TrainingController::class, 'getDepartment']);
            Route::get('training/dashboard/monthwisetraining', [TrainingController::class, 'getmonthwiseTraining']);
            Route::get('training/dashboard/trainingStatusCount', [TrainingController::class, 'gettrainingStatusCount']);

            Route::group(['prefix' => 'dashboard/'], function () {
                Route::get('total-incident', [AdminController::class, 'getTotalIncident']);
                Route::get('heatmap-of-imsData', [AdminController::class, 'getHeatmapImsData']);
                Route::get('incident-type-chart', [AdminController::class, 'getIncidentTypeChart']);
                Route::get('inspection-count', [AdminController::class, 'InspectionWiseCount']);
                Route::get('ptw-open-close', [AdminController::class, 'PTWActiveVsClose']);
                Route::get('ptw-type-wise-count', [AdminController::class, 'PTWTypeWiseCount']);
                Route::get('training-hour-safety-department', [AdminController::class, 'TrainingHoursSafetyDepartmentWise']);
                Route::get('ptw-hold-violation', [AdminController::class, 'ptwholdviolation']);
                Route::get('PPEIssuanceGroupWise', [AdminController::class, 'getPPEIssuanceGroupWise']);
                Route::get('PTWAvgTimeChart', [AdminController::class, 'getPTWAvgTimeChart']);
                Route::get('PPEAvailabilityChart', [AdminController::class, 'getPPEAvailabilityChart']);
                Route::get('TrainingCompletionCount', [AdminController::class, 'getTrainingCompletionCount']);
                Route::get('TypeofIIRCount', [AdminController::class, 'getTypeofIIRCount']);
                Route::get('AccidentReportUnitWiseCount', [AdminController::class, 'getAccidentReportUnitWiseCount']);
                Route::get('injurypart', [AdminController::class, 'getInjurypart']);
                Route::post('getbodycount', [AdminController::class, 'injurybodycount']);
                Route::get('IIRTypeWiseUAUC', [AdminController::class, 'IIRTypeWiseUAUC']);
                Route::get('uauc-static-report', [AdminController::class, 'uaucStaticReport']);
                Route::get('nearMissFrequency', [AdminController::class, 'nearMissFrequency']);
                Route::get('auditFindings', [AdminController::class, 'auditFindings']);
                Route::get('IIRTypeWiseRCPA', [AdminController::class, 'IIRTypeWiseRCPA']);
                Route::get('gemba-walk-observation', [AdminController::class, 'gembaWalkObservation']);
                Route::get('unitwiseptw', [AdminController::class, 'unitwiseptw']);
                Route::get('monthwiseptw', [AdminController::class, 'monthwiseptw']);
                Route::get('department', [AdminController::class, 'getDepartment']);
                Route::get('monthwisetraining', [AdminController::class, 'getmonthwiseTraining']);
                Route::get('trainingStatusCount', [AdminController::class, 'gettrainingStatusCount']);
                // Route::get('dailyObservation', [AdminController::class, 'DailyObservationMonthCount']);
            });

            /**
             * KPI
             */

            Route::group(['prefix' => 'kpi/dashboard/'], function () {
                Route::get('', [KpiDashboardController::class, 'index']);
                Route::get('chart1', [KpiDashboardController::class, 'getChart1']);
                Route::get('chart2', [KpiDashboardController::class, 'getChart2']);
                Route::get('chart3', [KpiDashboardController::class, 'getChart3']);
                Route::get('chart4', [KpiDashboardController::class, 'getChart4']);
                Route::get('chart5', [KpiDashboardController::class, 'getChart5']);
                Route::get('chart6', [KpiDashboardController::class, 'getChart6']);
                Route::get('chart7', [KpiDashboardController::class, 'getChart7']);
                Route::get('chart8', [KpiDashboardController::class, 'getChart8']);
                Route::get('chart9', [KpiDashboardController::class, 'getChart9']);
                Route::get('chart10', [KpiDashboardController::class, 'getChart10']);
                Route::get('chart11', [KpiDashboardController::class, 'getChart11']);
                Route::get('chart12', [KpiDashboardController::class, 'getChart12']);
                Route::get('chart13', [KpiDashboardController::class, 'getChart13']);
                Route::get('chart14', [KpiDashboardController::class, 'getChart14']);
                Route::get('chart15', [KpiDashboardController::class, 'getChart15']);
                Route::get('chart16', [KpiDashboardController::class, 'getChart16']);
                Route::get('chart17', [KpiDashboardController::class, 'getChart17']);
                Route::get('chart18', [KpiDashboardController::class, 'getChart18']);
                Route::get('chart19', [KpiDashboardController::class, 'getChart19']);
                Route::get('chart20', [KpiDashboardController::class, 'getChart20']);
                Route::get('chart21', [KpiDashboardController::class, 'getChart21']);

                Route::group(['prefix' =>  'leading-lagging/'], function () {
                    Route::get('', [LeadingLaggingDashboardController::class, 'index']);
                    Route::get('leading/chart1', [LeadingLaggingDashboardController::class, 'getChart1']);
                    Route::GET('lagging-line', [LeadingLaggingDashboardController::class, 'LaggingIndicatorLine']);
                    Route::GET('lagging-indicator', [LeadingLaggingDashboardController::class, 'LaggingDoughNut']);
                });
            });

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
                Route::get('/get-unit-data/{companyId}/{id}', [UnitController::class, 'unitData']);
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
                Route::post('/passwordchange/submit', [EmployeeController::class, 'PasswordUpdateSubmit']);
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



            Route::group(['prefix' => 'inspection/master/'], function () {
                Route::group(['prefix' => 'checklist-type'], function () {
                    Route::GET('/list', [ChecklistTypeController::class, 'Index']);
                    Route::POST('/list', [ChecklistTypeController::class, 'Index']);
                    Route::GET('/add', [ChecklistTypeController::class, 'Add']);
                    Route::POST('/add/submit', [ChecklistTypeController::class, 'Store']);
                    Route::POST('/unique', [ChecklistTypeController::class, 'UniqueCheck']);
                    Route::GET('/edit/{id}', [ChecklistTypeController::class, 'Edit']);
                    Route::POST('/edit/submit', [ChecklistTypeController::class, 'Update']);
                    Route::GET('/view/{id}', [ChecklistTypeController::class, 'View']);
                    Route::POST('/delete', [ChecklistTypeController::class, 'Delete']);
                    Route::POST('/status', [ChecklistTypeController::class, 'StatusChange']);
                    Route::GET('/export/excel', [ChecklistTypeController::class, 'ExportExcel']);
                    Route::GET('/export/pdf', [ChecklistTypeController::class, 'ExportPDF']);
                    Route::GET('/import', [ChecklistTypeController::class, 'Import']);
                    Route::POST('/import/Submit', [ChecklistTypeController::class, 'ImportSubmit']);
                    Route::GET('/sample_download', [ChecklistTypeController::class, 'DownloadSample']);
                    Route::POST('/lists', [ChecklistTypeController::class, 'Checklists']);
                });

                Route::group(['prefix' => 'checklist-sub-type'], function () {
                    Route::GET('/list', [ChecklistSubTypeController::class, 'Index']);
                    Route::POST('/list', [ChecklistSubTypeController::class, 'Index']);
                    Route::GET('/add', [ChecklistSubTypeController::class, 'Add']);
                    Route::POST('/add/submit', [ChecklistSubTypeController::class, 'Store']);
                    Route::POST('/unique', [ChecklistSubTypeController::class, 'UniqueCheck']);
                    Route::GET('/edit/{id}', [ChecklistSubTypeController::class, 'Edit']);
                    Route::POST('/edit/submit', [ChecklistSubTypeController::class, 'Update']);
                    Route::GET('/view/{id}', [ChecklistSubTypeController::class, 'View']);
                    Route::POST('/delete', [ChecklistSubTypeController::class, 'Delete']);
                    Route::POST('/status', [ChecklistSubTypeController::class, 'StatusChange']);
                    Route::GET('/export/excel', [ChecklistSubTypeController::class, 'ExportExcel']);
                    Route::GET('/export/pdf', [ChecklistSubTypeController::class, 'ExportPDF']);
                    Route::GET('/import', [ChecklistSubTypeController::class, 'Import']);
                    Route::POST('/import/submit', [ChecklistSubTypeController::class, 'ImportSubmit']);
                    Route::GET('/sampledownload', [ChecklistSubTypeController::class, 'DownloadSample']);
                    Route::POST('/lists', [ChecklistSubTypeController::class, 'Checklists']);
                    Route::get('/ajax-list/{checklistTypeId}/{id}', [ChecklistSubTypeController::class, 'checklistSubTypeList']);
                });

                Route::group(['prefix' => 'checklist-sub-type-data/'], function () {
                    Route::get('list', [ChecklistSubTypeDataController::class, 'index']);
                    Route::post('list', [ChecklistSubTypeDataController::class, 'index']);
                    Route::get('add', [ChecklistSubTypeDataController::class, 'add']);
                    Route::post('add/submit', [ChecklistSubTypeDataController::class, 'store']);
                    Route::get('edit/{id}', [ChecklistSubTypeDataController::class, 'edit']);
                    Route::post('edit/submit', [ChecklistSubTypeDataController::class, 'update']);
                    Route::get('view/{id}', [ChecklistSubTypeDataController::class, 'view']);
                    Route::post('delete', [ChecklistSubTypeDataController::class, 'delete']);
                    Route::get('export/excel', [ChecklistSubTypeDataController::class, 'exportExcel']);
                    Route::get('export/pdf', [ChecklistSubTypeDataController::class, 'exportPdf']);
                    Route::get('sample_download', [ChecklistSubTypeDataController::class, 'DownloadSample']);
                    Route::get('import', [ChecklistSubTypeDataController::class, 'import']);
                    Route::post('import/Submit', [ChecklistSubTypeDataController::class, 'importSubmit']);
                    Route::post('status', [ChecklistSubTypeDataController::class, 'statusChange']);
                    Route::post('unique', [ChecklistSubTypeDataController::class, 'Uniquecheck']);
                    Route::DELETE('deleteChecklist/{id}', [ChecklistSubTypeDataController::class, 'deleteChecklist']);
                });
            });



            Route::group(['prefix' => 'audit/'], function () {
                Route::group(['prefix' => 'assessment/'], function () {
                    Route::get('list', [AuditAssessmentController::class, 'index']);
                    Route::post('list', [AuditAssessmentController::class, 'index']);
                    Route::get('add', [AuditAssessmentController::class, 'add']);
                    Route::post('add/submit', [AuditAssessmentController::class, 'store']);
                    Route::get('edit/{id}', [AuditAssessmentController::class, 'edit']);
                    Route::post('edit/submit', [AuditAssessmentController::class, 'update']);
                    Route::get('view/{id}', [AuditAssessmentController::class, 'view']);
                    Route::post('delete', [AuditAssessmentController::class, 'delete']);
                    Route::get('export/excel', [AuditAssessmentController::class, 'exportExcel']);
                    Route::get('export/pdf', [AuditAssessmentController::class, 'exportPdf']);
                    Route::get('sample_download', [AuditAssessmentController::class, 'DownloadSample']);
                    Route::get('import', [AuditAssessmentController::class, 'import']);
                    Route::post('import/Submit', [AuditAssessmentController::class, 'importSubmit']);
                    Route::post('status', [AuditAssessmentController::class, 'statusChange']);
                    Route::post('unique', [AuditAssessmentController::class, 'Uniquecheck']);
                    Route::get('employeeName', [AuditAssessmentController::class, 'employeename']);
                    Route::get('generalpdf/{id}', [AuditAssessmentController::class, 'generalpdf']);
                    Route::get('generalExcel/{id}', [AuditAssessmentController::class, 'generalExcel']);
                });

                Route::group(['prefix' => '6s-analysis/'], function () {
                    Route::get('list', [AuditAnalysisController::class, 'index']);
                    Route::post('list', [AuditAnalysisController::class, 'index']);
                    Route::get('add', [AuditAnalysisController::class, 'add']);
                    Route::post('add/submit', [AuditAnalysisController::class, 'store']);
                    Route::get('edit/{id}', [AuditAnalysisController::class, 'edit']);
                    Route::post('edit/submit', [AuditAnalysisController::class, 'update']);
                    Route::get('view/{id}', [AuditAnalysisController::class, 'view']);
                    Route::post('delete', [AuditAnalysisController::class, 'delete']);
                    Route::get('export/excel', [AuditAnalysisController::class, 'exportExcel']);
                    Route::get('export/pdf', [AuditAnalysisController::class, 'exportPdf']);
                    Route::get('sample_download', [AuditAnalysisController::class, 'DownloadSample']);
                    Route::get('import', [AuditAnalysisController::class, 'import']);
                    Route::post('import/Submit', [AuditAnalysisController::class, 'importSubmit']);
                    Route::post('status', [AuditAnalysisController::class, 'statusChange']);
                    Route::get('employeeName', [AuditAnalysisController::class, 'employeename']);
                    Route::get('ajax-list', [AuditAnalysisController::class, 'Uniquecheck']);
                    Route::GET('exportViewPdf/{id}', [AuditAnalysisController::class, 'ExportViewPDF']);
                    Route::GET('generalExcel/{id}', [AuditAnalysisController::class, 'generalExcel']);
                });

                Route::group(['prefix' => 'master/task/'], function () {
                    Route::get('list', [TaskMasterController::class, 'index']);
                    Route::post('list', [TaskMasterController::class, 'index']);
                    Route::get('add', [TaskMasterController::class, 'add']);
                    Route::post('add/submit', [TaskMasterController::class, 'store']);
                    Route::get('edit/{id}', [TaskMasterController::class, 'edit']);
                    Route::post('edit/submit', [TaskMasterController::class, 'update']);
                    Route::get('view/{id}', [TaskMasterController::class, 'view']);
                    Route::get('export/excel', [TaskMasterController::class, 'exportExcel']);
                    Route::get('export/pdf', [TaskMasterController::class, 'exportPdf']);
                    Route::get('import', [TaskMasterController::class, 'import']);
                    Route::get('sample_download', [TaskMasterController::class, 'DownloadSample']);
                    Route::post('import/Submit', [TaskMasterController::class, 'importSubmit']);
                    Route::post('delete', [TaskMasterController::class, 'Delete']);
                    Route::post('status', [TaskMasterController::class, 'StatusChange']);
                    Route::post('unique', [TaskMasterController::class, 'Uniquecheck']);

                });

                Route::group(['prefix' => 'master/compliance_category'], function () {
                    Route::GET('/list', [ComplianceCategoryController::class, 'Index']);
                    Route::POST('/list', [ComplianceCategoryController::class, 'Index']);
                    Route::GET('/add', [ComplianceCategoryController::class, 'Add']);
                    Route::POST('/add/submit', [ComplianceCategoryController::class, 'Store']);
                    Route::POST('/unique', [ComplianceCategoryController::class, 'UniqueCheck']);
                    Route::GET('/edit/{id}', [ComplianceCategoryController::class, 'Edit']);
                    Route::POST('/edit/submit', [ComplianceCategoryController::class, 'Update']);
                    Route::GET('/view/{id}', [ComplianceCategoryController::class, 'View']);
                    Route::POST('/delete', [ComplianceCategoryController::class, 'Delete']);
                    Route::POST('/status', [ComplianceCategoryController::class, 'StatusChange']);
                    Route::GET('/export/excel', [ComplianceCategoryController::class, 'ExportExcel']);
                    Route::GET('/export/pdf', [ComplianceCategoryController::class, 'ExportPDF']);
                    Route::GET('/import', [ComplianceCategoryController::class, 'Import']);
                    Route::POST('/import/Submit', [ComplianceCategoryController::class, 'ImportSubmit']);
                    Route::GET('/sample_download', [ComplianceCategoryController::class, 'DownloadSample']);
                });

                Route::group(['prefix' => 'monthly-audit/audit-plan/'], function () {
                    Route::get('list', [MonthlyAuditPlanController::class, 'index']);
                    Route::post('list', [MonthlyAuditPlanController::class, 'index']);
                    Route::get('add', [MonthlyAuditPlanController::class, 'add']);
                    Route::post('add/submit', [MonthlyAuditPlanController::class, 'store']);
                    Route::get('view/{id}', [MonthlyAuditPlanController::class, 'view']);
                    Route::get('export/excel', [MonthlyAuditPlanController::class, 'exportExcel']);
                    Route::get('export/pdf', [MonthlyAuditPlanController::class, 'exportPdf']);
                    Route::get('generalpdf/{id}', [MonthlyAuditPlanController::class, 'generalpdf']);
                    Route::get('generalExcel/{id}', [MonthlyAuditPlanController::class, 'generalExcel']);
                });

                Route::group(['prefix' => 'inter-unit-audit/checklist/'], function () {
                    Route::get('list', [InterUnitAuditController::class, 'index']);
                    Route::post('list', [InterUnitAuditController::class, 'index']);
                    Route::get('add', [InterUnitAuditController::class, 'add']);
                    Route::post('add/submit', [InterUnitAuditController::class, 'store']);
                    Route::get('edit/{id}', [InterUnitAuditController::class, 'edit']);
                    Route::post('edit/submit', [InterUnitAuditController::class, 'update']);
                    Route::get('view/{id}', [InterUnitAuditController::class, 'view']);
                    Route::post('delete', [InterUnitAuditController::class, 'delete']);
                    Route::get('export/excel', [InterUnitAuditController::class, 'exportExcel']);
                    Route::get('export/pdf', [InterUnitAuditController::class, 'exportPdf']);
                    Route::get('sample_download', [InterUnitAuditController::class, 'DownloadSample']);
                    Route::get('import', [InterUnitAuditController::class, 'import']);
                    Route::post('import/Submit', [InterUnitAuditController::class, 'importSubmit']);
                    Route::post('status', [InterUnitAuditController::class, 'statusChange']);
                    Route::post('unique', [InterUnitAuditController::class, 'Uniquecheck']);
                    Route::get('employeeName', [InterUnitAuditController::class, 'employeename']);
                    Route::get('generalpdf/{id}', [InterUnitAuditController::class, 'generalpdf']);
                    Route::get('generalExcel/{id}', [InterUnitAuditController::class, 'generalExcel']);
                });
            });



            Route::group(['prefix' => 'inspection/gemba-walk/'], function () {
                Route::get('list', [GembaWalkController::class, 'index']);
                Route::post('list', [GembaWalkController::class, 'index']);
                Route::get('add', [GembaWalkController::class, 'add']);
                Route::post('add/submit', [GembaWalkController::class, 'store']);
                Route::get('view/{id}', [GembaWalkController::class, 'view']);
                // Route::get('capa-verification/{id}', [GembaWalkController::class, 'approvals']);
                // Route::post('capa/submit', [GembaWalkController::class, 'CAPASubmit']);
                Route::get('floor-manager/{id}', [GembaWalkController::class, 'review']);
                Route::post('floor-manager/review/submit', [GembaWalkController::class, 'capaReviewSubmit']);
                Route::get('ehs-officer/{id}', [GembaWalkController::class, 'ehsOfficerReview']);
                Route::post('ehs-officer/review/submit', [GembaWalkController::class, 'ehsReviewSubmit']);
                Route::get('generalpdf/{id}', [GembaWalkController::class, 'generalpdf']);
                Route::get('generalExcel/{id}', [GembaWalkController::class, 'generalExcel']);
                Route::get('export/pdf', [GembaWalkController::class, 'exportPdf']);
                Route::get('export/excel', [GembaWalkController::class, 'exportExcel']);
                Route::get('employeeName', [GembaWalkController::class, 'getEmployeeName']);
            });

            Route::group(['prefix' => 'environment/'], function () {
                Route::group(['prefix' => 'ambient-noise/'], function () {
                    Route::get('list', [AmbientNoiseMonitoringController::class, 'index']);
                    Route::post('list', [AmbientNoiseMonitoringController::class, 'index']);
                    Route::get('add', [AmbientNoiseMonitoringController::class, 'add']);
                    Route::post('add/submit', [AmbientNoiseMonitoringController::class, 'store']);
                    Route::get('view/{id}', [AmbientNoiseMonitoringController::class, 'view']);
                    Route::get('generalpdf/{id}', [AmbientNoiseMonitoringController::class, 'generalpdf']);
                    Route::get('generalExcel/{id}', [AmbientNoiseMonitoringController::class, 'generalExcel']);
                    Route::post('delete', [AmbientNoiseMonitoringController::class, 'delete']);
                    Route::get('export/excel', [AmbientNoiseMonitoringController::class, 'exportExcel']);
                    Route::get('export/pdf', [AmbientNoiseMonitoringController::class, 'exportPdf']);
                    Route::post('status', [AmbientNoiseMonitoringController::class, 'statusChange']);
                    Route::post('unique', [AmbientNoiseMonitoringController::class, 'Uniquecheck']);
                });

                Route::group(['prefix' => 'work-noise/'], function () {
                    Route::get('list', [WorkNoiseMonitoringController::class, 'index']);
                    Route::post('list', [WorkNoiseMonitoringController::class, 'index']);
                    Route::get('add', [WorkNoiseMonitoringController::class, 'add']);
                    Route::post('add/submit', [WorkNoiseMonitoringController::class, 'store']);
                    Route::get('view/{id}', [WorkNoiseMonitoringController::class, 'view']);
                    Route::get('generalpdf/{id}', [WorkNoiseMonitoringController::class, 'generalpdf']);
                    Route::get('generalExcel/{id}', [WorkNoiseMonitoringController::class, 'generalExcel']);
                    Route::post('delete', [WorkNoiseMonitoringController::class, 'delete']);
                    Route::get('export/excel', [WorkNoiseMonitoringController::class, 'exportExcel']);
                    Route::get('export/pdf', [WorkNoiseMonitoringController::class, 'exportPdf']);
                    Route::post('status', [WorkNoiseMonitoringController::class, 'statusChange']);
                    Route::post('unique', [WorkNoiseMonitoringController::class, 'Uniquecheck']);
                });

                Route::group(['prefix' => 'ambient-air/yearly/'], function () {
                    Route::get('list', [AmbientAirMonitoringYearlyController::class, 'index']);
                    Route::post('list', [AmbientAirMonitoringYearlyController::class, 'index']);
                    Route::get('add', [AmbientAirMonitoringYearlyController::class, 'add']);
                    Route::post('add/submit', [AmbientAirMonitoringYearlyController::class, 'store']);
                    Route::get('view/{id}', [AmbientAirMonitoringYearlyController::class, 'view']);
                    Route::get('generalpdf/{id}', [AmbientAirMonitoringYearlyController::class, 'generalpdf']);
                    Route::get('generalExcel/{id}', [AmbientAirMonitoringYearlyController::class, 'generalExcel']);
                    Route::post('delete', [AmbientAirMonitoringYearlyController::class, 'delete']);
                    Route::get('export/excel', [AmbientAirMonitoringYearlyController::class, 'exportExcel']);
                    Route::get('export/pdf', [AmbientAirMonitoringYearlyController::class, 'exportPdf']);
                    Route::post('status', [AmbientAirMonitoringYearlyController::class, 'statusChange']);
                });

                Route::group(['prefix' => 'work-zone/air/'], function () {
                    Route::get('list', [WorkZoneAirMonitoringController::class, 'index']);
                    Route::post('list', [WorkZoneAirMonitoringController::class, 'index']);
                    Route::get('add', [WorkZoneAirMonitoringController::class, 'add']);
                    Route::post('add/submit', [WorkZoneAirMonitoringController::class, 'store']);
                    Route::get('view/{id}', [WorkZoneAirMonitoringController::class, 'view']);
                    Route::get('generalpdf/{id}', [WorkZoneAirMonitoringController::class, 'generalpdf']);
                    Route::get('generalExcel/{id}', [WorkZoneAirMonitoringController::class, 'generalExcel']);
                    Route::post('delete', [WorkZoneAirMonitoringController::class, 'delete']);
                    Route::get('export/excel', [WorkZoneAirMonitoringController::class, 'exportExcel']);
                    Route::get('export/pdf', [WorkZoneAirMonitoringController::class, 'exportPdf']);
                    Route::post('status', [WorkZoneAirMonitoringController::class, 'statusChange']);
                });

                Route::group(['prefix' => 'dg-set-stack-emission/'], function () {
                    Route::get('list', [DgSetStackEmissionMonitoringController::class, 'index']);
                    Route::post('list', [DgSetStackEmissionMonitoringController::class, 'index']);
                    Route::get('add', [DgSetStackEmissionMonitoringController::class, 'add']);
                    Route::post('add/submit', [DgSetStackEmissionMonitoringController::class, 'store']);
                    Route::get('view/{id}', [DgSetStackEmissionMonitoringController::class, 'view']);
                    Route::get('generalpdf/{id}', [DgSetStackEmissionMonitoringController::class, 'generalpdf']);
                    Route::get('generalExcel/{id}', [DgSetStackEmissionMonitoringController::class, 'generalExcel']);
                    Route::post('delete', [DgSetStackEmissionMonitoringController::class, 'delete']);
                    Route::get('export/excel', [DgSetStackEmissionMonitoringController::class, 'exportExcel']);
                    Route::get('export/pdf', [DgSetStackEmissionMonitoringController::class, 'exportPdf']);
                    Route::post('status', [DgSetStackEmissionMonitoringController::class, 'statusChange']);
                });

                Route::group(['prefix' => 'lux/'], function () {
                    Route::get('list', [LuxMonitoringController::class, 'index']);
                    Route::post('list', [LuxMonitoringController::class, 'index']);
                    Route::get('add', [LuxMonitoringController::class, 'add']);
                    Route::post('add/submit', [LuxMonitoringController::class, 'store']);
                    Route::get('view/{id}', [LuxMonitoringController::class, 'view']);
                    Route::get('generalpdf/{id}', [LuxMonitoringController::class, 'generalpdf']);
                    Route::get('generalExcel/{id}', [LuxMonitoringController::class, 'generalExcel']);
                    Route::post('delete', [LuxMonitoringController::class, 'delete']);
                    Route::get('export/excel', [LuxMonitoringController::class, 'exportExcel']);
                    Route::get('export/pdf', [LuxMonitoringController::class, 'exportPdf']);
                    Route::post('status', [LuxMonitoringController::class, 'statusChange']);
                });
            });


            Route::group(['prefix' => 'safety/'], function () {
                Route::group(['prefix' => 'master/equipment'], function () {
                    Route::GET('/list', [EquipmentController::class, 'Index']);
                    Route::POST('/list', [EquipmentController::class, 'Index']);
                    Route::GET('/add', [EquipmentController::class, 'Add']);
                    Route::POST('/add/submit', [EquipmentController::class, 'Store']);
                    Route::POST('/unique', [EquipmentController::class, 'UniqueCheck']);
                    Route::GET('/edit/{id}', [EquipmentController::class, 'Edit']);
                    Route::POST('/edit/submit', [EquipmentController::class, 'Update']);
                    Route::GET('/view/{id}', [EquipmentController::class, 'View']);
                    Route::POST('/delete', [EquipmentController::class, 'Delete']);
                    Route::POST('/status', [EquipmentController::class, 'StatusChange']);
                    Route::GET('/export/excel', [EquipmentController::class, 'ExportExcel']);
                    Route::GET('/export/pdf', [EquipmentController::class, 'ExportPDF']);
                    Route::GET('/import', [EquipmentController::class, 'Import']);
                    Route::POST('/import/Submit', [EquipmentController::class, 'ImportSubmit']);
                    Route::GET('/sample_download', [EquipmentController::class, 'DownloadSample']);
                });

                Route::group(['prefix' => 'eye-wash-inspection/monthly/'], function () {
                    Route::GET('list', [MonthlyEyeWashInspectionController::class, 'Index']);
                    Route::POST('list', [MonthlyEyeWashInspectionController::class, 'Index']);
                    Route::GET('add', [MonthlyEyeWashInspectionController::class, 'Add']);
                    Route::POST('add/submit', [MonthlyEyeWashInspectionController::class, 'Store']);
                    Route::GET('get/locations', [MonthlyEyeWashInspectionController::class, 'GetLocations']);
                    Route::GET('view/{id}', [MonthlyEyeWashInspectionController::class, 'View']);
                    Route::GET('verification/{id}/{employee_type}', [MonthlyEyeWashInspectionController::class, 'Approvals']);
                    Route::POST('ehsofficer/verify/submit', [MonthlyEyeWashInspectionController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [MonthlyEyeWashInspectionController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [MonthlyEyeWashInspectionController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [MonthlyEyeWashInspectionController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [MonthlyEyeWashInspectionController::class, 'levelTwoManagerSubmit']);
                    Route::GET('export/excel', [MonthlyEyeWashInspectionController::class, 'exportExcel']);
                    Route::GET('export/pdf', [MonthlyEyeWashInspectionController::class, 'exportPdf']);
                    Route::GET('exportViewPdf/{id}', [MonthlyEyeWashInspectionController::class, 'exportViewPdf']);
                    Route::get('generalexcel/{id}', [MonthlyEyeWashInspectionController::class, 'generalExcel']);
                });

                Route::group(['prefix' => 'forklift-inspection/monthly/'], function () {
                    Route::get('list', [MonthlyForkLiftInspectionController::class, 'index']);
                    Route::post('list', [MonthlyForkLiftInspectionController::class, 'index']);
                    Route::get('add', [MonthlyForkLiftInspectionController::class, 'add']);
                    Route::post('add/submit', [MonthlyForkLiftInspectionController::class, 'store']);
                    Route::get('view/{id}', [MonthlyForkLiftInspectionController::class, 'view']);
                    Route::get('verification/{id}/{employee_type}', [MonthlyForkLiftInspectionController::class, 'approvals']);
                    Route::post('ehsofficer/verify/submit', [MonthlyForkLiftInspectionController::class, 'EHSOfficerSubmit']);
                    Route::post('capa/submit', [MonthlyForkLiftInspectionController::class, 'CAPASubmit']);
                    Route::post('capa/reverify/submit', [MonthlyForkLiftInspectionController::class, 'CAPAVerifySubmit']);
                    Route::post('level-one/verify/submit', [MonthlyForkLiftInspectionController::class, 'levelOneManagerSubmit']);
                    Route::post('level-two/verify/submit', [MonthlyForkLiftInspectionController::class, 'levelTwoManagerSubmit']);
                    Route::get('export/excel', [MonthlyForkLiftInspectionController::class, 'exportExcel']);
                    Route::get('export/pdf', [MonthlyForkLiftInspectionController::class, 'exportPdf']);
                    Route::get('exportViewPdf/{id}', [MonthlyForkLiftInspectionController::class, 'exportViewPdf']);
                    Route::get('generalexcel/{id}', [MonthlyForkLiftInspectionController::class, 'generalExcel']);
                });

                Route::group(['prefix' => 'forklift-inspection/'], function () {
                    Route::get('list', [ForkLiftInspectionController::class, 'index']);
                    Route::post('list', [ForkLiftInspectionController::class, 'index']);
                    Route::get('add', [ForkLiftInspectionController::class, 'add']);
                    Route::post('add/submit', [ForkLiftInspectionController::class, 'store']);
                    Route::get('view/{id}', [ForkLiftInspectionController::class, 'view']);
                    Route::get('export/excel', [ForkLiftInspectionController::class, 'exportExcel']);
                    Route::get('export/pdf', [ForkLiftInspectionController::class, 'exportPdf']);
                    Route::get('exportViewPdf/{id}', [ForkLiftInspectionController::class, 'exportViewPdf']);
                    Route::get('get/department', [ForkLiftInspectionController::class, 'GetDepartment']);
                    Route::get('get/unit', [ForkLiftInspectionController::class, 'GetUnit']);
                    Route::get('approval/{id}', [ForkLiftInspectionController::class, 'approval']);
                    Route::post('verify/submit', [ForkLiftInspectionController::class, 'approvalSubmit']);
                    Route::get('generalexcel/{id}', [ForkLiftInspectionController::class, 'generalExcel']);
                });


                Route::group(['prefix' => 'safety-gallery-inspection/'], function () {
                    Route::get('list', [SafetyGalleryInsepctionController::class, 'index']);
                    Route::post('list', [SafetyGalleryInsepctionController::class, 'index']);
                    Route::get('add', [SafetyGalleryInsepctionController::class, 'add']);
                    Route::post('add/submit', [SafetyGalleryInsepctionController::class, 'store']);
                    Route::get('view/{id}', [SafetyGalleryInsepctionController::class, 'view']);
                    Route::get('verification/{id}/{employee_type}', [SafetyGalleryInsepctionController::class, 'approvals']);
                    Route::post('ehsofficer/verify/submit', [SafetyGalleryInsepctionController::class, 'EHSOfficerSubmit']);
                    Route::post('capa/submit', [SafetyGalleryInsepctionController::class, 'CAPASubmit']);
                    Route::post('capa/reverify/submit', [SafetyGalleryInsepctionController::class, 'CAPAVerifySubmit']);
                    Route::post('level-one/verify/submit', [SafetyGalleryInsepctionController::class, 'levelOneManagerSubmit']);
                    Route::post('level-two/verify/submit', [SafetyGalleryInsepctionController::class, 'levelTwoManagerSubmit']);
                    Route::post('unique', [SafetyGalleryInsepctionController::class, 'UniqueCheck']);
                    Route::get('export/excel', [SafetyGalleryInsepctionController::class, 'exportExcel']);
                    Route::get('export/pdf', [SafetyGalleryInsepctionController::class, 'exportPdf']);
                    Route::get('exportViewPdf/{id}', [SafetyGalleryInsepctionController::class, 'exportViewPdf']);
                    Route::get('generalexcel/{id}', [SafetyGalleryInsepctionController::class, 'generalExcel']);
                });

                Route::group(['prefix' => 'fire-safety-equipment/'], function () {
                    Route::get('list', [FireSafetyEquipmentController::class, 'index']);
                    Route::post('list', [FireSafetyEquipmentController::class, 'index']);
                    Route::get('add', [FireSafetyEquipmentController::class, 'add']);
                    Route::post('add/submit', [FireSafetyEquipmentController::class, 'store']);
                    Route::get('view/{id}', [FireSafetyEquipmentController::class, 'view']);
                    Route::GET('get/equipment', [FireSafetyEquipmentController::class, 'GetEquipment']);
                    Route::get('export/excel', [FireSafetyEquipmentController::class, 'exportExcel']);
                    Route::get('export/pdf', [FireSafetyEquipmentController::class, 'exportPdf']);
                    Route::get('exportViewPdf/{id}', [FireSafetyEquipmentController::class, 'exportViewPdf']);
                    Route::get('generalExcel/{id}', [FireSafetyEquipmentController::class, 'generalExcel']);
                    Route::post('Equipmentunique', [FireSafetyEquipmentController::class, 'Equipmentunique']);
                    Route::POST('/status', [FireSafetyEquipmentController::class, 'StatusChange']);
                    Route::POST('/unique', [FireSafetyEquipmentController::class, 'UniqueCheck']);
                });

                Route::group(['prefix' => 'safety-walk-observation/'], function () {
                    Route::get('list', [SafetyWalkObservationController::class, 'index']);
                    Route::post('list', [SafetyWalkObservationController::class, 'index']);
                    Route::get('add', [SafetyWalkObservationController::class, 'add']);
                    Route::post('add/submit', [SafetyWalkObservationController::class, 'store']);
                    Route::get('view/{id}', [SafetyWalkObservationController::class, 'view']);
                    Route::get('approval/{id}', [SafetyWalkObservationController::class, 'approval']);
                    Route::get('export/excel', [SafetyWalkObservationController::class, 'exportExcel']);
                    Route::get('export/pdf', [SafetyWalkObservationController::class, 'exportPdf']);
                    Route::get('exportViewPdf/{id}', [SafetyWalkObservationController::class, 'exportViewPdf']);
                    Route::post('verify/submit', [SafetyWalkObservationController::class, 'approvalSubmit']);
                    Route::GET('generalExcel/{id}', [SafetyWalkObservationController::class, 'generalExcel']);
                });

                Route::group(['prefix' => 'ohc-plant-summary/'], function () {
                    Route::get('list', [OHSPlantSummaryReportController::class, 'index']);
                    Route::post('list', [OHSPlantSummaryReportController::class, 'index']);
                    Route::get('add', [OHSPlantSummaryReportController::class, 'add']);
                    Route::post('add/submit', [OHSPlantSummaryReportController::class, 'store']);
                    Route::get('view/{id}', [OHSPlantSummaryReportController::class, 'view']);
                    Route::GET('get/equipment', [OHSPlantSummaryReportController::class, 'GetEquipment']);
                    Route::get('export/excel', [OHSPlantSummaryReportController::class, 'exportExcel']);
                    Route::get('export/pdf', [OHSPlantSummaryReportController::class, 'exportPdf']);
                    Route::get('exportViewPdf/{id}', [OHSPlantSummaryReportController::class, 'exportViewPdf']);
                    Route::get('generalexcel/{id}', [OHSPlantSummaryReportController::class, 'generalExcel']);
                });
            });

            Route::group(['prefix' => 'msds/'], function () {
                Route::group(['prefix' => 'master/chemicals'], function () {
                    Route::GET('/list', [ChemicalController::class, 'Index']);
                    Route::POST('/list', [ChemicalController::class, 'Index']);
                    Route::GET('/add', [ChemicalController::class, 'Add']);
                    Route::POST('/add/submit', [ChemicalController::class, 'Store']);
                    Route::POST('/unique', [ChemicalController::class, 'UniqueCheck']);
                    Route::GET('/edit/{id}', [ChemicalController::class, 'Edit']);
                    Route::POST('/edit/submit', [ChemicalController::class, 'Update']);
                    Route::GET('/view/{id}', [ChemicalController::class, 'View']);
                    Route::POST('/delete', [ChemicalController::class, 'Delete']);
                    Route::POST('/status', [ChemicalController::class, 'StatusChange']);
                    Route::GET('/export/excel', [ChemicalController::class, 'ExportExcel']);
                    Route::GET('/export/pdf', [ChemicalController::class, 'ExportPDF']);
                    Route::GET('/import', [ChemicalController::class, 'Import']);
                    Route::POST('/import/Submit', [ChemicalController::class, 'ImportSubmit']);
                    Route::GET('/sample_download', [ChemicalController::class, 'DownloadSample']);
                });

                Route::get('list', [MSDSController::class, 'index']);
                Route::post('list', [MSDSController::class, 'index']);
                Route::get('add', [MSDSController::class, 'add']);
                Route::post('add/submit', [MSDSController::class, 'store']);
                Route::get('edit/{id}', [MSDSController::class, 'edit']);
                Route::post('edit/submit', [MSDSController::class, 'update']);
                Route::get('view/{id}', [MSDSController::class, 'view']);
                Route::post('delete', [MSDSController::class, 'delete']);
                Route::get('export/excel', [MSDSController::class, 'exportExcel']);
                Route::get('export/pdf', [MSDSController::class, 'exportPdf']);
                Route::post('status', [MSDSController::class, 'statusChange']);
                Route::post('unique', [MSDSController::class, 'Uniquecheck']);
                Route::get('generalpdf/{id}', [MSDSController::class, 'generalpdf']);
                Route::GET('generalExcel/{id}', [MSDSController::class, 'generalExcel']);
                Route::POST('getUnit', [MSDSController::class, 'GetUnit']);
                Route::POST('getDepartment', [MSDSController::class, 'getDepartment']);
            });

            Route::group(['prefix' => 'rraa/ohc_fire_environment_compliance/'], function () {
                Route::get('list', [RRAAController::class, 'index']);
                Route::post('list', [RRAAController::class, 'index']);
                Route::get('add', [RRAAController::class, 'add']);
                Route::post('add/submit', [RRAAController::class, 'store']);
                Route::get('view/{id}', [RRAAController::class, 'view']);
                Route::post('delete', [RRAAController::class, 'delete']);
                Route::get('export/excel', [RRAAController::class, 'exportExcel']);
                Route::get('export/pdf', [RRAAController::class, 'exportPdf']);
                Route::post('status', [RRAAController::class, 'statusChange']);
                Route::post('unique', [RRAAController::class, 'Uniquecheck']);
                Route::get('employeeid', [RRAAController::class, 'employeeid']);
                Route::get('generalpdf/{id}', [RRAAController::class, 'generalpdf']);
                Route::GET('generalExcel/{id}', [RRAAController::class, 'generalExcel']);
            });

            Route::group(['prefix' => 'ohc/safety-petty-logbook/'], function () {
                Route::get('list', [SafetyPettyController::class, 'index']);
                Route::post('list', [SafetyPettyController::class, 'index']);
                Route::get('add', [SafetyPettyController::class, 'add']);
                Route::post('add/submit', [SafetyPettyController::class, 'store']);
                Route::get('view/{id}', [SafetyPettyController::class, 'view']);
                Route::post('delete', [SafetyPettyController::class, 'delete']);
                Route::get('export/excel', [SafetyPettyController::class, 'exportExcel']);
                Route::get('export/pdf', [SafetyPettyController::class, 'exportPdf']);
                Route::post('status', [SafetyPettyController::class, 'statusChange']);
                Route::post('unique', [SafetyPettyController::class, 'Uniquecheck']);
                Route::get('employeeid', [SafetyPettyController::class, 'employeeid']);
                Route::get('generalpdf/{id}', [SafetyPettyController::class, 'generalpdf']);
                Route::post('unique', [SafetyPettyController::class, 'uniqueCheck']);
                Route::get('get-signature', [SafetyPettyController::class, 'getSignature']);
                Route::get('generalexcel/{id}', [SafetyPettyController::class, 'generalExcel']);
            });

            Route::group(['prefix' => 'fire/'], function () {
                Route::group(['prefix' => 'hooter-inspection/'], function () {
                    Route::GET('list', [HooterInspectionController::class, 'Index']);
                    Route::POST('list', [HooterInspectionController::class, 'Index']);
                    Route::GET('add', [HooterInspectionController::class, 'Add']);
                    Route::POST('add/submit', [HooterInspectionController::class, 'Store']);
                    Route::GET('view/{id}', [HooterInspectionController::class, 'View']);
                    Route::GET('verification/{id}/{employee_type}', [HooterInspectionController::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [HooterInspectionController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [HooterInspectionController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [HooterInspectionController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [HooterInspectionController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [HooterInspectionController::class, 'levelTwoManagerSubmit']);
                    Route::GET('exportViewPdf/{id}', [HooterInspectionController::class, 'ExportViewPDF']);
                    Route::GET('export/excel', [HooterInspectionController::class, 'ExportExcel']);
                    Route::GET('export/excel/{id}', [HooterInspectionController::class, 'GeneralExcel']);
                    Route::GET('export/pdf', [HooterInspectionController::class, 'ExportPDF']);
                    Route::GET('get/department', [HooterInspectionController::class, 'GetDepartment']);
                });

                Route::group(['prefix' => 'emergency-light-inspection/'], function () {
                    Route::GET('list', [EmergencyLightInspectionController::class, 'Index']);
                    Route::POST('list', [EmergencyLightInspectionController::class, 'Index']);
                    Route::GET('add', [EmergencyLightInspectionController::class, 'Add']);
                    Route::POST('add/submit', [EmergencyLightInspectionController::class, 'Store']);
                    Route::GET('view/{id}', [EmergencyLightInspectionController::class, 'View']);
                    Route::GET('verification/{id}/{employee_type}', [EmergencyLightInspectionController::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [EmergencyLightInspectionController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [EmergencyLightInspectionController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [EmergencyLightInspectionController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [EmergencyLightInspectionController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [EmergencyLightInspectionController::class, 'levelTwoManagerSubmit']);
                    Route::GET('exportViewPdf/{id}', [EmergencyLightInspectionController::class, 'ExportViewPDF']);
                    Route::GET('exportViewExcel/{id}', [EmergencyLightInspectionController::class, 'generalExcel']);
                    Route::GET('export/excel', [EmergencyLightInspectionController::class, 'ExportExcel']);
                    Route::GET('export/pdf', [EmergencyLightInspectionController::class, 'ExportPDF']);
                    Route::GET('get/department', [EmergencyLightInspectionController::class, 'GetDepartment']);
                });

                Route::group(['prefix' => 'monthly-fire-pump-house-inspection/'], function () {
                    Route::GET('list', [MonthlyFirePumpHouseController::class, 'Index']);
                    Route::POST('list', [MonthlyFirePumpHouseController::class, 'Index']);
                    Route::GET('add', [MonthlyFirePumpHouseController::class, 'Add']);
                    Route::POST('add/submit', [MonthlyFirePumpHouseController::class, 'Store']);
                    Route::GET('view/{id}', [MonthlyFirePumpHouseController::class, 'View']);
                    Route::GET('export/excel', [MonthlyFirePumpHouseController::class, 'ExportExcel']);
                    Route::GET('export/pdf', [MonthlyFirePumpHouseController::class, 'ExportPDF']);
                    Route::GET('verification/{id}/{employee_type}', [MonthlyFirePumpHouseController::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [MonthlyFirePumpHouseController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [MonthlyFirePumpHouseController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [MonthlyFirePumpHouseController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [MonthlyFirePumpHouseController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [MonthlyFirePumpHouseController::class, 'levelTwoManagerSubmit']);
                    Route::GET('exportViewPdf/{id}', [MonthlyFirePumpHouseController::class, 'exportViewPdf']);
                    Route::GET('generalExcel/{id}', [MonthlyFirePumpHouseController::class, 'generalExcel']);
                });

                Route::group(['prefix' => 'fire_extinguisher-inspection'], function () {
                    Route::GET('list', [FireExtinguisherController::class, 'Index']);
                    Route::POST('list', [FireExtinguisherController::class, 'Index']);
                    Route::GET('add', [FireExtinguisherController::class, 'Add']);
                    Route::POST('add/submit', [FireExtinguisherController::class, 'Store']);
                    Route::GET('view/{id}', [FireExtinguisherController::class, 'View']);
                    Route::GET('verification/{id}/{employee_type}', [FireExtinguisherController::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [FireExtinguisherController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [FireExtinguisherController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [FireExtinguisherController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [FireExtinguisherController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [FireExtinguisherController::class, 'levelTwoManagerSubmit']);
                    Route::GET('exportViewPdf/{id}', [FireExtinguisherController::class, 'ExportViewPDF']);
                    Route::GET('export/excel', [FireExtinguisherController::class, 'ExportExcel']);
                    Route::GET('export/excel/{id}', [FireExtinguisherController::class, 'GeneralExcel']);
                    Route::GET('export/pdf', [FireExtinguisherController::class, 'ExportPDF']);
                });

                Route::group(['prefix' => 'isolating-valve-inspection'], function () {
                    Route::GET('list', [IsolationValveController::class, 'Index']);
                    Route::POST('list', [IsolationValveController::class, 'Index']);
                    Route::GET('add', [IsolationValveController::class, 'Add']);
                    Route::POST('add/submit', [IsolationValveController::class, 'Store']);
                    Route::GET('view/{id}', [IsolationValveController::class, 'View']);
                    Route::GET('verification/{id}/{employee_type}', [IsolationValveController::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [IsolationValveController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [IsolationValveController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [IsolationValveController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [IsolationValveController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [IsolationValveController::class, 'levelTwoManagerSubmit']);
                    Route::GET('exportViewPdf/{id}', [IsolationValveController::class, 'ExportViewPDF']);
                    Route::GET('export/excel', [IsolationValveController::class, 'ExportExcel']);
                    Route::GET('export/excel/{id}', [IsolationValveController::class, 'GeneralExcel']);
                    Route::GET('export/pdf', [IsolationValveController::class, 'ExportPDF']);
                });

                Route::group(['prefix' => 'fire-mock-drill-observation'], function () {
                    Route::GET('list', [FireMockDrillInspectionController::class, 'Index']);
                    Route::POST('list', [FireMockDrillInspectionController::class, 'Index']);
                    Route::GET('add', [FireMockDrillInspectionController::class, 'Add']);
                    Route::POST('add/submit', [FireMockDrillInspectionController::class, 'Store']);
                    Route::GET('view/{id}', [FireMockDrillInspectionController::class, 'View']);
                    Route::GET('verification/{id}/{employee_type}', [FireMockDrillInspectionController::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [FireMockDrillInspectionController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [FireMockDrillInspectionController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [FireMockDrillInspectionController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [FireMockDrillInspectionController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [FireMockDrillInspectionController::class, 'levelTwoManagerSubmit']);
                    Route::GET('exportViewPdf/{id}', [FireMockDrillInspectionController::class, 'ExportViewPDF']);
                    Route::GET('generalExcel/{id}', [FireMockDrillInspectionController::class, 'generalExcel']);
                    Route::GET('export/excel', [FireMockDrillInspectionController::class, 'ExportExcel']);
                    Route::GET('export/pdf', [FireMockDrillInspectionController::class, 'ExportPDF']);
                });
                Route::group(['prefix' => 'fire-alarm-inspection/'], function () {
                    Route::GET('list', [FireAlarmController::class, 'Index']);
                    Route::POST('list', [FireAlarmController::class, 'Index']);
                    Route::GET('add', [FireAlarmController::class, 'Add']);
                    Route::POST('add/submit', [FireAlarmController::class, 'Store']);
                    Route::GET('view/{id}', [FireAlarmController::class, 'View']);
                    Route::GET('verification/{id}/{employee_type}', [FireAlarmController::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [FireAlarmController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [FireAlarmController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [FireAlarmController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [FireAlarmController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [FireAlarmController::class, 'levelTwoManagerSubmit']);
                    Route::GET('exportViewPdf/{id}', [FireAlarmController::class, 'ExportViewPDF']);
                    Route::GET('export/excel', [FireAlarmController::class, 'ExportExcel']);
                    Route::GET('export/excel/{id}', [FireAlarmController::class, 'GeneralExcel']);
                    Route::GET('export/pdf', [FireAlarmController::class, 'ExportPDF']);
                });

                Route::group(['prefix' => 'sprinkler-inspection'], function () {
                    Route::GET('list', [SprinklarSystemController::class, 'Index']);
                    Route::POST('list', [SprinklarSystemController::class, 'Index']);
                    Route::GET('add', [SprinklarSystemController::class, 'Add']);
                    Route::POST('add/submit', [SprinklarSystemController::class, 'Store']);
                    Route::GET('view/{id}', [SprinklarSystemController::class, 'View']);
                    Route::GET('verification/{id}/{employee_type}', [SprinklarSystemController::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [SprinklarSystemController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [SprinklarSystemController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [SprinklarSystemController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [SprinklarSystemController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [SprinklarSystemController::class, 'levelTwoManagerSubmit']);
                    Route::GET('exportViewPdf/{id}', [SprinklarSystemController::class, 'ExportViewPDF']);
                    Route::GET('export/excel', [SprinklarSystemController::class, 'ExportExcel']);
                    Route::GET('export/excel/{id}', [SprinklarSystemController::class, 'GeneralExcel']);
                    Route::GET('export/pdf', [SprinklarSystemController::class, 'ExportPDF']);
                });

                Route::group(['prefix' => 'certified-fire-fighter/'], function () {
                    Route::get('list', [CertifiedFireFighterController::class, 'index']);
                    Route::post('list', [CertifiedFireFighterController::class, 'index']);
                    Route::get('add', [CertifiedFireFighterController::class, 'add']);
                    Route::post('add/submit', [CertifiedFireFighterController::class, 'store']);
                    Route::get('view/{id}', [CertifiedFireFighterController::class, 'view']);
                    Route::post('delete', [CertifiedFireFighterController::class, 'delete']);
                    Route::get('export/excel', [CertifiedFireFighterController::class, 'exportExcel']);
                    Route::get('export/pdf', [CertifiedFireFighterController::class, 'exportPdf']);
                    Route::post('status', [CertifiedFireFighterController::class, 'statusChange']);
                    Route::GET('exportViewPdf/{id}', [CertifiedFireFighterController::class, 'ExportViewPDF']);
                    Route::GET('generalExcel/{id}', [CertifiedFireFighterController::class, 'generalExcel']);
                });

                Route::group(['prefix' => 'equipment-monthly-physical-inspection/'], function () {
                    Route::get('list', [MonthlyPhysicalInspectionController::class, 'index']);
                    Route::post('list', [MonthlyPhysicalInspectionController::class, 'index']);
                    Route::get('add', [MonthlyPhysicalInspectionController::class, 'add']);
                    Route::post('add/submit', [MonthlyPhysicalInspectionController::class, 'store']);
                    Route::get('view/{id}', [MonthlyPhysicalInspectionController::class, 'view']);
                    Route::post('delete', [MonthlyPhysicalInspectionController::class, 'delete']);
                    Route::get('export/excel', [MonthlyPhysicalInspectionController::class, 'exportExcel']);
                    Route::get('export/pdf', [MonthlyPhysicalInspectionController::class, 'exportPdf']);
                    Route::post('status', [MonthlyPhysicalInspectionController::class, 'statusChange']);
                    Route::GET('exportViewPdf/{id}', [MonthlyPhysicalInspectionController::class, 'ExportViewPDF']);
                    Route::GET('export/excel/{id}', [MonthlyPhysicalInspectionController::class, 'GeneralExcel']);
                });

                Route::group(['prefix' => 'fire-safety/equipments/code-sheet/'], function () {
                    Route::get('list', [FireSafetyEquipmentsController::class, 'index']);
                    Route::post('list', [FireSafetyEquipmentsController::class, 'index']);
                    Route::get('add', [FireSafetyEquipmentsController::class, 'add']);
                    Route::post('add/submit', [FireSafetyEquipmentsController::class, 'store']);
                    Route::get('view/{id}', [FireSafetyEquipmentsController::class, 'view']);
                    Route::post('delete', [FireSafetyEquipmentsController::class, 'delete']);
                    Route::get('export/excel', [FireSafetyEquipmentsController::class, 'exportExcel']);
                    Route::get('export/pdf', [FireSafetyEquipmentsController::class, 'exportPdf']);
                    Route::post('status', [FireSafetyEquipmentsController::class, 'statusChange']);
                    Route::GET('exportViewPdf/{id}', [FireSafetyEquipmentsController::class, 'ExportViewPDF']);
                    Route::GET('generalExcel/{id}', [FireSafetyEquipmentsController::class, 'generalExcel']);
                });

                Route::group(['prefix' => 'detector-inspection/'], function () {
                    Route::GET('list', [DetectorInspectionController::class, 'Index']);
                    Route::POST('list', [DetectorInspectionController::class, 'Index']);
                    Route::GET('add', [DetectorInspectionController::class, 'Add']);
                    Route::POST('add/submit', [DetectorInspectionController::class, 'Store']);
                    Route::GET('view/{id}', [DetectorInspectionController::class, 'View']);
                    Route::GET('verification/{id}/{employee_type}', [DetectorInspectionController::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [DetectorInspectionController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [DetectorInspectionController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [DetectorInspectionController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [DetectorInspectionController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [DetectorInspectionController::class, 'levelTwoManagerSubmit']);
                    Route::GET('exportViewPdf/{id}', [DetectorInspectionController::class, 'ExportViewPDF']);
                    Route::GET('generalExcel/{id}', [DetectorInspectionController::class, 'generalExcel']);
                    Route::GET('export/excel', [DetectorInspectionController::class, 'ExportExcel']);
                    Route::GET('export/pdf', [DetectorInspectionController::class, 'ExportPDF']);
                    Route::GET('get/department', [DetectorInspectionController::class, 'GetDepartment']);
                });

                Route::group(['prefix' => 'daily-fire-pump-house-inspection/'], function () {
                    Route::get('list', [FirePumpHouseController::class, 'index']);
                    Route::post('list', [FirePumpHouseController::class, 'index']);
                    Route::get('add', [FirePumpHouseController::class, 'add']);
                    Route::post('add/submit', [FirePumpHouseController::class, 'store']);
                    Route::get('edit/{id}', [FirePumpHouseController::class, 'edit']);
                    Route::post('edit/submit', [FirePumpHouseController::class, 'update']);
                    Route::get('view/{id}', [FirePumpHouseController::class, 'view']);
                    Route::post('delete', [FirePumpHouseController::class, 'delete']);
                    Route::get('export/excel', [FirePumpHouseController::class, 'exportExcel']);
                    Route::get('export/pdf', [FirePumpHouseController::class, 'exportPdf']);
                    Route::get('sample_download', [FirePumpHouseController::class, 'DownloadSample']);
                    Route::get('import', [FirePumpHouseController::class, 'import']);
                    Route::post('import/Submit', [FirePumpHouseController::class, 'importSubmit']);
                    Route::post('status', [FirePumpHouseController::class, 'statusChange']);
                    Route::post('unique', [FirePumpHouseController::class, 'Uniquecheck']);
                    Route::get('employeeName', [FirePumpHouseController::class, 'employeename']);
                    Route::get('generalpdf/{id}', [FirePumpHouseController::class, 'generalpdf']);
                    Route::get('generalExcel/{id}', [FirePumpHouseController::class, 'generalExcel']);
                });

                Route::group(['prefix' => 'pre-noc/checklist/'], function () {
                    Route::get('list', [FirePreNocController::class, 'index']);
                    Route::post('list', [FirePreNocController::class, 'index']);
                    Route::get('add', [FirePreNocController::class, 'add']);
                    Route::post('add/submit', [FirePreNocController::class, 'store']);
                    Route::get('edit/{id}', [FirePreNocController::class, 'edit']);
                    Route::post('edit/submit', [FirePreNocController::class, 'update']);
                    Route::get('view/{id}', [FirePreNocController::class, 'view']);
                    Route::get('generalpdf/{id}', [FirePreNocController::class, 'generalpdf']);
                    Route::get('generalExcel/{id}', [FirePreNocController::class, 'generalExcel']);
                    Route::post('delete', [FirePreNocController::class, 'delete']);
                    Route::get('export/excel', [FirePreNocController::class, 'exportExcel']);
                    Route::get('export/pdf', [FirePreNocController::class, 'exportPdf']);
                    Route::get('sample_download', [FirePreNocController::class, 'DownloadSample']);
                    Route::get('import', [FirePreNocController::class, 'import']);
                    Route::post('import/Submit', [FirePreNocController::class, 'importSubmit']);
                    Route::post('status', [FirePreNocController::class, 'statusChange']);
                    Route::post('unique', [FirePreNocController::class, 'Uniquecheck']);
                    Route::get('employeeName', [FirePreNocController::class, 'employeename']);
                    Route::get('generalpdf/{id}', [FirePreNocController::class, 'generalpdf']);
                });
                Route::group(['prefix' => 'pa-system-inspection'], function () {
                    Route::GET('list', [PASystemInspectionController::class, 'Index']);
                    Route::POST('list', [PASystemInspectionController::class, 'Index']);
                    Route::GET('add', [PASystemInspectionController::class, 'Add']);
                    Route::POST('add/submit', [PASystemInspectionController::class, 'Store']);
                    Route::GET('view/{id}', [PASystemInspectionController::class, 'View']);
                    Route::GET('verification/{id}/{employee_type}', [PASystemInspectionController::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [PASystemInspectionController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [PASystemInspectionController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [PASystemInspectionController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [PASystemInspectionController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [PASystemInspectionController::class, 'levelTwoManagerSubmit']);
                    Route::GET('exportViewPdf/{id}', [PASystemInspectionController::class, 'ExportViewPDF']);
                    Route::GET('generalExcel/{id}', [PASystemInspectionController::class, 'generalExcel']);
                    Route::GET('export/excel', [PASystemInspectionController::class, 'ExportExcel']);
                    Route::GET('export/pdf', [PASystemInspectionController::class, 'ExportPDF']);
                    Route::GET('get/locations', [PASystemInspectionController::class, 'GetLocations']);
                });

                Route::group(['prefix' => 'fire-extinguisher/co2/'], function () {
                    Route::GET('list', [CoTypeFireExtinguisherController::class, 'Index']);
                    Route::POST('list', [CoTypeFireExtinguisherController::class, 'Index']);
                    Route::GET('add', [CoTypeFireExtinguisherController::class, 'Add']);
                    Route::POST('add/submit', [CoTypeFireExtinguisherController::class, 'Store']);
                    Route::GET('view/{id}', [CoTypeFireExtinguisherController::class, 'View']);
                    Route::GET('verification/{id}/{employee_type}', [CoTypeFireExtinguisherController::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [CoTypeFireExtinguisherController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [CoTypeFireExtinguisherController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [CoTypeFireExtinguisherController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [CoTypeFireExtinguisherController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [CoTypeFireExtinguisherController::class, 'levelTwoManagerSubmit']);
                    Route::GET('exportViewPdf/{id}', [CoTypeFireExtinguisherController::class, 'ExportViewPDF']);
                    Route::GET('export/excel', [CoTypeFireExtinguisherController::class, 'ExportExcel']);
                    Route::GET('export/pdf', [CoTypeFireExtinguisherController::class, 'ExportPDF']);
                    Route::GET('get/department', [CoTypeFireExtinguisherController::class, 'GetDepartment']);
                    Route::GET('generalExcel/{id}', [CoTypeFireExtinguisherController::class, 'generalExcel']);
                });

                Route::group(['prefix' => 'fire-sand-bucket-inspection/'], function () {
                    Route::GET('list', [SandBucketInspectionController::class, 'Index']);
                    Route::POST('list', [SandBucketInspectionController::class, 'Index']);
                    Route::GET('add', [SandBucketInspectionController::class, 'Add']);
                    Route::POST('add/submit', [SandBucketInspectionController::class, 'Store']);
                    Route::GET('view/{id}', [SandBucketInspectionController::class, 'View']);
                    Route::GET('verification/{id}/{employee_type}', [SandBucketInspectionController::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [SandBucketInspectionController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [SandBucketInspectionController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [SandBucketInspectionController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [SandBucketInspectionController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [SandBucketInspectionController::class, 'levelTwoManagerSubmit']);
                    Route::GET('exportViewPdf/{id}', [SandBucketInspectionController::class, 'ExportViewPDF']);
                    Route::GET('export/excel', [SandBucketInspectionController::class, 'ExportExcel']);
                    Route::GET('export/pdf', [SandBucketInspectionController::class, 'ExportPDF']);
                    Route::GET('get/department', [SandBucketInspectionController::class, 'GetDepartment']);
                    Route::GET('generalExcel/{id}', [SandBucketInspectionController::class, 'generalExcel']);
                });

                Route::group(['prefix' => 'hose-box-inspection'], function () {
                    Route::GET('list', [HoseBoxController::class, 'Index']);
                    Route::POST('list', [HoseBoxController::class, 'Index']);
                    Route::GET('add', [HoseBoxController::class, 'Add']);
                    Route::POST('add/submit', [HoseBoxController::class, 'Store']);
                    Route::GET('view/{id}', [HoseBoxController::class, 'View']);
                    Route::GET('verification/{id}/{employee_type}', [HoseBoxController::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [HoseBoxController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [HoseBoxController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [HoseBoxController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [HoseBoxController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [HoseBoxController::class, 'levelTwoManagerSubmit']);
                    Route::GET('exportViewPdf/{id}', [HoseBoxController::class, 'ExportViewPDF']);
                    Route::GET('export/excel', [HoseBoxController::class, 'ExportExcel']);
                    Route::GET('exportExcel/{id}', [HoseBoxController::class, 'GeneralExcel']);
                    Route::GET('export/pdf', [HoseBoxController::class, 'ExportPDF']);
                });

                Route::group(['prefix' => 'checklist-observation/'], function () {
                    Route::get('list', [ChecklistObservationFollowupController::class, 'index']);
                    Route::post('list', [ChecklistObservationFollowupController::class, 'index']);
                    Route::get('add/{inspection_type}/{inspection_id}', [ChecklistObservationFollowupController::class, 'add']);
                    Route::post('add/submit', [ChecklistObservationFollowupController::class, 'store']);
                    Route::get('edit/{id}', [ChecklistObservationFollowupController::class, 'edit']);
                    Route::post('edit/submit', [ChecklistObservationFollowupController::class, 'update']);
                    Route::get('view/{id}/{observationid}', [ChecklistObservationFollowupController::class, 'view']);
                    Route::get('generalpdf/{id}/{observationid}', [ChecklistObservationFollowupController::class, 'generalpdf']);
                    Route::get('generalExcel/{id}/{observationid}', [ChecklistObservationFollowupController::class, 'generalExcel']);
                    Route::post('delete', [ChecklistObservationFollowupController::class, 'delete']);
                    Route::GET('verification/{id}/{observationid}', [ChecklistObservationFollowupController::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [ChecklistObservationFollowupController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [ChecklistObservationFollowupController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [ChecklistObservationFollowupController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [ChecklistObservationFollowupController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [ChecklistObservationFollowupController::class, 'levelTwoManagerSubmit']);
                    Route::get('export/excel', [ChecklistObservationFollowupController::class, 'exportExcel']);
                    Route::get('export/pdf', [ChecklistObservationFollowupController::class, 'exportPdf']);
                    Route::get('sample_download', [ChecklistObservationFollowupController::class, 'DownloadSample']);
                    Route::get('import', [ChecklistObservationFollowupController::class, 'import']);
                    Route::post('import/Submit', [ChecklistObservationFollowupController::class, 'importSubmit']);
                    Route::post('status', [ChecklistObservationFollowupController::class, 'statusChange']);
                    Route::post('unique', [ChecklistObservationFollowupController::class, 'Uniquecheck']);
                    Route::get('employeeName', [ChecklistObservationFollowupController::class, 'employeename']);
                });
                Route::group(['prefix' => 'fire-extinguisher/cartridge/'], function () {
                    Route::GET('list', [CartridgeTypeFireExtinguisherController::class, 'Index']);
                    Route::POST('list', [CartridgeTypeFireExtinguisherController::class, 'Index']);
                    Route::GET('add', [CartridgeTypeFireExtinguisherController::class, 'Add']);
                    Route::POST('add/submit', [CartridgeTypeFireExtinguisherController::class, 'Store']);
                    Route::GET('view/{id}', [CartridgeTypeFireExtinguisherController::class, 'View']);
                    Route::GET('verification/{id}/{employee_type}', [CartridgeTypeFireExtinguisherController::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [CartridgeTypeFireExtinguisherController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [CartridgeTypeFireExtinguisherController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [CartridgeTypeFireExtinguisherController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [CartridgeTypeFireExtinguisherController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [CartridgeTypeFireExtinguisherController::class, 'levelTwoManagerSubmit']);
                    Route::GET('exportViewPdf/{id}', [CartridgeTypeFireExtinguisherController::class, 'ExportViewPDF']);
                    Route::GET('generalExcel/{id}', [CartridgeTypeFireExtinguisherController::class, 'generalExcel']);
                    Route::GET('export/excel', [CartridgeTypeFireExtinguisherController::class, 'ExportExcel']);
                    Route::GET('export/pdf', [CartridgeTypeFireExtinguisherController::class, 'ExportPDF']);
                    Route::GET('get/department', [CartridgeTypeFireExtinguisherController::class, 'GetDepartment']);
                });

                Route::group(['prefix' => 'hose-reel-hose-inspection'], function () {
                    Route::GET('list', [HoseReelHoseController::class, 'Index']);
                    Route::POST('list', [HoseReelHoseController::class, 'Index']);
                    Route::GET('add', [HoseReelHoseController::class, 'Add']);
                    Route::POST('add/submit', [HoseReelHoseController::class, 'Store']);
                    Route::GET('view/{id}', [HoseReelHoseController::class, 'View']);
                    Route::GET('verification/{id}/{employee_type}', [HoseReelHoseController::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [HoseReelHoseController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [HoseReelHoseController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [HoseReelHoseController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [HoseReelHoseController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [HoseReelHoseController::class, 'levelTwoManagerSubmit']);
                    Route::GET('exportViewPdf/{id}', [HoseReelHoseController::class, 'ExportViewPDF']);
                    Route::GET('export/excel', [HoseReelHoseController::class, 'ExportExcel']);
                    Route::GET('export/excel/{id}', [HoseReelHoseController::class, 'GeneralExcel']);
                    Route::GET('export/pdf', [HoseReelHoseController::class, 'ExportPDF']);
                });
                Route::group(['prefix' => 'fire-modular-inspection/checklist/'], function () {
                    Route::GET('list', [FireModularInspectionController::class, 'Index']);
                    Route::POST('list', [FireModularInspectionController::class, 'Index']);
                    Route::GET('add', [FireModularInspectionController::class, 'Add']);
                    Route::POST('add/submit', [FireModularInspectionController::class, 'Store']);
                    Route::GET('view/{id}', [FireModularInspectionController::class, 'View']);
                    Route::GET('verification/{id}/{employee_type}', [FireModularInspectionController::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [FireModularInspectionController::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [FireModularInspectionController::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [FireModularInspectionController::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [FireModularInspectionController::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [FireModularInspectionController::class, 'levelTwoManagerSubmit']);
                    Route::GET('exportViewPdf/{id}', [FireModularInspectionController::class, 'ExportViewPDF']);
                    Route::GET('generalExcel/{id}', [FireModularInspectionController::class, 'generalExcel']);
                    Route::GET('export/excel', [FireModularInspectionController::class, 'ExportExcel']);
                    Route::GET('export/pdf', [FireModularInspectionController::class, 'ExportPDF']);
                    Route::GET('get/department', [FireModularInspectionController::class, 'GetDepartment']);
                });

                Route::group(['prefix' => 'hydrant-riser-inspection/'], function () {
                    Route::GET('list', [HydrantRiserInspectionContoller::class, 'Index']);
                    Route::POST('list', [HydrantRiserInspectionContoller::class, 'Index']);
                    Route::GET('add', [HydrantRiserInspectionContoller::class, 'Add']);
                    Route::POST('add/submit', [HydrantRiserInspectionContoller::class, 'Store']);
                    Route::GET('view/{id}', [HydrantRiserInspectionContoller::class, 'View']);
                    Route::GET('verification/{id}/{employee_type}', [HydrantRiserInspectionContoller::class, 'approvals']);
                    Route::POST('ehsofficer/verify/submit', [HydrantRiserInspectionContoller::class, 'EHSOfficerSubmit']);
                    Route::POST('capa/submit', [HydrantRiserInspectionContoller::class, 'CAPASubmit']);
                    Route::POST('capa/reverify/submit', [HydrantRiserInspectionContoller::class, 'CAPAVerifySubmit']);
                    Route::POST('level-one/verify/submit', [HydrantRiserInspectionContoller::class, 'levelOneManagerSubmit']);
                    Route::POST('level-two/verify/submit', [HydrantRiserInspectionContoller::class, 'levelTwoManagerSubmit']);
                    Route::GET('exportViewPdf/{id}', [HydrantRiserInspectionContoller::class, 'ExportViewPDF']);
                    Route::GET('generalExcel/{id}', [HydrantRiserInspectionContoller::class, 'generalExcel']);
                    Route::GET('export/excel', [HydrantRiserInspectionContoller::class, 'ExportExcel']);
                    Route::GET('export/pdf', [HydrantRiserInspectionContoller::class, 'ExportPDF']);
                    Route::GET('get/department', [HydrantRiserInspectionContoller::class, 'GetDepartment']);
                });
            });

            Route::group(['prefix' => 'ohc/floor_stretcher/checklist/'], function () {
                Route::GET('list', [FloorStretcherController::class, 'Index']);
                Route::POST('list', [FloorStretcherController::class, 'Index']);
                Route::GET('add', [FloorStretcherController::class, 'Add']);
                Route::POST('add/submit', [FloorStretcherController::class, 'Store']);
                Route::GET('view/{id}', [FloorStretcherController::class, 'View']);
                Route::GET('export/excel', [FloorStretcherController::class, 'ExportExcel']);
                Route::GET('export/pdf', [FloorStretcherController::class, 'ExportPdf']);
                Route::GET('exportViewPdf/{id}', [FloorStretcherController::class, 'ExportViewPDF']);
                Route::GET('export/excel/{id}', [FloorStretcherController::class, 'GeneralExcel']);
                Route::POST('status', [FloorStretcherController::class, 'StatusChange']);
                Route::POST('delete', [FloorStretcherController::class, 'Delete']);
            });


            Route::group(['prefix' => 'ohc/first-aid-record/'], function () {
                Route::get('list', [FirstAidRecordController::class, 'index']);
                Route::post('list', [FirstAidRecordController::class, 'index']);
                Route::get('add', [FirstAidRecordController::class, 'add']);
                Route::post('add/submit', [FirstAidRecordController::class, 'store']);
                Route::get('view/{id}', [FirstAidRecordController::class, 'view']);
                Route::get('export/excel', [FirstAidRecordController::class, 'exportExcel']);
                Route::get('export/pdf', [FirstAidRecordController::class, 'exportPdf']);
                Route::post('status', [FirstAidRecordController::class, 'statusChange']);
                Route::get('generalpdf/{id}', [FirstAidRecordController::class, 'generalpdf']);
                Route::get('first-aid-location/details', [FirstAidRecordController::class, 'getFirstAidDetails']);
                Route::post('unique', [FirstAidRecordController::class, 'Uniquecheck']);
                Route::get('generalexcel/{id}', [FirstAidRecordController::class, 'generalExcel']);
            });

            Route::group(['prefix' => 'ohc/health-instrument/calibration-track-sheet/'], function () {
                Route::get('list', [HealthInstrumentCalibrationController::class, 'index']);
                Route::post('list', [HealthInstrumentCalibrationController::class, 'index']);
                Route::get('add', [HealthInstrumentCalibrationController::class, 'add']);
                Route::post('add/submit', [HealthInstrumentCalibrationController::class, 'store']);
                Route::get('view/{id}', [HealthInstrumentCalibrationController::class, 'view']);
                Route::get('generalpdf/{id}', [HealthInstrumentCalibrationController::class, 'generalpdf']);
                Route::get('generalExcel/{id}', [HealthInstrumentCalibrationController::class, 'generalExcel']);
                Route::get('export/pdf', [HealthInstrumentCalibrationController::class, 'exportPdf']);
                Route::get('export/excel', [HealthInstrumentCalibrationController::class, 'exportExcel']);
                Route::post('status', [HealthInstrumentCalibrationController::class, 'statusChange']);
            });

            Route::group(['prefix' => 'ohc/monthly-medicine-store/inspection/'], function () {
                Route::get('list', [MonthlyMedicineStoreController::class, 'index']);
                Route::post('list', [MonthlyMedicineStoreController::class, 'index']);
                Route::get('add', [MonthlyMedicineStoreController::class, 'add']);
                Route::post('add/submit', [MonthlyMedicineStoreController::class, 'store']);
                Route::get('view/{id}', [MonthlyMedicineStoreController::class, 'view']);
                Route::get('generalpdf/{id}', [MonthlyMedicineStoreController::class, 'generalpdf']);
                Route::get('export/pdf', [MonthlyMedicineStoreController::class, 'exportPdf']);
                Route::get('export/excel', [MonthlyMedicineStoreController::class, 'exportExcel']);
                Route::post('status', [MonthlyMedicineStoreController::class, 'statusChange']);
                Route::GET('exportViewpdf/{id}', [MonthlyMedicineStoreController::class, 'ExportViewPDF']);
                Route::get('generalexcel/{id}', [MonthlyMedicineStoreController::class, 'generalExcel']);
                Route::get('approval/{id}', [MonthlyMedicineStoreController::class, 'approval']);
                Route::post('verify/submit', [MonthlyMedicineStoreController::class, 'approvalSubmit']);
            });
            Route::group(['prefix' => 'ohc/first-aid/opd-medicine-inspection/'], function () {
                Route::get('list', [FirstAidMedicineInspectionController::class, 'index']);
                Route::post('list', [FirstAidMedicineInspectionController::class, 'index']);
                Route::get('add', [FirstAidMedicineInspectionController::class, 'add']);
                Route::post('add/submit', [FirstAidMedicineInspectionController::class, 'store']);
                Route::get('view/{id}', [FirstAidMedicineInspectionController::class, 'view']);
                Route::get('generalpdf/{id}', [FirstAidMedicineInspectionController::class, 'generalpdf']);
                Route::get('export/pdf', [FirstAidMedicineInspectionController::class, 'exportPdf']);
                Route::get('export/excel', [FirstAidMedicineInspectionController::class, 'exportExcel']);
                Route::get('generalexcel/{id}', [FirstAidMedicineInspectionController::class, 'generalExcel']);
                Route::post('status', [FirstAidMedicineInspectionController::class, 'statusChange']);
                Route::GET('exportViewpdf/{id}', [FirstAidMedicineInspectionController::class, 'ExportViewPDF']);
                Route::get('approval/{id}', [FirstAidMedicineInspectionController::class, 'approval']);
                Route::post('verify/submit', [FirstAidMedicineInspectionController::class, 'approvalSubmit']);
            });
            Route::group(['prefix' => 'ohc/emergency-floor-first-aid-bag/checklist/'], function () {
                Route::get('list', [FirstAidBagChecklistController::class, 'index']);
                Route::post('list', [FirstAidBagChecklistController::class, 'index']);
                Route::get('fetchemployeename', [FirstAidBagChecklistController::class, 'fetchemployeename']);
                Route::get('add', [FirstAidBagChecklistController::class, 'add']);
                Route::post('add/submit', [FirstAidBagChecklistController::class, 'store']);
                Route::get('view/{id}', [FirstAidBagChecklistController::class, 'view']);
                Route::get('generalpdf/{id}', [FirstAidBagChecklistController::class, 'generalpdf']);
                Route::get('export/pdf', [FirstAidBagChecklistController::class, 'exportPdf']);
                Route::get('export/excel', [FirstAidBagChecklistController::class, 'exportExcel']);
                Route::post('status', [FirstAidBagChecklistController::class, 'statusChange']);
                Route::GET('exportViewpdf/{id}', [FirstAidBagChecklistController::class, 'ExportViewPDF']);
                Route::get('generalexcel/{id}', [FirstAidBagChecklistController::class, 'generalExcel']);
                Route::get('approval/{id}', [FirstAidBagChecklistController::class, 'approval']);
                Route::post('verify/submit', [FirstAidBagChecklistController::class, 'approvalSubmit']);
            });

            Route::group(['prefix' => 'ohc/master/first-aid-stock/'], function () {
                Route::get('list', [FirstAidController::class, 'index']);
                Route::post('list', [FirstAidController::class, 'index']);
                Route::get('add', [FirstAidController::class, 'add']);
                Route::post('add/submit', [FirstAidController::class, 'store']);
                Route::post('unique', [FirstAidController::class, 'UniqueCheck']);
                Route::get('view/{id}', [FirstAidController::class, 'view']);
                Route::get('edit/{id}', [FirstAidController::class, 'edit']);
                Route::post('edit/submit', [FirstAidController::class, 'update']);
                Route::post('delete', [FirstAidController::class, 'Delete']);
                Route::post('status', [FirstAidController::class, 'StatusChange']);
                Route::get('export/pdf', [FirstAidController::class, 'ExportPDF']);
                Route::get('export/excel', [FirstAidController::class, 'ExportExcel']);
                Route::get('import', [FirstAidController::class, 'Import']);
                Route::post('import/Submit', [FirstAidController::class, 'ImportSubmit']);
                Route::get('sample_download', [FirstAidController::class, 'DownloadSample']);
            });


            Route::group(['prefix' => 'ohc/ohc-hygiene-cleaning-checklist/'], function () {
                Route::get('list', [OHCHygieneCleaningChecklistController::class, 'index']);
                Route::post('list', [OHCHygieneCleaningChecklistController::class, 'index']);
                Route::get('add', [OHCHygieneCleaningChecklistController::class, 'add']);
                Route::post('add/submit', [OHCHygieneCleaningChecklistController::class, 'store']);
                Route::get('view/{id}', [OHCHygieneCleaningChecklistController::class, 'view']);
                Route::get('export/pdf', [OHCHygieneCleaningChecklistController::class, 'ExportPDF']);
                Route::get('export/excel', [OHCHygieneCleaningChecklistController::class, 'ExportExcel']);
                Route::get('approval/{id}', [OHCHygieneCleaningChecklistController::class, 'approval']);
                Route::post('verify/submit', [OHCHygieneCleaningChecklistController::class, 'approvalSubmit']);
                Route::get('generalexcel/{id}', [OHCHygieneCleaningChecklistController::class, 'generalExcel']);
                Route::get('generalpdf/{id}', [OHCHygieneCleaningChecklistController::class, 'generalpdf']);
            });


            Route::group(['prefix' => 'ohc/physical-medical-examination/yearly/'], function () {
                Route::get('list', [PhysicalMedicalExaminationController::class, 'index']);
                Route::post('list', [PhysicalMedicalExaminationController::class, 'index']);
                Route::get('add', [PhysicalMedicalExaminationController::class, 'add']);
                Route::post('add/submit', [PhysicalMedicalExaminationController::class, 'store']);
                Route::post('status', [PhysicalMedicalExaminationController::class, 'statuschange']);
                Route::get('view/{id}', [PhysicalMedicalExaminationController::class, 'view']);
                Route::get('export/pdf', [PhysicalMedicalExaminationController::class, 'ExportPDF']);
                Route::get('export/excel', [PhysicalMedicalExaminationController::class, 'ExportExcel']);
                Route::get('approval/{id}', [PhysicalMedicalExaminationController::class, 'approval']);
                Route::post('verify/submit', [PhysicalMedicalExaminationController::class, 'approvalSubmit']);
                Route::get('generalexcel/{id}', [PhysicalMedicalExaminationController::class, 'generalExcel']);
                Route::get('generalpdf/{id}', [PhysicalMedicalExaminationController::class, 'generalpdf']);
            });


            Route::group(['prefix' => 'ohc/first-aid-box/weekly-inspection/'], function () {
                Route::get('list', [WeeklyFirstAidBoxController::class, 'index']);
                Route::post('list', [WeeklyFirstAidBoxController::class, 'index']);
                Route::get('add', [WeeklyFirstAidBoxController::class, 'add']);
                Route::post('add/submit', [WeeklyFirstAidBoxController::class, 'store']);
                Route::get('view/{id}', [WeeklyFirstAidBoxController::class, 'view']);
                Route::get('generalpdf/{id}', [WeeklyFirstAidBoxController::class, 'generalpdf']);
                Route::get('generalExcel/{id}', [WeeklyFirstAidBoxController::class, 'generalExcel']);
                Route::get('export/pdf', [WeeklyFirstAidBoxController::class, 'ExportPDF']);
                Route::get('export/excel', [WeeklyFirstAidBoxController::class, 'ExportExcel']);
            });

            Route::group(['prefix' => 'ohc/emergency-buyer-first-aid-bag/checklist/'], function () {
                Route::get('list', [EmergencyBuyerFirstAidBagChecklistController::class, 'index']);
                Route::post('list', [EmergencyBuyerFirstAidBagChecklistController::class, 'index']);
                Route::get('add', [EmergencyBuyerFirstAidBagChecklistController::class, 'add']);
                Route::post('add/submit', [EmergencyBuyerFirstAidBagChecklistController::class, 'store']);
                Route::get('view/{id}', [EmergencyBuyerFirstAidBagChecklistController::class, 'view']);
                Route::get('generalpdf/{id}', [EmergencyBuyerFirstAidBagChecklistController::class, 'generalpdf']);
                Route::get('generalExcel/{id}', [EmergencyBuyerFirstAidBagChecklistController::class, 'generalExcel']);
                Route::get('export/pdf', [EmergencyBuyerFirstAidBagChecklistController::class, 'ExportPDF']);
                Route::get('export/excel', [EmergencyBuyerFirstAidBagChecklistController::class, 'ExportExcel']);
            });


            Route::group(['prefix' => 'ohc/daily-vital-equipment'], function () {
                Route::GET('list', [DailyVitalEquipmentController::class, 'Index']);
                Route::POST('list', [DailyVitalEquipmentController::class, 'Index']);
                Route::GET('add', [DailyVitalEquipmentController::class, 'Add']);
                Route::POST('add/submit', [DailyVitalEquipmentController::class, 'Store']);
                Route::GET('view/{id}', [DailyVitalEquipmentController::class, 'View']);
                Route::GET('export/excel', [DailyVitalEquipmentController::class, 'ExportExcel']);
                Route::GET('export/pdf', [DailyVitalEquipmentController::class, 'ExportPDF']);
                Route::GET('exportViewPdf/{id}', [DailyVitalEquipmentController::class, 'exportViewPdf']);
                Route::get('generalexcel/{id}', [DailyVitalEquipmentController::class, 'generalExcel']);
            });



            Route::group(['prefix' => 'ohc/current-new-ext-code-dialing/'], function () {
                Route::get('list', [CurrentNewExtCodeDialingController::class, 'Index']);
                Route::post('list', [CurrentNewExtCodeDialingController::class, 'Index']);
                Route::get('add', [CurrentNewExtCodeDialingController::class, 'add']);
                Route::post('add/submit', [CurrentNewExtCodeDialingController::class, 'store']);
                Route::get('view/{id}', [CurrentNewExtCodeDialingController::class, 'View']);
                Route::get('edit/{id}', [CurrentNewExtCodeDialingController::class, 'edit']);
                Route::post('edit/submit', [CurrentNewExtCodeDialingController::class, 'update']);
                Route::get('export/pdf', [CurrentNewExtCodeDialingController::class, 'ExportPDF']);
                Route::get('export/excel', [CurrentNewExtCodeDialingController::class, 'ExportExcel']);
                Route::post('unique', [CurrentNewExtCodeDialingController::class, 'UniqueCheck']);
                Route::post('delete', [CurrentNewExtCodeDialingController::class, 'Delete']);
                Route::post('status', [CurrentNewExtCodeDialingController::class, 'StatusChange']);
                Route::get('import', [CurrentNewExtCodeDialingController::class, 'Import']);
                Route::post('import/Submit', [CurrentNewExtCodeDialingController::class, 'ImportSubmit']);
                Route::get('sample_download', [CurrentNewExtCodeDialingController::class, 'DownloadSample']);
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
                Route::get('/unit/ajax-list/{companyId}/{id}', [SafetyPermitController::class, 'unitList']);
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

            Route::group(['prefix' => 'ohc'], function () {
                Route::get('/dashboard', [OhcDashboardController::class, 'index']);
                Route::get('/dashboard/medicine-requisition', [OhcDashboardController::class, 'medicineRequisition']);
            });

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

            Route::group(['prefix' => 'ohc/hospital-details'], function () {
                Route::get('/list', [HospitalDetailsController::class, 'index']);
                Route::post('/list', [HospitalDetailsController::class, 'index']);
                Route::get('/add', [HospitalDetailsController::class, 'add']);
                Route::post('/add/submit', [HospitalDetailsController::class, 'store']);
                Route::get('/edit/{id}', [HospitalDetailsController::class, 'edit']);
                Route::post('/edit/submit', [HospitalDetailsController::class, 'update']);
                Route::get('/view/{id}', [HospitalDetailsController::class, 'view']);
                Route::post('/delete', [HospitalDetailsController::class, 'delete']);
                Route::get('/export/excel', [HospitalDetailsController::class, 'exportExcel']);
                Route::get('/export/pdf', [HospitalDetailsController::class, 'exportPdf']);
                Route::get('/sampledownload', [HospitalDetailsController::class, 'DownloadSample']);
                Route::get('/import', [HospitalDetailsController::class, 'import']);
                Route::post('/import/submit', [HospitalDetailsController::class, 'importSubmit']);
                Route::post('/status', [HospitalDetailsController::class, 'statusChange']);
                Route::post('/unique', [HospitalDetailsController::class, 'Uniquecheck']);
                Route::post('/hsn-unique', [HospitalDetailsController::class, 'hsnNumber']);
                Route::get('/approval/view/{id}', [HospitalDetailsController::class, 'approval']);
                Route::post('/approval/submit', [HospitalDetailsController::class, 'approvalsubmit']);
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
                Route::post('/empunique', [EmployeecumPatientController::class, 'EmployeeUniquecheck']);
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
                Route::post('/station-number-unique', [FirstAidLocationController::class, 'StationNumberUniquecheck']);
                Route::post('/first-aid-box', [FirstAidLocationController::class, 'FirstAidUniquecheck']);

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

            // medical Fitness Certificate

            Route::group(['prefix' => 'ohc/medical-fitness/'], function () {
                Route::get('/list', [MedicalFitnessCertificateController::class, 'index']);
                Route::post('/list', [MedicalFitnessCertificateController::class, 'index']);
                Route::get('/add', [MedicalFitnessCertificateController::class, 'add']);
                Route::post('/add/submit', [MedicalFitnessCertificateController::class, 'store']);
                Route::get('/edit/{id}', [MedicalFitnessCertificateController::class, 'edit']);
                Route::post('/edit/submit', [MedicalFitnessCertificateController::class, 'update']);
                Route::get('/view/{id}', [MedicalFitnessCertificateController::class, 'view']);
                Route::get('/approval/view/{id}', [MedicalFitnessCertificateController::class, 'approval']);
                Route::post('/delete', [MedicalFitnessCertificateController::class, 'delete']);
                Route::get('/export/excel', [MedicalFitnessCertificateController::class, 'exportExcel']);
                Route::get('/export/pdf', [MedicalFitnessCertificateController::class, 'exportPdf']);
                Route::get('generalpdf/{id}', [MedicalFitnessCertificateController::class, 'generalpdf']);

                Route::get('/sampledownload', [MedicalFitnessCertificateController::class, 'DownloadSample']);
                Route::get('/import', [MedicalFitnessCertificateController::class, 'import']);
                Route::post('/import/submit', [MedicalFitnessCertificateController::class, 'importSubmit']);
                Route::post('/status', [MedicalFitnessCertificateController::class, 'statusChange']);
                Route::post('/unique', [MedicalFitnessCertificateController::class, 'Uniquecheck']);
                Route::post('/approvereject/submit', [MedicalFitnessCertificateController::class, 'doctorapproval']);
                Route::post('/ehsheadapprove/submit', [MedicalFitnessCertificateController::class, 'ehsheadapproval']);
            });


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
                Route::post('/pack-id', [MedicineReceivingController::class, 'packid']);
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
                Route::post('/delete/{id}', [MedicineRequisitionController::class, 'delete']);
                Route::get('/import', [MedicineRequisitionController::class, 'import']);
                Route::post('/import/submit', [MedicineRequisitionController::class, 'importSubmit']);
                Route::get('/export/excel', [MedicineRequisitionController::class, 'exportExcel']);
                Route::get('/export/pdf', [MedicineRequisitionController::class, 'exportPdf']);
                Route::post('/status', [MedicineRequisitionController::class, 'statusChange']);
                Route::get('/quantity/{quantity_id}', [MedicineRequisitionController::class, 'quantity']);
                Route::get('approval/view/{id}', [MedicineRequisitionController::class, 'approvalview']);
                Route::post('approval/submit', [MedicineRequisitionController::class, 'apporvalsubmit']);
                Route::get('generalpdf/{id}', [MedicineRequisitionController::class, 'generalpdf']);
                Route::get('stockdata', [MedicineRequisitionController::class, 'stockdata']);
                Route::get('/sample-download', [MedicineRequisitionController::class, 'DownloadSample']);
            });

            Route::group(['prefix' => 'ohc/medicine-issuance'], function () {
                Route::get('/list', [MedicineIssuanceController::class, 'index']);
                Route::post('/list', [MedicineIssuanceController::class, 'index']);
                Route::get('/add', [MedicineIssuanceController::class, 'add']);
                Route::get('/import', [MedicineIssuanceController::class, 'import']);
                Route::post('/add/submit', [MedicineIssuanceController::class, 'store']);
                Route::get('/edit/{id}', [MedicineIssuanceController::class, 'edit']);
                Route::post('/edit/submit', [MedicineIssuanceController::class, 'update']);
                Route::get('/view/{id}', [MedicineIssuanceController::class, 'view']);
                Route::post('/import/submit', [MedicineIssuanceController::class, 'importSubmit']);
                Route::get('/export/excel', [MedicineIssuanceController::class, 'exportExcel']);
                Route::get('/export/pdf', [MedicineIssuanceController::class, 'exportPdf']);
                Route::post('/status', [MedicineIssuanceController::class, 'statusChange']);
                Route::get('/quantity/{quantity_id}', [MedicineIssuanceController::class, 'quantity']);
                Route::get('/editquantity/{quantity_id}', [MedicineIssuanceController::class, 'editquantity']);
                Route::get('/sample-download', [MedicineIssuanceController::class, 'DownloadSample']);
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
                Route::post('/unique', [PrescribetoPatientController::class, 'Uniquecheck']);
                Route::get('/view/{id}', [PrescribetoPatientController::class, 'view']);
                Route::get('/view/{id}', [PrescribetoPatientController::class, 'view']);
                Route::get('/generalpdf/{id}', [PrescribetoPatientController::class, 'medicineslip']);
                Route::get('/fetchemployeename', [PrescribetoPatientController::class, 'fetchemployeename']);

                Route::get('/emp-details/{emp_id}', [PrescribetoPatientController::class, 'employeedetails']);
                Route::get('/first-aider-number', [PrescribetoPatientController::class, 'firstaidernumber']);
                Route::get('/firstaider', [PrescribetoPatientController::class, 'firstaider']);
                Route::post('/delete/{id}', [PrescribetoPatientController::class, 'delete']);
                Route::post('/cancel', [PrescribetoPatientController::class, 'cancel']);
                Route::post('/close', [PrescribetoPatientController::class, 'close']);
                Route::get('/export/excel', [PrescribetoPatientController::class, 'exportExcel']);
                Route::get('/export/pdf', [PrescribetoPatientController::class, 'exportPdf']);
                Route::post('/status', [PrescribetoPatientController::class, 'statusChange']);
                Route::get('/quantity', [PrescribetoPatientController::class, 'quantity']);
                Route::get('/employeename', [PrescribetoPatientController::class, 'employeename']);
            });

            Route::group(['prefix' => 'ohc/first-aid'], function () {
                Route::get('/list', [OpdFirstAidController::class, 'index']);
                Route::post('/list', [OpdFirstAidController::class, 'index']);
                Route::get('/add', [OpdFirstAidController::class, 'add']);
                Route::post('/add/submit', [OpdFirstAidController::class, 'store']);
                Route::get('/edit/{id}', [OpdFirstAidController::class, 'edit']);
                Route::post('/edit/submit', [OpdFirstAidController::class, 'update']);
                Route::get('/view/{id}', [OpdFirstAidController::class, 'view']);
                Route::post('/delete', [OpdFirstAidController::class, 'delete']);
                Route::get('/export/excel', [OpdFirstAidController::class, 'exportExcel']);
                Route::get('/export/pdf', [OpdFirstAidController::class, 'exportPdf']);
                Route::post('/status', [OpdFirstAidController::class, 'statusChange']);
                Route::get('/quantity', [OpdFirstAidController::class, 'quantity']);
                Route::get('/employeename', [OpdFirstAidController::class, 'employeename']);
                Route::get('/emp-details/{emp_id}', [OpdFirstAidController::class, 'employeedetails']);
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
                Route::get('/edit/{id}/{medicineId}', [DiscardController::class, 'edit']);
                Route::post('/edit/submit', [DiscardController::class, 'update']);
                Route::get('/view/{id}', [DiscardController::class, 'view']);
                Route::get('generalpdf/{id}', [DiscardController::class, 'generalpdf']);
                Route::post('/delete', [DiscardController::class, 'delete']);
                Route::get('/export/excel', [DiscardController::class, 'exportExcel']);
                Route::get('/export/pdf', [DiscardController::class, 'exportPdf']);
                Route::post('/status', [DiscardController::class, 'statusChange']);
                Route::get('/quantity/{quantity_id}', [DiscardController::class, 'quantity']);
                Route::post('/delete', [DiscardController::class, 'delete']);
                Route::get('add/{id}', [DiscardController::class, 'issue']);
                Route::post('/issue/submit', [DiscardController::class, 'issuestore']);
                Route::post('/unique', [DiscardController::class, 'Uniquecheck']);
                Route::get('/approval/view/{id}', [DiscardController::class, 'approval']);
                Route::post('/approval/submit', [DiscardController::class, 'approvalsubmit']);

                Route::get('/medicine-details/{unit_id}/{id}', [DiscardController::class, 'medicineDetails']);
            });

            // Inventory Report
            Route::group(['prefix' => 'ohc/inventory-tabular-view'], function () {
                Route::get('/list', [InventoryController::class, 'index']);
                Route::post('/list', [InventoryController::class, 'index']);
                Route::get('/userlist', [InventoryController::class, 'userindex']);
                Route::post('/userlist', [InventoryController::class, 'userindex']);
            });

            Route::group(['prefix' => 'ohc/medicine-expire-report'], function () {
                Route::get('/list', [MedicineExpireController::class, 'index']);
                Route::post('/list', [MedicineExpireController::class, 'index']);
                Route::get('/view/{id}', [MedicineExpireController::class, 'view']);
                Route::get('/export/excel', [MedicineExpireController::class, 'exportExcel']);
                Route::get('/export/pdf', [MedicineExpireController::class, 'exportPdf']);
                Route::post('/discard', [MedicineExpireController::class, 'discard']);
                Route::post('/close', [MedicineExpireController::class, 'close']);
                Route::get('/approval/view/{id}', [MedicineExpireController::class, 'approval']);
                Route::post('/approval/submit', [MedicineExpireController::class, 'approvalsubmit']);
                Route::post('/balance', [MedicineExpireController::class, 'balance']);
            });
            Route::group(['prefix' => 'ohc/monthly-inventory'], function () {
                Route::get('/list', [MonthlyInventoryController::class, 'index']);
                Route::post('/list', [MonthlyInventoryController::class, 'index']);
                Route::get('/medicinereport', [MonthlyInventoryController::class, 'medicinereport']);
                Route::get('/exportexcel', [MonthlyInventoryController::class, 'exportexcel']);
            });
            Route::group(['prefix' => 'ohc/yearly-inventory-report'], function () {
                Route::get('/list', [YearlyInventoryController::class, 'index']);
                Route::post('/list', [YearlyInventoryController::class, 'index']);
                Route::get('/medicinereport', [YearlyInventoryController::class, 'medicinereport']);
                Route::get('/exportexcel', [YearlyInventoryController::class, 'exportexcel']);
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
                Route::get('/incident-investigation/add/{incident_id}/{hiramoc}', [HiraController::class, 'addNewHiraIncident']);
                Route::get('/accident-investigation/add/{accident_id}/{hiramoc}', [HiraController::class, 'addNewHiraAccident']);
                Route::get('/fire-investigation/add/{fire_id}/{hiramoc}', [HiraController::class, 'addNewHiraFire']);
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
                Route::get('/ehsapproval/{id}', [HiraController::class, 'ehsapproval']);
                Route::post('/ehsapproval/submit', [HiraController::class, 'ehsApprovalSubmit']);
            });



            Route::group(['prefix' => 'incident/initial-incident'], function () {
                Route::get('/list/{type}/{condition}', [InitialIncidentController::class, 'index']);
                Route::post('/list/{type}/{condition}', [InitialIncidentController::class, 'index']);

                Route::get('unit', [InitialIncidentController::class, 'redirectindex']);
                Route::post('/list/all/type', [InitialIncidentController::class, 'redirectindex']);

                Route::get('/investigationList', [InitialIncidentController::class, 'investigationList']);
                Route::post('/investigationList', [InitialIncidentController::class, 'investigationList']);
                Route::get('/calist', [InitialIncidentController::class, 'calist']);
                Route::post('/calist', [InitialIncidentController::class, 'calist']);
                Route::get('/add', [InitialIncidentController::class, 'add']);
                Route::post('/add/submit', [InitialIncidentController::class, 'store']);
                Route::get('/edit/{id}', [InitialIncidentController::class, 'edit']);
                Route::post('/edit/submit', [InitialIncidentController::class, 'update']);
                Route::get('/view/{id}', [InitialIncidentController::class, 'view']);
                Route::get('/caview/{id}/{incident_id}', [InitialIncidentController::class, 'caview']);
                Route::post('/delete', [InitialIncidentController::class, 'delete']);
                Route::get('/export/excel', [InitialIncidentController::class, 'exportExcel']);
                Route::get('/export/pdf', [InitialIncidentController::class, 'exportPdf']);
                Route::get('/generalpdf/{id}', [InitialIncidentController::class, 'generalpdf']);
                Route::get('/cageneralpdf/{id}/{incident_id}', [InitialIncidentController::class, 'cageneralpdf']);
                Route::get('/sample_download', [InitialIncidentController::class, 'DownloadSample']);
                Route::get('/import', [InitialIncidentController::class, 'import']);
                Route::post('/import/Submit', [InitialIncidentController::class, 'importSubmit']);
                Route::post('/status', [InitialIncidentController::class, 'statusChange']);
                Route::post('/unique', [InitialIncidentController::class, 'Uniquecheck']);
                Route::get('/employeename', [InitialIncidentController::class, 'employeename']);
                Route::get('/fetchEmployeeDetails/{emp_id}', [InitialIncidentController::class, 'fetchEmployeeDetails']);
                Route::post('/deleteEvidence/{id}', [InitialIncidentController::class, 'deleteEvidence']);
                Route::get('/review/{id}', [InitialIncidentController::class, 'review']);
                Route::get('/ehsApproval/{id}/{incident_id}', [InitialIncidentController::class, 'ehsApproval']);
                Route::get('/caSubmission/{id}/{incident_id}', [InitialIncidentController::class, 'caSubmission']);
                Route::post('/ehs_head_review/submit', [InitialIncidentController::class, 'ehsHeadReviewSubmit']);
                Route::get('/teamMembers', [InitialIncidentController::class, 'teamMembers']);
                Route::get('/reportedBy', [InitialIncidentController::class, 'reportedBy']);
                Route::get('/investigation/{incident_id}', [InitialIncidentController::class, 'investigation']);
                Route::post('/savehira', [InitialIncidentController::class, 'saveHira']);
                Route::post('/investigation/submit', [InitialIncidentController::class, 'investigationSubmit']);
                Route::get('/gethiradetails/{hira_id}', [InitialIncidentController::class, 'gethiradetails']);
                Route::get('/existingHira/{incident_id}', [InitialIncidentController::class, 'existingHira']);
                Route::get('/existingMOC/{incident_id}', [InitialIncidentController::class, 'existingMOC']);
                Route::get('/approvereject/{incident_id}', [InitialIncidentController::class, 'approvereject']);
                Route::post('/uauc/submit', [InitialIncidentController::class, 'uaucSubmit']);
                Route::post('/riskAnalysis/submit', [InitialIncidentController::class, 'riskAnalysisSubmit']);
                Route::post('/ehs_head_verify/submit', [InitialIncidentController::class, 'ehsHeadVerifySubmit']);
                Route::post('/actiontaken/submit', [InitialIncidentController::class, 'actiontakenSubmit']);
                Route::post('/ehApproval/submit', [InitialIncidentController::class, 'ehsApprovalSubmit']);
                Route::get('/fetchEmployeeOrWorkerList/{type}', [InitialIncidentController::class, 'fetchEmployeeOrWorkerList']);
                Route::get('/getemployeename', [InitialIncidentController::class, 'employeename']);
                Route::get('/fetchPersonDetails/{id}/{type}', [InitialIncidentController::class, 'fetchPersonDetails']);
                Route::post('/investigation/getbodyEmpdetails', [InitialIncidentController::class, 'getbodyEmpdetails']);
                Route::post('/addInjury', [InitialIncidentController::class, 'addInjury']);
                Route::get('/get-employees', [InitialIncidentController::class, 'getEmployee']);
                Route::get('/get-workers', [InitialIncidentController::class, 'getWorkers']);
                Route::post('/injuryDelete/{incidentId}/{injuryId}', [InitialIncidentController::class, 'injuryDelete']);
                Route::get('/employeeid', [EmployeecumPatientController::class, 'employeeid']);
            });

            Route::group(['prefix' => 'ohc/'], function () {
                Route::group(['prefix' => 'weekly-ambulance/inspection/checklist'], function () {
                    Route::GET('/list', [WeeklyAmbulanceController::class, 'Index']);
                    Route::POST('/list', [WeeklyAmbulanceController::class, 'Index']);
                    Route::GET('/add', [WeeklyAmbulanceController::class, 'Add']);
                    Route::POST('/add/submit', [WeeklyAmbulanceController::class, 'Store']);
                    Route::POST('/unique', [WeeklyAmbulanceController::class, 'UniqueCheck']);
                    Route::GET('/view/{id}', [WeeklyAmbulanceController::class, 'View']);
                    Route::GET('/generalpdf/{id}', [WeeklyAmbulanceController::class, 'generalpdf']);
                    Route::GET('/generalExcel/{id}', [WeeklyAmbulanceController::class, 'generalExcel']);
                    Route::get('verification/{id}/{employee_type}', [WeeklyAmbulanceController::class, 'approvals']);
                    Route::post('ehsofficer/verify/submit', [WeeklyAmbulanceController::class, 'EHSOfficerSubmit']);
                    Route::post('capa/submit', [WeeklyAmbulanceController::class, 'CAPASubmit']);
                    Route::post('capa/reverify/submit', [WeeklyAmbulanceController::class, 'CAPAVerifySubmit']);
                    Route::post('level-one/verify/submit', [WeeklyAmbulanceController::class, 'levelOneManagerSubmit']);
                    Route::post('level-two/verify/submit', [WeeklyAmbulanceController::class, 'levelTwoManagerSubmit']);
                    Route::POST('/status', [WeeklyAmbulanceController::class, 'StatusChange']);
                    Route::GET('/export/excel', [WeeklyAmbulanceController::class, 'ExportExcel']);
                    Route::GET('/export/pdf', [WeeklyAmbulanceController::class, 'ExportPDF']);
                    Route::POST('/lists', [WeeklyAmbulanceController::class, 'Checklists']);
                });

                Route::group(['prefix' => 'inspection'], function () {
                    Route::GET('/list', [OccupationHealthInspectionController::class, 'Index']);
                    Route::POST('/list', [OccupationHealthInspectionController::class, 'Index']);
                    Route::GET('/add', [OccupationHealthInspectionController::class, 'Add']);
                    Route::POST('/add/submit', [OccupationHealthInspectionController::class, 'Store']);
                    Route::POST('/unique', [OccupationHealthInspectionController::class, 'UniqueCheck']);
                    Route::GET('/view/{id}', [OccupationHealthInspectionController::class, 'View']);
                    Route::GET('/generalpdf/{id}', [OccupationHealthInspectionController::class, 'generalpdf']);
                    Route::GET('/generalExcel/{id}', [OccupationHealthInspectionController::class, 'generalExcel']);
                    Route::get('verification/{id}/{employee_type}', [OccupationHealthInspectionController::class, 'approvals']);
                    Route::post('ehsofficer/verify/submit', [OccupationHealthInspectionController::class, 'EHSOfficerSubmit']);
                    Route::post('capa/submit', [OccupationHealthInspectionController::class, 'CAPASubmit']);
                    Route::post('capa/reverify/submit', [OccupationHealthInspectionController::class, 'CAPAVerifySubmit']);
                    Route::post('level-one/verify/submit', [OccupationHealthInspectionController::class, 'levelOneManagerSubmit']);
                    Route::post('level-two/verify/submit', [OccupationHealthInspectionController::class, 'levelTwoManagerSubmit']);
                    Route::POST('/status', [OccupationHealthInspectionController::class, 'StatusChange']);
                    Route::GET('/export/excel', [OccupationHealthInspectionController::class, 'ExportExcel']);
                    Route::GET('/export/pdf', [OccupationHealthInspectionController::class, 'ExportPDF']);
                    Route::POST('/lists', [OccupationHealthInspectionController::class, 'Checklists']);
                });

                Route::group(['prefix' => 'first-aid-box/monthly-audit'], function () {
                    Route::GET('/list', [MonthlyFirstAidboxController::class, 'Index']);
                    Route::POST('/list', [MonthlyFirstAidboxController::class, 'Index']);
                    Route::GET('/add', [MonthlyFirstAidboxController::class, 'Add']);
                    Route::POST('/add/submit', [MonthlyFirstAidboxController::class, 'Store']);
                    Route::POST('/unique', [MonthlyFirstAidboxController::class, 'UniqueCheck']);
                    Route::GET('/view/{id}', [MonthlyFirstAidboxController::class, 'View']);
                    Route::GET('/generalpdf/{id}', [MonthlyFirstAidboxController::class, 'generalpdf']);
                    Route::GET('/generalExcel/{id}', [MonthlyFirstAidboxController::class, 'generalExcel']);
                    Route::get('verification/{id}/{employee_type}', [MonthlyFirstAidboxController::class, 'approvals']);
                    Route::post('ehsofficer/verify/submit', [MonthlyFirstAidboxController::class, 'EHSOfficerSubmit']);
                    Route::post('capa/submit', [MonthlyFirstAidboxController::class, 'CAPASubmit']);
                    Route::post('capa/reverify/submit', [MonthlyFirstAidboxController::class, 'CAPAVerifySubmit']);
                    Route::post('level-one/verify/submit', [MonthlyFirstAidboxController::class, 'levelOneManagerSubmit']);
                    Route::post('level-two/verify/submit', [MonthlyFirstAidboxController::class, 'levelTwoManagerSubmit']);
                    Route::POST('/status', [MonthlyFirstAidboxController::class, 'StatusChange']);
                    Route::GET('/export/excel', [MonthlyFirstAidboxController::class, 'ExportExcel']);
                    Route::GET('/export/pdf', [MonthlyFirstAidboxController::class, 'ExportPDF']);
                    Route::POST('/lists', [MonthlyFirstAidboxController::class, 'Checklists']);
                });

                Route::group(['prefix' => 'medical-requisition-slip'], function () {
                    Route::GET('/list', [MedicalRequisitionSlipController::class, 'Index']);
                    Route::POST('/list', [MedicalRequisitionSlipController::class, 'Index']);
                    Route::GET('/add', [MedicalRequisitionSlipController::class, 'Add']);
                    Route::GET('/freeze-medicine-quantity', [MedicalRequisitionSlipController::class, 'freezeQuantity']);
                    Route::POST('/add/submit', [MedicalRequisitionSlipController::class, 'Store']);
                    Route::POST('/unique', [MedicalRequisitionSlipController::class, 'UniqueCheck']);
                    Route::GET('/view/{id}', [MedicalRequisitionSlipController::class, 'View']);
                    Route::GET('/generalpdf/{id}', [MedicalRequisitionSlipController::class, 'generalpdf']);
                    Route::GET('/generalExcel/{id}', [MedicalRequisitionSlipController::class, 'generalExcel']);
                    Route::GET('/approval/view/{id}', [MedicalRequisitionSlipController::class, 'approval']);
                    Route::POST('/status', [MedicalRequisitionSlipController::class, 'StatusChange']);
                    Route::GET('/export/excel', [MedicalRequisitionSlipController::class, 'ExportExcel']);
                    Route::GET('/export/pdf', [MedicalRequisitionSlipController::class, 'ExportPDF']);
                    Route::POST('/lists', [MedicalRequisitionSlipController::class, 'Checklists']);
                    Route::POST('/floormanagerapproval/submit', [MedicalRequisitionSlipController::class, 'floormanagerapproval']);
                    Route::POST('/safetyofficerapproval/submit', [MedicalRequisitionSlipController::class, 'safetyofficerapproval']);
                });

                Route::group(['prefix' => 'medical-requisition-slip/fdo-security-gate'], function () {
                    Route::GET('/list', [MedicalRequisitionSlipSecurityGateController::class, 'Index']);
                    Route::POST('/list', [MedicalRequisitionSlipSecurityGateController::class, 'Index']);
                    Route::GET('/add', [MedicalRequisitionSlipSecurityGateController::class, 'Add']);
                    Route::POST('/add/submit', [MedicalRequisitionSlipSecurityGateController::class, 'Store']);
                    Route::POST('/unique', [MedicalRequisitionSlipSecurityGateController::class, 'UniqueCheck']);
                    Route::GET('/generalpdf/{id}', [MedicalRequisitionSlipSecurityGateController::class, 'generalpdf']);
                    Route::GET('/generalExcel/{id}', [MedicalRequisitionSlipSecurityGateController::class, 'generalExcel']);
                    Route::GET('/approval/view/{id}', [MedicalRequisitionSlipSecurityGateController::class, 'approval']);
                    Route::GET('/view/{id}', [MedicalRequisitionSlipSecurityGateController::class, 'View']);
                    Route::POST('/status', [MedicalRequisitionSlipSecurityGateController::class, 'StatusChange']);
                    Route::GET('/export/excel', [MedicalRequisitionSlipSecurityGateController::class, 'ExportExcel']);
                    Route::GET('/export/pdf', [MedicalRequisitionSlipSecurityGateController::class, 'ExportPDF']);
                    Route::POST('/safetyofficerapproval/submit', [MedicalRequisitionSlipSecurityGateController::class, 'safetyofficerapproval']);
                });

                Route::group(['prefix' => 'first-aid-box/daily-departmental'], function () {
                    Route::GET('/list', [DailyDepartmentFirstAidBoxController::class, 'Index']);
                    Route::POST('/list', [DailyDepartmentFirstAidBoxController::class, 'Index']);
                    Route::GET('/add', [DailyDepartmentFirstAidBoxController::class, 'Add']);
                    Route::GET('/generalpdf/{id}', [DailyDepartmentFirstAidBoxController::class, 'generalpdf']);
                    Route::GET('/generalExcel/{id}', [DailyDepartmentFirstAidBoxController::class, 'generalExcel']);
                    Route::GET('/approval/view/{id}', [DailyDepartmentFirstAidBoxController::class, 'approval']);
                    Route::POST('/add/submit', [DailyDepartmentFirstAidBoxController::class, 'Store']);
                    Route::POST('/unique', [DailyDepartmentFirstAidBoxController::class, 'UniqueCheck']);
                    Route::GET('/view/{id}', [DailyDepartmentFirstAidBoxController::class, 'View']);
                    Route::POST('/status', [DailyDepartmentFirstAidBoxController::class, 'StatusChange']);
                    Route::GET('/export/excel', [DailyDepartmentFirstAidBoxController::class, 'ExportExcel']);
                    Route::GET('/export/pdf', [DailyDepartmentFirstAidBoxController::class, 'ExportPDF']);

                    Route::POST('/floormanagerapproval/submit', [DailyDepartmentFirstAidBoxController::class, 'floormanagerapproval']);
                    Route::POST('/lists', [DailyDepartmentFirstAidBoxController::class, 'Checklists']);
                });

                Route::group(['prefix' => 'first-aider'], function () {
                    Route::GET('/list', [FirstAiderlistController::class, 'Index']);
                    Route::POST('/list', [FirstAiderlistController::class, 'Index']);
                    Route::GET('/add', [FirstAiderlistController::class, 'Add']);
                    Route::POST('/add/submit', [FirstAiderlistController::class, 'Store']);
                    Route::POST('/unique', [FirstAiderlistController::class, 'UniqueCheck']);
                    Route::GET('/view/{id}', [FirstAiderlistController::class, 'View']);
                    Route::GET('/generalpdf/{id}', [FirstAiderlistController::class, 'generalpdf']);
                    Route::GET('/generalExcel/{id}', [FirstAiderlistController::class, 'generalExcel']);
                    Route::POST('/status', [FirstAiderlistController::class, 'StatusChange']);
                    Route::GET('/export/excel', [FirstAiderlistController::class, 'ExportExcel']);
                    Route::GET('/export/pdf', [FirstAiderlistController::class, 'ExportPDF']);
                    Route::POST('/lists', [FirstAiderlistController::class, 'Checklists']);
                    Route::GET('/employeename', [FirstAiderlistController::class, 'employeename']);
                    Route::GET('/employeedetails ', [FirstAiderlistController::class, 'employeedetails']);
                });
            });

            Route::group(['prefix' => 'kpi/'], function () {
                Route::group(['prefix' => 'master/leading-lagging'], function () {
                    Route::GET('/list', [LeadingLaggingController::class, 'Index']);
                    Route::POST('/list', [LeadingLaggingController::class, 'Index']);
                    Route::GET('/add', [LeadingLaggingController::class, 'Add']);
                    Route::POST('/add/submit', [LeadingLaggingController::class, 'Store']);
                    Route::POST('/unique', [LeadingLaggingController::class, 'UniqueCheck']);
                    Route::GET('/edit/{id}', [LeadingLaggingController::class, 'Edit']);
                    Route::POST('/edit/submit', [LeadingLaggingController::class, 'Update']);
                    Route::GET('/view/{id}', [LeadingLaggingController::class, 'View']);
                    Route::POST('/delete', [LeadingLaggingController::class, 'Delete']);
                    Route::POST('/status', [LeadingLaggingController::class, 'StatusChange']);
                    Route::GET('/export/excel', [LeadingLaggingController::class, 'ExportExcel']);
                    Route::GET('/export/pdf', [LeadingLaggingController::class, 'ExportPDF']);
                    Route::GET('/import', [LeadingLaggingController::class, 'Import']);
                    Route::POST('/import/Submit', [LeadingLaggingController::class, 'ImportSubmit']);
                    Route::GET('/sample_download', [LeadingLaggingController::class, 'DownloadSample']);
                    Route::POST('/lists', [LeadingLaggingController::class, 'Checklists']);
                });

                Route::group(['prefix' => 'ehs-inputs'], function () {
                    Route::GET('/list', [HSCInputsController::class, 'Index']);
                    Route::POST('/list', [HSCInputsController::class, 'Index']);
                    Route::GET('/add', [HSCInputsController::class, 'Add']);
                    Route::POST('/add/submit', [HSCInputsController::class, 'Store']);
                    Route::POST('/unique', [HSCInputsController::class, 'UniqueCheck']);
                    Route::GET('/edit/{id}', [HSCInputsController::class, 'Edit']);
                    Route::POST('/edit/submit', [HSCInputsController::class, 'Update']);
                    Route::GET('/view/{id}', [HSCInputsController::class, 'View']);
                    Route::POST('/delete', [HSCInputsController::class, 'Delete']);
                    Route::POST('/status', [HSCInputsController::class, 'StatusChange']);
                    Route::GET('/export/excel', [HSCInputsController::class, 'ExportExcel']);
                    Route::GET('/export/pdf', [HSCInputsController::class, 'ExportPDF']);
                    Route::GET('/import', [HSCInputsController::class, 'Import']);
                    Route::POST('/import/Submit', [HSCInputsController::class, 'ImportSubmit']);
                    Route::GET('/sample_download', [HSCInputsController::class, 'DownloadSample']);
                    Route::POST('/lists', [HSCInputsController::class, 'Checklists']);
                });
            });
        });
    });
});
