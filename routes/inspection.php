<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inspection\Master\ChecklistTypeController;
use App\Http\Controllers\Inspection\Master\ChecklistSubTypeDataController;


Route::group(['prefix' => 'inspection/master/'], function(){
    Route::GET('checklist-type/list', [ChecklistTypeController::class, 'Index']);
    Route::POST('checklist-type/list', [ChecklistTypeController::class, 'Index']);
    Route::GET('checklist-type/add', [ChecklistTypeController::class, 'Add']);
    Route::POST('checklist-type/add/submit', [ChecklistTypeController::class, 'Store']);
    Route::POST('checklist-type/unique', [ChecklistTypeController::class, 'UniqueCheck']);
    Route::GET('checklist-type/edit/{id}', [ChecklistTypeController::class, 'Edit']);
    Route::POST('checklist-type/edit/submit', [ChecklistTypeController::class, 'Update']);
    Route::GET('checklist-type/view/{id}', [ChecklistTypeController::class, 'View']);
    Route::POST('checklist-type/delete', [ChecklistTypeController::class, 'Delete']);
    Route::POST('checklist-type/status', [ChecklistTypeController::class, 'StatusChange']);
    Route::GET('checklist-type/export/excel', [ChecklistTypeController::class, 'ExportExcel']);
    Route::GET('checklist-type/export/pdf', [ChecklistTypeController::class, 'ExportPDF']);
    Route::GET('checklist-type/import', [ChecklistTypeController::class, 'Import']);
    Route::POST('checklist-type/import/submit', [ChecklistTypeController::class, 'ImportSubmit']);
    Route::GET('checklist-type/sampledownload', [ChecklistTypeController::class, 'DownloadSample']);
    Route::POST('checklist-type/lists', [ChecklistTypeController::class, 'Checklists']);

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

