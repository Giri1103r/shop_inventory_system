<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inspection\Master\ChecklistTypeController;
use App\Http\Controllers\Inspection\Master\ChecklistSubTypeController;
use App\Http\Controllers\Inspection\Master\ChecklistSubTypeDataController;
use App\Http\Controllers\Inspection\Ohc\MedicalRequisitionSlipController;
use App\Http\Controllers\Inspection\Ohc\MedicalRequisitionSlipSecurityGateController;
use App\Http\Controllers\Inspection\Ohc\WeaklyAmbulanceController;

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
        Route::POST('/import/submit', [ChecklistTypeController::class, 'ImportSubmit']);
        Route::GET('/sampledownload', [ChecklistTypeController::class, 'DownloadSample']);
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


Route::group(['prefix' => 'ohc/'], function () {


    Route::group(['prefix' => 'weekly-ambulance/inspection/checklist'], function () {
        Route::GET('/list', [WeaklyAmbulanceController::class, 'Index']);
        Route::POST('/list', [WeaklyAmbulanceController::class, 'Index']);
        Route::GET('/add', [WeaklyAmbulanceController::class, 'Add']);
        Route::POST('/add/submit', [WeaklyAmbulanceController::class, 'Store']);
        Route::POST('/unique', [WeaklyAmbulanceController::class, 'UniqueCheck']);
        Route::GET('/view/{id}', [WeaklyAmbulanceController::class, 'View']);
        Route::POST('/status', [WeaklyAmbulanceController::class, 'StatusChange']);
        Route::GET('/export/excel', [WeaklyAmbulanceController::class, 'ExportExcel']);
        Route::GET('/export/pdf', [WeaklyAmbulanceController::class, 'ExportPDF']);
        Route::POST('/lists', [WeaklyAmbulanceController::class, 'Checklists']);
    });

    Route::group(['prefix' => 'medical-requisition-slip/fdo-security-gate/'], function () {
        Route::GET('/list', [MedicalRequisitionSlipController::class, 'Index']);
        Route::POST('/list', [MedicalRequisitionSlipController::class, 'Index']);
        Route::GET('/add', [MedicalRequisitionSlipController::class, 'Add']);
        Route::POST('/add/submit', [MedicalRequisitionSlipController::class, 'Store']);
        Route::POST('/unique', [MedicalRequisitionSlipController::class, 'UniqueCheck']);
        Route::GET('/view/{id}', [MedicalRequisitionSlipController::class, 'View']);
        Route::POST('/status', [MedicalRequisitionSlipController::class, 'StatusChange']);
        Route::GET('/export/excel', [MedicalRequisitionSlipController::class, 'ExportExcel']);
        Route::GET('/export/pdf', [MedicalRequisitionSlipController::class, 'ExportPDF']);
        Route::POST('/lists', [MedicalRequisitionSlipController::class, 'Checklists']);
    });

    Route::group(['prefix' => 'medical-requisition-slip'], function () {
        Route::GET('/list', [MedicalRequisitionSlipController::class, 'Index']);
        Route::POST('/list', [MedicalRequisitionSlipController::class, 'Index']);
        Route::GET('/add', [MedicalRequisitionSlipController::class, 'Add']);
        Route::POST('/add/submit', [MedicalRequisitionSlipController::class, 'Store']);
        Route::POST('/unique', [MedicalRequisitionSlipController::class, 'UniqueCheck']);
        Route::GET('/view/{id}', [MedicalRequisitionSlipController::class, 'View']);
        Route::POST('/status', [MedicalRequisitionSlipController::class, 'StatusChange']);
        Route::GET('/export/excel', [MedicalRequisitionSlipController::class, 'ExportExcel']);
        Route::GET('/export/pdf', [MedicalRequisitionSlipController::class, 'ExportPDF']);
        Route::POST('/lists', [MedicalRequisitionSlipController::class, 'Checklists']);
    });

    Route::group(['prefix' => 'medical-requisition-slip/fdo-security-gate'], function () {
        Route::GET('/list', [MedicalRequisitionSlipSecurityGateController::class, 'Index']);
        Route::POST('/list', [MedicalRequisitionSlipSecurityGateController::class, 'Index']);
        Route::GET('/add', [MedicalRequisitionSlipSecurityGateController::class, 'Add']);
        Route::POST('/add/submit', [MedicalRequisitionSlipSecurityGateController::class, 'Store']);
        Route::POST('/unique', [MedicalRequisitionSlipSecurityGateController::class, 'UniqueCheck']);
        Route::GET('/view/{id}', [MedicalRequisitionSlipSecurityGateController::class, 'View']);
        Route::POST('/status', [MedicalRequisitionSlipSecurityGateController::class, 'StatusChange']);
        Route::GET('/export/excel', [MedicalRequisitionSlipSecurityGateController::class, 'ExportExcel']);
        Route::GET('/export/pdf', [MedicalRequisitionSlipSecurityGateController::class, 'ExportPDF']);
        Route::POST('/lists', [MedicalRequisitionSlipSecurityGateController::class, 'Checklists']);
    });
});
