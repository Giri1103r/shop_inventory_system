<?php

namespace App\Http\Controllers\Api\Inspection\Ohc;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Models\Inspection\Ohc\HealthInstrumentCalibration;
use App\Models\Inspection\Ohc\HealthInstrumentCalibrationDetails;

class HealthInstrumentCalibrationController extends BaseController
{
    private $health_instrument_calibration;
    private $health_instrument_calibration_details;

    public function __construct()
    {
        $this->health_instrument_calibration = new HealthInstrumentCalibration();
        $this->health_instrument_calibration_details = new HealthInstrumentCalibrationDetails();
    }

    public function list()
    {
        if (Auth::check()) {
            try {
                $data = $this->health_instrument_calibration->listApi();
                if (count($data) > 0) {
                    return response()->json([
                        'status' => true,
                        'data' => $data,
                        'message' => 'Data Received Successfully'
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'data' => $data,
                        'message' => 'No Date Found'
                    ]);
                }
            } catch (Exception $ex) {
                dd($ex);
                return $this->sendError('Unauthotized', ['error', 'Unauthorized'], 404);
            }
        } else {
            return $this->sendError('Unauthotized', ['error', 'Unauthorized'], 404);
        }
    }

    public function view(Request $request)
    {
        try {
            if (Auth::check()) {

                $id = $request->id;
                $inspection = $this->health_instrument_calibration->getInspectionData($id);
                $inspection_checklist = $this->health_instrument_calibration_details->getInspectionDetails($inspection->id);

                $inspection_data = [
                    'id' => $inspection->id,
                    'health_instrument_id' => $inspection->health_auto_id,
                    'document_no' => $inspection->doc_no,
                    'issue_date' => Displaydateformat($inspection->issue_date),
                    'revision_date' => $inspection->rev_dt,
                    'unit_name' => getUnitname($inspection->unit_id)

                ];

                $inspection_checklist_details = [];
                $inspection_checklist_data = [];

                foreach ($inspection_checklist as $index => $data) {
                    $inspection_checklist_details['health_instrument_id'] = $data['health_instrument_id'];
                    $inspection_checklist_details['instrument_name'] = $data['instrument_name'];
                    $inspection_checklist_details['resource_code'] = $data['resource_code'];
                    $inspection_checklist_details['exact_location'] = $data['exact_location'];
                    $inspection_checklist_details['instrument_serial_no'] = $data['instrument_serial_no'];
                    $inspection_checklist_details['make'] = $data['make'];
                    $inspection_checklist_details['model'] = $data['model'];
                    $inspection_checklist_details['instrument_range'] = $data['instrument_range'];
                    $inspection_checklist_details['calibration_frequency'] = getFrequencyname($data['calibration_frequency']);
                    $inspection_checklist_details['remarks'] = $data['remarks'];

                    $inspection_checklist_data[$index] = $inspection_checklist_details;
                }

                $success = [
                    'id' => $inspection->id,
                    'inspection_data' => $inspection_data,
                    'inspection_checklist_data' => $inspection_checklist_data
                ];

                return $this->sendResponse($success, 'Date Received Successfully');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 404);
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError(
                'Unauthorised.',
                ['error' => 'Please try again after sometimes'],
                404
            );
        }
    }


    public function Store(Request $request)
    {
        try {



            $health_instrument = $this->health_instrument_calibration->storeApi();
            $id = $health_instrument->id;

            $health_instrument_details = $this->health_instrument_calibration_details->storeApi($id);

            return response()->json([
                'success' => true,
                'message' => 'Inspection data stored successfully',
                'data' => $health_instrument
            ], 201);
        } catch (Exception $ex) {
            dd($ex);
            return $this->sendError(
                'Unauthorised.',
                ['error' => 'Please try again after sometimes'],
                404
            );
        }
    }
}
