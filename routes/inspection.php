<?php

use App\Http\Controllers\Inspection\GembaWalkController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inspection\Master\ChecklistTypeController;
use App\Http\Controllers\Inspection\Master\ChecklistSubTypeDataController;


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



Route::group(['prefix' => 'inspection/gemba-walk/'], function () {
    Route::get('list', [GembaWalkController::class, 'index']);
    Route::post('list', [GembaWalkController::class, 'index']);
    Route::get('add', [GembaWalkController::class, 'add']);
    Route::post('add/submit', [GembaWalkController::class, 'store']);
    // Route::get('edit/{id}', [ChecklistSubTypeDataController::class, 'edit']);
    // Route::post('edit/submit', [ChecklistSubTypeDataController::class, 'update']);
    Route::get('view/{id}', [GembaWalkController::class, 'view']);
    // Route::post('delete', [ChecklistSubTypeDataController::class, 'delete']);
    // Route::get('export/excel', [ChecklistSubTypeDataController::class, 'exportExcel']);
    // Route::get('export/pdf', [ChecklistSubTypeDataController::class, 'exportPdf']);
    // Route::get('sample_download', [ChecklistSubTypeDataController::class, 'DownloadSample']);
    // Route::get('import', [ChecklistSubTypeDataController::class, 'import']);
    // Route::post('import/Submit', [ChecklistSubTypeDataController::class, 'importSubmit']);
    // Route::post('status', [ChecklistSubTypeDataController::class, 'statusChange']);
    // Route::post('unique', [ChecklistSubTypeDataController::class, 'Uniquecheck']);
});
