<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inspection\MSDS\MSDSController;
use App\Http\Controllers\Inspection\RRAA\RRAAController;
use App\Http\Controllers\Inspection\Ohc\SafetyPettyController;
use App\Http\Controllers\Inspection\Ohc\FloorStretcherController;
use App\Http\Controllers\Inspection\Audit\AuditAnalysisController;
use App\Http\Controllers\Inspection\GembaWalk\GembaWalkController;
use App\Http\Controllers\Inspection\Master\ChecklistTypeController;
use App\Http\Controllers\Inspection\Audit\AuditAssessmentController;
use App\Http\Controllers\Inspection\Fire\HooterInspectionController;
use App\Http\Controllers\Inspection\Safety\Master\EquipmentController;
use App\Http\Controllers\Inspection\Ohc\MonthlyMedicineStoreController;
use App\Http\Controllers\Inspection\Fire\MonthlyFirePumpHouseController;
use App\Http\Controllers\Inspection\Safety\ForkLiftInspectionController;
use App\Http\Controllers\Inspection\Safety\FireSafetyEquipmentController;
use App\Http\Controllers\Inspection\Master\ChecklistSubTypeDataController;
use App\Http\Controllers\Inspection\Safety\OHSPlantSummaryReportController;
use App\Http\Controllers\Inspection\Safety\SafetyWalkObservationController;
use App\Http\Controllers\Inspection\Safety\SafetyGalleryInsepctionController;
use App\Http\Controllers\Inspection\Environment\WorkNoiseMonitoringController;
use App\Http\Controllers\Inspection\Safety\MonthlyEyeWashInspectionController;
use App\Http\Controllers\Inspection\Safety\MonthlyForkLiftInspectionController;
use App\Http\Controllers\Inspection\Environment\AmbientNoiseMonitoringController;
use App\Http\Controllers\Inspection\Master\ChecklistSubTypeController;
use App\Http\Controllers\Inspection\Ohc\FirstAidRecordController;
use App\Http\Controllers\Inspection\Ohc\HealthInstrumentCalibrationController;
use App\Http\Controllers\Inspection\Safety\EquipmentController as SafetyEquipmentController;

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
    Route::get('export/pdf', [GembaWalkController::class, 'exportPdf']);
    Route::get('export/excel', [GembaWalkController::class, 'exportExcel']);
});

Route::group(['prefix' => 'environment/'], function () {
    Route::group(['prefix' => 'ambient-noise/'], function () {
        Route::get('list', [AmbientNoiseMonitoringController::class, 'index']);
        Route::post('list', [AmbientNoiseMonitoringController::class, 'index']);
        Route::get('add', [AmbientNoiseMonitoringController::class, 'add']);
        Route::post('add/submit', [AmbientNoiseMonitoringController::class, 'store']);
        Route::get('edit/{id}', [AmbientNoiseMonitoringController::class, 'edit']);
        Route::post('edit/submit', [AmbientNoiseMonitoringController::class, 'update']);
        Route::get('view/{id}', [AmbientNoiseMonitoringController::class, 'view']);
        Route::post('delete', [AmbientNoiseMonitoringController::class, 'delete']);
        Route::get('export/excel', [AmbientNoiseMonitoringController::class, 'exportExcel']);
        Route::get('export/pdf', [AmbientNoiseMonitoringController::class, 'exportPdf']);
        Route::get('sample_download', [AmbientNoiseMonitoringController::class, 'DownloadSample']);
        Route::get('import', [AmbientNoiseMonitoringController::class, 'import']);
        Route::post('import/Submit', [AmbientNoiseMonitoringController::class, 'importSubmit']);
        Route::post('status', [AmbientNoiseMonitoringController::class, 'statusChange']);
        Route::post('unique', [AmbientNoiseMonitoringController::class, 'Uniquecheck']);
        Route::get('employeeName', [AmbientNoiseMonitoringController::class, 'employeename']);
    });

    Route::group(['prefix' => 'work-noise/'], function () {
        Route::get('list', [WorkNoiseMonitoringController::class, 'index']);
        Route::post('list', [WorkNoiseMonitoringController::class, 'index']);
        Route::get('add', [WorkNoiseMonitoringController::class, 'add']);
        Route::post('add/submit', [WorkNoiseMonitoringController::class, 'store']);
        Route::get('edit/{id}', [WorkNoiseMonitoringController::class, 'edit']);
        Route::post('edit/submit', [WorkNoiseMonitoringController::class, 'update']);
        Route::get('view/{id}', [WorkNoiseMonitoringController::class, 'view']);
        Route::post('delete', [WorkNoiseMonitoringController::class, 'delete']);
        Route::get('export/excel', [WorkNoiseMonitoringController::class, 'exportExcel']);
        Route::get('export/pdf', [WorkNoiseMonitoringController::class, 'exportPdf']);
        Route::get('sample_download', [WorkNoiseMonitoringController::class, 'DownloadSample']);
        Route::get('import', [WorkNoiseMonitoringController::class, 'import']);
        Route::post('import/Submit', [WorkNoiseMonitoringController::class, 'importSubmit']);
        Route::post('status', [WorkNoiseMonitoringController::class, 'statusChange']);
        Route::post('unique', [WorkNoiseMonitoringController::class, 'Uniquecheck']);
        Route::get('employeeName', [WorkNoiseMonitoringController::class, 'employeename']);
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
        Route::POST('/lists', [EquipmentController::class, 'Checklists']);
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
    Route::get('verification/{id}/{employee_type}', [MSDSController::class, 'approvals']);
    Route::post('ehsofficer/verify/submit', [MSDSController::class, 'EHSOfficerSubmit']);
    Route::post('capa/submit', [MSDSController::class, 'CAPASubmit']);
    Route::post('capa/reverify/submit', [MSDSController::class, 'CAPAVerifySubmit']);
    Route::post('level-one/verify/submit', [MSDSController::class, 'levelOneManagerSubmit']);
    Route::post('level-two/verify/submit', [MSDSController::class, 'levelTwoManagerSubmit']);
    Route::get('generalpdf/{id}', [MSDSController::class, 'generalpdf']);
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
    Route::get('verification/{id}/{employee_type}', [RRAAController::class, 'approvals']);
    Route::post('ehsofficer/verify/submit', [RRAAController::class, 'EHSOfficerSubmit']);
    Route::post('capa/submit', [RRAAController::class, 'CAPASubmit']);
    Route::post('capa/reverify/submit', [RRAAController::class, 'CAPAVerifySubmit']);
    Route::post('level-one/verify/submit', [RRAAController::class, 'levelOneManagerSubmit']);
    Route::post('level-two/verify/submit', [RRAAController::class, 'levelTwoManagerSubmit']);
    Route::get('generalpdf/{id}', [RRAAController::class, 'generalpdf']);
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

});
Route::group(['prefix' => 'ohc/monthly-medicine-store/inspection/'], function () {
    Route::GET('list', [MonthlyMedicineStoreController::class, 'Index']);
    Route::POST('list', [MonthlyMedicineStoreController::class, 'Index']);
    Route::GET('add', [MonthlyMedicineStoreController::class, 'Add']);
    Route::POST('add/submit', [MonthlyMedicineStoreController::class, 'Store']);
    Route::GET('view/{id}', [MonthlyMedicineStoreController::class, 'View']);
    Route::GET('export/excel', [MonthlyMedicineStoreController::class, 'ExportExcel']);
    Route::GET('export/pdf', [MonthlyMedicineStoreController::class, 'ExportPdf']);
    Route::GET('exportViewpdf/{id}', [MonthlyMedicineStoreController::class, 'ExportViewPDF']);
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
