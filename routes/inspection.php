<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inspection\Master\ChecklistTypeController;
use App\Http\Controllers\Inspection\Master\ChecklistSubTypeController;
use App\Http\Controllers\Inspection\Master\ChecklistSubTypeDataController;
use App\Http\Controllers\Inspection\Audit\AuditAssessmentController;
use App\Http\Controllers\Inspection\Ohc\MedicalRequisitionSlipController;
use App\Http\Controllers\Inspection\Ohc\MedicalRequisitionSlipSecurityGateController;
use App\Http\Controllers\Inspection\Ohc\WeaklyAmbulanceController;
use App\Http\Controllers\Inspection\Ohc\WeeklyAmbulanceController;

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
    Route::GET('/list', [ChecklistSubTypeController::class, 'index']);
    Route::POST('/list', [ChecklistSubTypeController::class, 'index']);
    Route::GET('/add', [ChecklistSubTypeController::class, 'add']);
    Route::POST('/add/submit', [ChecklistSubTypeController::class, 'store']);
    Route::POST('/unique', [ChecklistSubTypeController::class, 'uniqueCheck']);
    Route::GET('/edit/{id}', [ChecklistSubTypeController::class, 'edit']);
    Route::POST('/edit/submit', [ChecklistSubTypeController::class, 'update']);
    Route::GET('/view/{id}', [ChecklistSubTypeController::class, 'view']);
    Route::POST('/delete', [ChecklistSubTypeController::class, 'delete']);
    Route::POST('/status', [ChecklistSubTypeController::class, 'statusChange']);
    Route::GET('/export/excel', [ChecklistSubTypeController::class, 'exportExcel']);
    Route::GET('/export/pdf', [ChecklistSubTypeController::class, 'exportPDF']);
    Route::GET('/import', [ChecklistSubTypeController::class, 'import']);
    Route::POST('/import/submit', [ChecklistSubTypeController::class, 'importSubmit']);
    Route::GET('/sampledownload', [ChecklistSubTypeController::class, 'downloadSample']);
    Route::POST('/lists', [ChecklistSubTypeController::class, 'checklists']);
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
});
