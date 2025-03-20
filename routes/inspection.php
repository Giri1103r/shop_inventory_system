<?php

use App\Http\Controllers\Inspection\GembaWalk\GembaWalkController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inspection\MSDSController;
use App\Http\Controllers\Inspection\RRAAController;
use App\Http\Controllers\Inspection\Master\ChecklistTypeController;
use App\Http\Controllers\Inspection\Audit\AuditAssessmentController;
use App\Http\Controllers\Inspection\Audit\AuditAnalysisController;
use App\Http\Controllers\Inspection\Master\ChecklistSubTypeController;
use App\Http\Controllers\Inspection\Safety\Master\EquipmentController;
use App\Http\Controllers\Inspection\Master\ChecklistSubTypeDataController;
use App\Http\Controllers\Inspection\Safety\SafetyGalleryInsepctionController;
use App\Http\Controllers\Inspection\Safety\MonthlyEyeWashInspectionController;

use App\Http\Controllers\Inspection\Safety\MonthlyForkLiftInspectionController;
use App\Http\Controllers\Inspection\Environment\AmbientNoiseMonitoringController;
use App\Http\Controllers\Inspection\Safety\EquipmentController as SafetyEquipmentController;
use App\Http\Controllers\OhcManagement\SafetyPettyLogbook\SafetyPettyController;

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
        Route::get('list', [MonthlyEyeWashInspectionController::class, 'index']);
        Route::post('list', [MonthlyEyeWashInspectionController::class, 'index']);
        Route::get('add', [MonthlyEyeWashInspectionController::class, 'add']);
        Route::post('add/submit', [MonthlyEyeWashInspectionController::class, 'store']);
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
        Route::get('list', [SafetyGalleryInsepctionController::class, 'index']);
        Route::post('list', [SafetyGalleryInsepctionController::class, 'index']);
        Route::get('add', [SafetyGalleryInsepctionController::class, 'add']);
        Route::post('add/submit', [SafetyGalleryInsepctionController::class, 'store']);
        Route::get('view/{id}', [SafetyGalleryInsepctionController::class, 'view']);
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
    Route::get('verification/{id}/{employee_type}', [SafetyPettyController::class, 'approvals']);
    Route::post('ehsofficer/verify/submit', [SafetyPettyController::class, 'EHSOfficerSubmit']);
    Route::post('capa/submit', [SafetyPettyController::class, 'CAPASubmit']);
    Route::post('capa/reverify/submit', [SafetyPettyController::class, 'CAPAVerifySubmit']);
    Route::post('level-one/verify/submit', [SafetyPettyController::class, 'levelOneManagerSubmit']);
    Route::post('level-two/verify/submit', [SafetyPettyController::class, 'levelTwoManagerSubmit']);
    Route::get('generalpdf/{id}', [SafetyPettyController::class, 'generalpdf']);

});
