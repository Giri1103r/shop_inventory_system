<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inspection\Master\ChecklistTypeController;
use App\Http\Controllers\Inspection\Master\ChecklistSubTypeDataController;
use App\Http\Controllers\Inspection\MSDSController;
use App\Http\Controllers\Inspection\RRAAController;

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
    Route::get('sample_download', [MSDSController::class, 'DownloadSample']);
    Route::get('import', [MSDSController::class, 'import']);
    Route::post('import/Submit', [MSDSController::class, 'importSubmit']);
    Route::post('status', [MSDSController::class, 'statusChange']);
    Route::post('unique', [MSDSController::class, 'Uniquecheck']);

});

Route::group(['prefix' => 'rraa/ohc_fire_environment_compliance/'], function () {
    Route::get('list', [RRAAController::class, 'index']);
    Route::post('list', [RRAAController::class, 'index']);
    Route::get('add', [RRAAController::class, 'add']);
    Route::post('add/submit', [RRAAController::class, 'store']);
    Route::get('edit/{id}', [RRAAController::class, 'edit']);
    Route::post('edit/submit', [RRAAController::class, 'update']);
    Route::get('view/{id}', [RRAAController::class, 'view']);
    Route::post('delete', [RRAAController::class, 'delete']);
    Route::get('export/excel', [RRAAController::class, 'exportExcel']);
    Route::get('export/pdf', [RRAAController::class, 'exportPdf']);
    Route::get('sample_download', [RRAAController::class, 'DownloadSample']);
    Route::get('import', [RRAAController::class, 'import']);
    Route::post('import/Submit', [RRAAController::class, 'importSubmit']);
    Route::post('status', [RRAAController::class, 'statusChange']);
    Route::post('unique', [RRAAController::class, 'Uniquecheck']);

    Route::get('/employeeid', [RRAAController::class, 'employeeid']);

});