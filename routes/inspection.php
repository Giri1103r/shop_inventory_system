<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inspection\Master\ChecklistTypeController;


Route::group(['prefix' => 'inspection'], function(){
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
});


  //CheckList Category Master Routes

  // CheckList Subcategory Master Routes
//   Route::GET('master/check_sub_cat/list', [TestChecklistSubCategoryController::class, 'Index']);
//   Route::POST('master/check_sub_cat/list', [TestChecklistSubCategoryController::class, 'Index']);
//   Route::GET('master/check_sub_cat/add', [TestChecklistSubCategoryController::class, 'Add']);
//   Route::POST('master/check_sub_cat/add/submit', [TestChecklistSubCategoryController::class, 'Store']);
//   Route::POST('master/check_sub_cat/unique', [TestChecklistSubCategoryController::class, 'Uniquecheck']);
//   Route::GET('master/check_sub_cat/edit/{id}', [TestChecklistSubCategoryController::class, 'Edit']);
//   Route::POST('master/check_sub_cat/edit/submit', [TestChecklistSubCategoryController::class, 'Update']);
//   Route::GET('master/check_sub_cat/view/{id}', [TestChecklistSubCategoryController::class, 'View']);
//   Route::POST('master/check_sub_cat/delete', [TestChecklistSubCategoryController::class, 'Delete']);
//   Route::POST('master/check_sub_cat/status', [TestChecklistSubCategoryController::class, 'StatusChange']);
//   Route::GET('master/check_sub_cat/export/excel', [TestChecklistSubCategoryController::class, 'ExportExcel']);
//   Route::GET('master/check_sub_cat/export/pdf', [TestChecklistSubCategoryController::class, 'ExportPDF']);
//   Route::GET('master/check_sub_cat/import', [TestChecklistSubCategoryController::class, 'Import']);
//   Route::POST('master/check_sub_cat/import/submit', [TestChecklistSubCategoryController::class, 'ImportSubmit']);
//   Route::GET('master/check_sub_cat/sampledownload', [TestChecklistSubCategoryController::class, 'DownloadSample']);
