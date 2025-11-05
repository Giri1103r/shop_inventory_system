<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MasterController;
use App\Http\Controllers\Api\Permit\SafetyPermitController;
use App\Http\Controllers\Api\Inspection\Fire\HoseController;
use App\Http\Controllers\Api\Inspection\Safety\OHSPlantSummary;
use App\Http\Controllers\Api\Inspection\Fire\HoseReelController;
use App\Http\Controllers\Api\Ppemanagement\PperequestController;
use App\Http\Controllers\Api\Trainig\TrainingSheducleController;
use App\Http\Controllers\Api\Inspection\Fire\FireAlarmController;
use App\Http\Controllers\Api\Inspection\Safety\ForkliftInspection;
use App\Http\Controllers\Api\Ppemanagement\PpeExemptionController;
use App\Http\Controllers\Api\Ppemanagement\PpemanagementController;
use App\Http\Controllers\Api\Inspection\Audit\MonthlyAuditController;
use App\Http\Controllers\Api\Inspection\Audit\AuditAnalysisController;
use App\Http\Controllers\Api\Inspection\Fire\IsolationValveController;
use App\Http\Controllers\Api\Inspection\GembaWalk\GembaWalkController;
use App\Http\Controllers\Api\Inspection\Audit\InterUnitAuditController;
use App\Http\Controllers\Api\Inspection\Fire\SprinklerSystemController;
use App\Http\Controllers\Api\Inspection\Safety\SafetyGalleryInspection;
use App\Http\Controllers\Api\Inspection\Audit\AuditAssessmentController;
use App\Http\Controllers\Api\Inspection\Fire\FireExtinguisherController;
use App\Http\Controllers\Api\Inspection\Fire\HooterInspectionController;
use App\Http\Controllers\Api\Inspection\Safety\MonthlyEyeWashController;
use App\Http\Controllers\Api\Inspection\Safety\MonthlyForkLiftInspection;
use App\Http\Controllers\Api\Inspection\Fire\MonthlyFirePumpHouseController;
use App\Http\Controllers\Api\{LoginController, NotificationController, AdminController};
use App\Http\Controllers\Api\Inspection\Fire\DetectorInspectionController;
use App\Http\Controllers\Api\Inspection\Fire\FireModularInspectionController;
use App\Http\Controllers\Api\Inspection\Fire\FireSandBucketInspectionController;
use App\Http\Controllers\Api\Inspection\Audit\Master\TaskMasterController;
use App\Http\Controllers\Api\Inspection\Fire\HydrantRiserController;
use App\Http\Controllers\Api\Inspection\Ohc\EmergencyBuyerFirstAidBagChecklistController;
use App\Http\Controllers\Api\Inspection\Ohc\HealthInstrumentCalibrationController;
use App\Http\Controllers\Api\Inspection\Ohc\Master\FirstAidContoller;
use App\Http\Controllers\Api\Inspection\Ohc\Master\FirstAidMedicineController;
use App\Http\Controllers\Api\Inspection\Ohc\OHCHygieneCleaningChecklist;
use App\Http\Controllers\Api\Inspection\Ohc\WeeklyFirstAidBoxController;
use App\Http\Controllers\Api\Inspection\Safety\FireSafetyEquipment;
use App\Http\Controllers\Api\Inspection\Safety\SafetyWalkObservation;
use App\Http\Controllers\Api\Ims\InitialIncidentController;
use App\Http\Controllers\Api\Inspection\Master\InspectionMasterController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');





Route::middleware('api')->prefix('v1')->group(function () {




    Route::post('login', [LoginController::class, 'login']);


    Route::post('password/forgot', [LoginController::class, 'forgotPassword']);
    Route::post('password/otp', [LoginController::class, 'passwordOtp']);
    Route::post('password/change', [LoginController::class, 'passwordChange']);

    Route::middleware(['auth:api', 'extendAccesstokenexpiration'])->group(function () {

        Route::get('user/profile', [LoginController::class, 'userProfile']);
        Route::get('dashboard', [LoginController::class, 'index']);
        Route::post('logout', [LoginController::class, 'logout']);

        Route::post('notification', [NotificationController::class, 'notification']);
        Route::post('notification/update', [NotificationController::class, 'notificationUpdate']);

        /**
         * Master Routes
         */

        Route::post('/master/employee/dropdown', [MasterController::class, 'employee']);
        Route::post('/master/worker/dropdown', [MasterController::class, 'worker']);
        Route::post('/master/company/dropdown', [MasterController::class, 'company']);
        Route::post('/master/location/dropdown', [MasterController::class, 'location']);
        Route::post('/master/unit/dropdown', [MasterController::class, 'unit']);
        Route::post('/master/department/dropdown', [MasterController::class, 'department']);
        Route::post('/inspection/master/document/dropdown', [MasterController::class, 'documentNumber']);


        Route::group(['prefix' => 'ppe/master'], function () {
            Route::post('/ppetype', [PpemanagementController::class, 'ppetype']);
            Route::post('/ppemaster', [PpemanagementController::class, 'ppemaster']);
        });
        Route::group(['prefix' => 'ppe/master'], function () {
            Route::post('/ppetype', [PpemanagementController::class, 'ppetype']);
            Route::post('/ppemaster', [PpemanagementController::class, 'ppemaster']);
            Route::post('/ppestock', [PpemanagementController::class, 'ppestock']);
        });
        Route::group(['prefix' => 'ppe/pperequest'], function () {
            Route::post('/list', [PperequestController::class, 'list']);
            Route::post('/store', [PperequestController::class, 'store']);
            Route::post('/view', [PperequestController::class, 'view']);
            Route::post('/hodapproval', [PperequestController::class, 'hodapproval']);
            Route::post('/ehsapproval', [PperequestController::class, 'ehsapproval']);
            Route::post('/smapproval', [PperequestController::class, 'smapproval']);
        });
        Route::group(['prefix' => 'ppe/ppeexemption'], function () {
            Route::post('/list', [PpeExemptionController::class, 'list']);
            Route::post('/store', [PpeExemptionController::class, 'store']);
            Route::post('/view', [PpeExemptionController::class, 'view']);
            Route::post('/approval', [PpeExemptionController::class, 'approvereject']);
        });

        Route::group(['prefix' => 'ptw/safetypermit'], function () {
            Route::post('/list', [SafetyPermitController::class, 'list']);
            Route::post('/store', [SafetyPermitController::class, 'store']);
            Route::post('/view', [SafetyPermitController::class, 'view']);
            Route::post('/approval', [SafetyPermitController::class, 'ehsapproval']);
            Route::post('/qrcode', [SafetyPermitController::class, 'qrcode']);
            Route::post('/reassignEmployeeList', [SafetyPermitController::class, 'getReassignEmployee']);
        });
        Route::group(['prefix' => 'trainng/master/dropdown/'], function () {
            Route::post('/topiclist', [TrainingSheducleController::class, 'topiclist']);
            Route::post('/venulist', [TrainingSheducleController::class, 'venulist']);
        });
        Route::group(['prefix' => 'trainng/training-schedule/'], function () {
            Route::post('list', [TrainingSheducleController::class, 'list']);
            Route::post('view', [TrainingSheducleController::class, 'view']);
            Route::post('attendance-recoder', [TrainingSheducleController::class, 'storeAttendance']);
            Route::post('post-assessment', [TrainingSheducleController::class, 'endTrainingStore']);
        });

        Route::group(['prefix' => 'inspection/'], function () {
            Route::group(['prefix' => 'master/'], function () {
                Route::group(['prefix' => 'checklist-type/'], function () {
                    Route::post('list', [InspectionMasterController::class, 'list']);
                });
                Route::group(['prefix' => 'checklist-sub-type/'], function () {
                    Route::post('list', [InspectionMasterController::class, 'list']);
                });
                Route::group(['prefix' => 'checklist-sub-type-data/'], function () {
                    Route::post('list', [InspectionMasterController::class, 'list']);
                });
            });
            Route::group(['prefix' => 'audit/'], function () {
                Route::group(['prefix' => 'audit-assessment/'], function () {
                    Route::post('list', [AuditAssessmentController::class, 'list']);
                    Route::post('view', [AuditAssessmentController::class, 'view']);
                    Route::post('add', [AuditAssessmentController::class, 'store']);
                });

                Route::group(['prefix' => 'audit-analysis/'], function () {
                    Route::post('list', [AuditAnalysisController::class, 'list']);
                    Route::post('view', [AuditAnalysisController::class, 'view']);
                    Route::post('add', [AuditAnalysisController::class, 'add']);
                });

                Route::group(['prefix' => 'monthly-audit/'], function () {
                    Route::post('list', [MonthlyAuditController::class, 'list']);
                    Route::post('view', [MonthlyAuditController::class, 'view']);
                    Route::post('add', [MonthlyAuditController::class, 'store']);
                });

                Route::group(['prefix' => 'inter-unit-audit/'], function () {
                    Route::post('list', [InterUnitAuditController::class, 'list']);
                    Route::post('view', [InterUnitAuditController::class, 'view']);
                    Route::post('add', [InterUnitAuditController::class, 'add']);
                });

                Route::post('task-master/list', [TaskMasterController::class, 'list']);
                Route::post('compilance-category/list', [TaskMasterController::class, 'compilancelist']);
            });

            // gembaWalk
            Route::group(['prefix' => 'gemba-walk/'], function () {
                Route::post('list', [GembaWalkController::class, 'list']);
                Route::post('add', [GembaWalkController::class, 'store']);
                Route::post('view', [GembaWalkController::class, 'view']);
            });

            Route::group(['prefix' => 'fire/'], function () {
                Route::group(['prefix' => 'hooter-inspection/'], function () {
                    Route::post('list', [HooterInspectionController::class, 'List']);
                    Route::post('add', [HooterInspectionController::class, 'Add']);
                    Route::post('view', [HooterInspectionController::class, 'View']);
                });

                Route::group(['prefix' => 'fire-alarm-inspection/'], function () {
                    Route::post('list', [FireAlarmController::class, 'List']);
                    Route::post('add', [FireAlarmController::class, 'Add']);
                    Route::post('view', [FireAlarmController::class, 'View']);
                });

                Route::group(['prefix' => 'fire-extinguisher/inspection/'], function () {
                    Route::post('list', [FireExtinguisherController::class, 'List']);
                    Route::post('add', [FireExtinguisherController::class, 'Add']);
                    Route::post('view', [FireExtinguisherController::class, 'Add']);
                });

                Route::group(['prefix' => 'hose-box-inspection/'], function () {
                    Route::post('list', [HoseController::class, 'List']);
                    Route::post('add', [HoseController::class, 'Add']);
                    Route::post('view', [HoseController::class, 'View']);
                });

                Route::group(['prefix' => 'hose-reel-inspection/'], function () {
                    Route::post('list', [HoseReelController::class, 'List']);
                    Route::post('add', [HoseReelController::class, 'Add']);
                    Route::post('view', [HoseReelController::class, 'View']);
                });

                Route::group(['prefix' => 'isolating-valve-inspection/'], function () {
                    Route::post('list', [IsolationValveController::class, 'List']);
                    Route::post('add', [IsolationValveController::class, 'Add']);
                    Route::post('view', [IsolationValveController::class, 'View']);
                });

                Route::group(['prefix' => 'monthly-fire-pump-house-inspection/'], function () {
                    Route::post('list', [MonthlyFirePumpHouseController::class, 'List']);
                    Route::post('add', [MonthlyFirePumpHouseController::class, 'Add']);
                    Route::post('view', [MonthlyFirePumpHouseController::class, 'View']);
                });

                Route::group(['prefix' => 'sprinkler-system-inspection/'], function () {
                    Route::post('list', [SprinklerSystemController::class, 'List']);
                    Route::post('add', [SprinklerSystemController::class, 'Add']);
                    Route::post('view', [SprinklerSystemController::class, 'View']);
                });

                Route::group(['prefix' => 'detector-inspection/'], function () {
                    Route::post('list', [DetectorInspectionController::class, 'List']);
                    Route::post('add', [DetectorInspectionController::class, 'Add']);
                    Route::post('view', [DetectorInspectionController::class, 'View']);
                });

                Route::group(['prefix' => 'fire-sand-bucket-inspection/'], function () {
                    Route::post('list', [FireSandBucketInspectionController::class, 'List']);
                    Route::post('add', [FireSandBucketInspectionController::class, 'Add']);
                    Route::post('view', [FireSandBucketInspectionController::class, 'View']);
                });
                Route::group(['prefix' => 'hydrant-and-riser/'], function () {
                    Route::post('list', [HydrantRiserController::class, 'list']);
                    Route::post('view', [HydrantRiserController::class, 'view']);
                    Route::post('add', [HydrantRiserController::class, 'store']);
                });

                Route::group(['prefix' => 'fire-modular-inspection/'], function () {
                    Route::post('list', [FireModularInspectionController::class, 'List']);
                    Route::post('add', [FireModularInspectionController::class, 'Add']);
                    Route::post('view', [FireModularInspectionController::class, 'View']);
                });
            });

            // OHC
            Route::group(['prefix' => 'ohc/'], function () {

                // Master
                Route::post('first-aid-stock/list', [FirstAidContoller::class, 'medicine_stock_list']);
                Route::post('first-aid-medicine/list', [FirstAidMedicineController::class, 'medicine_list']);
                Route::post('first-aider', [WeeklyFirstAidBoxController::class, 'getFirstAiderName']);

                // Emergency Buyer
                Route::group(['prefix' => 'emergency-buyer-first-bag-checklist/'], function () {
                    Route::post('list', [EmergencyBuyerFirstAidBagChecklistController::class, 'List']);
                    Route::post('view', [EmergencyBuyerFirstAidBagChecklistController::class, 'View']);
                    Route::post('add', [EmergencyBuyerFirstAidBagChecklistController::class, 'Store']);
                });

                // HealthInstrument
                Route::group(['prefix' => 'health-instrument-calibration/'], function () {
                    Route::post('list', [HealthInstrumentCalibrationController::class, 'list']);
                    Route::post('view', [HealthInstrumentCalibrationController::class, 'view']);
                    Route::post('add', [HealthInstrumentCalibrationController::class, 'store']);
                });

                // weeklyFirstAidBox
                Route::group(['prefix' => 'weekly-first-aid-box/'], function () {
                    Route::post('list', [WeeklyFirstAidBoxController::class, 'list']);
                    Route::post('view', [WeeklyFirstAidBoxController::class, 'view']);
                    Route::post('add', [WeeklyFirstAidBoxController::class, 'store']);
                });

                Route::group(['prefix' => 'ohc-hygiene-cleaning-checklist/'], function () {
                    Route::post('list', [OHCHygieneCleaningChecklist::class, 'list']);
                    Route::post('view', [OHCHygieneCleaningChecklist::class, 'view']);
                    Route::post('add', [OHCHygieneCleaningChecklist::class, 'store']);
                });
            });
        });

        //Master -  Safety
        Route::post('shift/list', [MonthlyForkLiftInspection::class, 'shift']);
        Route::post('frequency/list', [MonthlyForkLiftInspection::class, 'frequencyName']);
        Route::post('forklift_type/list', [MonthlyForkLiftInspection::class, 'forklift_type']);
        Route::post('equipment/list', [FireSafetyEquipment::class, 'equipment']);

        //Safety
        Route::prefix('safety')->group(function () {
            Route::prefix('safety-gallery-inspection')->group(function () {
                Route::post('list', [SafetyGalleryInspection::class, 'list']);
                Route::post('add', [SafetyGalleryInspection::class, 'store']);
                Route::post('view', [SafetyGalleryInspection::class, 'view']);
                Route::post('level-one-verification', [SafetyGalleryInspection::class, 'leveloneverification']);
                Route::post('ehs-head-approval', [SafetyGalleryInspection::class, 'ehsheadapproval']);
            });

            Route::prefix('forklift-inspection')->group(function () {
                Route::post('list', [ForkliftInspection::class, 'list']);
                Route::post('add', [ForkliftInspection::class, 'store']);
                Route::post('view', [ForkliftInspection::class, 'view']);
                Route::post('ehs-head-approval', [ForkliftInspection::class, 'EhsapprovalSubmit']);
            });

            Route::prefix('monthly-forklift-inspection')->group(function () {
                Route::post('list', [MonthlyForkLiftInspection::class, 'list']);
                Route::post('add', [MonthlyForkLiftInspection::class, 'store']);
                Route::post('view', [MonthlyForkLiftInspection::class, 'view']);
                Route::post('ehs-officer', [MonthlyForkLiftInspection::class, 'EHSofficersubmit']);
                Route::post('capa-action', [MonthlyForkLiftInspection::class, 'capaSubmit']);
                Route::post('capa-reverification', [MonthlyForkLiftInspection::class, 'capaverification']);
                Route::post('level-one-manager-approval', [MonthlyForkLiftInspection::class, 'leveloneverfication']);
                Route::post('level-two-manager-approval', [MonthlyForkLiftInspection::class, 'leveltwoverfication']);
            });

            Route::prefix('ohs-plant-summary')->group(function () {
                Route::post('list', [OHSPlantSummary::class, 'list']);
                Route::post('add', [OHSPlantSummary::class, 'store']);
                Route::post('view', [OHSPlantSummary::class, 'view']);
            });

            Route::prefix('fire-safety-equipment')->group(function () {
                Route::post('list', [FireSafetyEquipment::class, 'list']);
                Route::post('add', [FireSafetyEquipment::class, 'store']);
                Route::post('view', [FireSafetyEquipment::class, 'view']);
            });

            Route::prefix('safety-walk-observation')->group(function () {
                Route::post('list', [SafetyWalkObservation::class, 'list']);
                Route::post('add', [SafetyWalkObservation::class, 'store']);
                Route::post('view', [SafetyWalkObservation::class, 'view']);
            });

            Route::prefix('eye-wash-inspection/monthly')->group(function () {
                Route::post('list', [MonthlyEyeWashController::class, 'list']);
                Route::post('view', [MonthlyEyeWashController::class, 'view']);
                Route::post('ehs-officer', [MonthlyEyeWashController::class, 'EHSOfficerSubmit']);
                Route::post('capa-action', [MonthlyEyeWashController::class, 'CAPASubmit']);
                Route::post('ehs-reverification', [MonthlyEyeWashController::class, 'CAPAVerifySubmit']);
                Route::post('level-one-action', [MonthlyEyeWashController::class, 'levelOneManagerSubmit']);
                Route::post('level-two-action', [MonthlyEyeWashController::class, 'levelTwoManagerSubmit']);
            });
        });



        Route::group(['prefix' => 'incident/initial-incident/'], function () {
            Route::post('master/iir_type/list', [InitialIncidentController::class, 'iirTypeList']);
            Route::post('list', [InitialIncidentController::class, 'list']);
            Route::post('view', [InitialIncidentController::class, 'view']);
            Route::post('store', [InitialIncidentController::class, 'store']);
            Route::post('investigationList', [InitialIncidentController::class, 'investigationList']);
            Route::post('investigation/view', [InitialIncidentController::class, 'investigationView']);
            Route::post('calist', [InitialIncidentController::class, 'calist']);
            Route::post('capa/view', [InitialIncidentController::class, 'capaView']);
            Route::get('generate-random-id', [InitialIncidentController::class, 'generate']);
            Route::post('get-body-part-url', [InitialIncidentController::class, 'getBodyPartUrl']);
            Route::post('list-bodyparts', [InitialIncidentController::class, 'listBodyParts']);
        });
    });
});
