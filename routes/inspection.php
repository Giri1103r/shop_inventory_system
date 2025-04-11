<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inspection\MSDS\MSDSController;
use App\Http\Controllers\Inspection\RRAA\RRAAController;
use App\Models\Inspection\Fire\MonthlyPhysicalInspection;
use App\Http\Controllers\Inspection\Fire\HoseBoxController;
use App\Http\Controllers\Inspection\Fire\FireAlarmController;
use App\Http\Controllers\Inspection\Ohc\SafetyPettyController;
use App\Http\Controllers\Inspection\Fire\HoseReelHoseController;
use App\Http\Controllers\Inspection\Ohc\FirstAidRecordController;
use App\Http\Controllers\Inspection\Ohc\FloorStretcherController;
use App\Http\Controllers\Inspection\Audit\AuditAnalysisController;
use App\Http\Controllers\Inspection\Fire\IsolationValveController;
use App\Http\Controllers\Inspection\GembaWalk\GembaWalkController;
use App\Http\Controllers\Inspection\Ohc\Master\FirstAidController;
use App\Http\Controllers\Inspection\Fire\SprinklarSystemController;
use App\Http\Controllers\Inspection\Master\ChecklistTypeController;
use App\Http\Controllers\Inspection\Audit\AuditAssessmentController;
use App\Http\Controllers\Inspection\Fire\FireExtinguisherController;
use App\Http\Controllers\Inspection\Fire\HooterInspectionController;
use App\Http\Controllers\Inspection\Ohc\WeeklyFirstAidBoxController;
use App\Http\Controllers\Inspection\Audit\MonthlyAuditPlanController;
use App\Http\Controllers\Inspection\Audit\Master\TaskMasterController;
use App\Http\Controllers\Inspection\Fire\DetectorInspectionController;
use App\Http\Controllers\Inspection\Fire\PASystemInspectionController;
use App\Http\Controllers\Inspection\Master\ChecklistSubTypeController;
use App\Http\Controllers\Inspection\Ohc\DailyVitalEquipmentController;
use App\Http\Controllers\Inspection\Safety\Master\EquipmentController;
use App\Http\Controllers\Inspection\ohc\FirstAidBagChecklistController;
use App\Http\Controllers\Inspection\Ohc\MonthlyMedicineStoreController;
use App\Http\Controllers\Inspection\Environment\LuxMonitoringController;
use App\Http\Controllers\Inspection\Fire\CertifiedFireFighterController;
use App\Http\Controllers\Inspection\Fire\FireSafetyEquipmentsController;
use App\Http\Controllers\Inspection\Fire\MonthlyFirePumpHouseController;
use App\Http\Controllers\Inspection\Fire\SandBucketInspectionController;
use App\Http\Controllers\Inspection\Safety\ForkLiftInspectionController;
use App\Http\Controllers\Inspection\Fire\FireModularInspectionController;
use App\Http\Controllers\Inspection\Fire\HydrantRiserInspectionContoller;
use App\Http\Controllers\Inspection\Safety\FireSafetyEquipmentController;
use App\Http\Controllers\Inspection\Fire\CoTypeFireExtinguisherController;
use App\Http\Controllers\Inspection\Master\ChecklistSubTypeDataController;
use App\Http\Controllers\Inspection\Fire\FireMockDrillInspectionController;
use App\Http\Controllers\Inspection\Ohc\CurrentNewExtCodeDialingController;
use App\Http\Controllers\Inspection\Safety\OHSPlantSummaryReportController;
use App\Http\Controllers\Inspection\Safety\SafetyWalkObservationController;
use App\Http\Controllers\Inspection\Fire\EmergencyLightInspectionController;
use App\Http\Controllers\Inspection\Fire\MonthlyPhysicalInspectionController;
use App\Http\Controllers\Inspection\Ohc\FirstAidMedicineInspectionController;
use App\Http\Controllers\Inspection\Safety\SafetyGalleryInsepctionController;
use App\Http\Controllers\Inspection\Environment\WorkNoiseMonitoringController;
use App\Http\Controllers\Inspection\Ohc\HealthInstrumentCalibrationController;
use App\Http\Controllers\Inspection\ohc\OHCHygieneCleaningChecklistController;
use App\Http\Controllers\Inspection\Safety\MonthlyEyeWashInspectionController;
use App\Http\Controllers\Inspection\Safety\MonthlyForkLiftInspectionController;
use App\Http\Controllers\Inspection\Environment\WorkZoneAirMonitoringController;
use App\Http\Controllers\Inspection\Environment\AmbientNoiseMonitoringController;
use App\Http\Controllers\Inspection\Fire\CartridgeTypeFireExtinguisherController;
use App\Http\Controllers\Inspection\Environment\AmbientAirMonitoringYearlyController;
use App\Http\Controllers\Inspection\Ohc\EmergencyBuyerFirstAidBagChecklistController;
use App\Http\Controllers\Inspection\Environment\DgSetStackEmissionMonitoringController;
use App\Http\Controllers\Inspection\Safety\EquipmentController as SafetyEquipmentController;
use App\Http\Controllers\Inspection\Fire\FirePumpHouseController;
use App\Http\Controllers\Inspection\Fire\FirePreNocController;
use App\Http\Controllers\Inspection\Audit\InterUnitAuditController;
use App\Http\Controllers\Inspection\Fire\ChecklistObservationFollowupController;

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
        Route::post('unique', [AuditAnalysisController::class, 'Uniquecheck']);
        Route::get('employeeName', [AuditAnalysisController::class, 'employeename']);
        Route::get('ajax-list', [AuditAnalysisController::class, 'Uniquecheck']);
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
    });
});



Route::group(['prefix' => 'inspection/gemba-walk/'], function () {
    Route::get('list', [GembaWalkController::class, 'index']);
    Route::post('list', [GembaWalkController::class, 'index']);
    Route::get('add', [GembaWalkController::class, 'add']);
    Route::post('add/submit', [GembaWalkController::class, 'store']);
    Route::get('view/{id}', [GembaWalkController::class, 'view']);
    Route::get('capa-verification/{id}', [GembaWalkController::class, 'approvals']);
    Route::post('capa/submit', [GembaWalkController::class, 'CAPASubmit']);
    Route::get('floor-manager/{id}', [GembaWalkController::class, 'review']);
    Route::post('floor-manager/review/submit', [GembaWalkController::class, 'capaReviewSubmit']);
    Route::get('ehs-officer/{id}', [GembaWalkController::class, 'ehsOfficerReview']);
    Route::post('ehs-officer/review/submit', [GembaWalkController::class, 'ehsReviewSubmit']);
    Route::get('generalpdf/{id}', [GembaWalkController::class, 'generalpdf']);
    Route::get('generalExcel/{id}', [GembaWalkController::class, 'generalExcel']);
    Route::get('export/pdf', [GembaWalkController::class, 'exportPdf']);
    Route::get('export/excel', [GembaWalkController::class, 'exportExcel']);
});

Route::group(['prefix' => 'environment/'], function () {
    Route::group(['prefix' => 'ambient-noise/'], function () {
        Route::get('list', [AmbientNoiseMonitoringController::class, 'index']);
        Route::post('list', [AmbientNoiseMonitoringController::class, 'index']);
        Route::get('add', [AmbientNoiseMonitoringController::class, 'add']);
        Route::post('add/submit', [AmbientNoiseMonitoringController::class, 'store']);
        Route::get('view/{id}', [AmbientNoiseMonitoringController::class, 'view']);
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
    });
});


Route::group(['prefix' => 'msds/'], function () {
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
    Route::GET('/generalExcel/{id}', [MSDSController::class, 'generalExcel']);
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
    Route::GET('/generalExcel/{id}', [RRAAController::class, 'generalExcel']);

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
        Route::GET('exportViewExcel/{id}', [EmergencyLightInspectionController::class, 'ExportExcel']);
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
    });

    Route::group(['prefix' => 'pre-noc/checklist/'], function () {
        Route::get('list', [FirePreNocController::class, 'index']);
        Route::post('list', [FirePreNocController::class, 'index']);
        Route::get('add', [FirePreNocController::class, 'add']);
        Route::post('add/submit', [FirePreNocController::class, 'store']);
        Route::get('edit/{id}', [FirePreNocController::class, 'edit']);
        Route::post('edit/submit', [FirePreNocController::class, 'update']);
        Route::get('view/{id}', [FirePreNocController::class, 'view']);
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
});

Route::group(['prefix' => 'ohc/health-instrument/calibration-track-sheet/'], function () {
    Route::get('list', [HealthInstrumentCalibrationController::class, 'index']);
    Route::post('list', [HealthInstrumentCalibrationController::class, 'index']);
    Route::get('add', [HealthInstrumentCalibrationController::class, 'add']);
    Route::post('add/submit', [HealthInstrumentCalibrationController::class, 'store']);
    Route::get('view/{id}', [HealthInstrumentCalibrationController::class, 'view']);
    Route::get('generalpdf/{id}', [HealthInstrumentCalibrationController::class, 'generalpdf']);
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
