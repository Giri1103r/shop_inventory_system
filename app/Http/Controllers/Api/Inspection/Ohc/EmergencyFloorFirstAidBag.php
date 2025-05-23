<?php

namespace App\Http\Controllers\Api\Inspection\Ohc;

use App\Http\Controllers\Api\BaseController;
use Exception;
use App\Models\Master\Unit;
use App\Models\Master\Work;
use Illuminate\Http\Request;
use App\Models\Master\Employee;
use App\Models\Master\Location;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspection\Master\Shift;
use Illuminate\Support\Facades\Validator;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Ohc\FirstAidBagChecklist;
use App\Models\Inspection\Ohc\Master\FirstAidEquipment;

class EmergencyFloorFirstAidBag extends BaseController
{
    private $first_aid_bag;
    private $medicine;
    private $signature;
    private $document_reference;
    private $location;
    private $unit;
    private $frequency;
    private $shift;
    private $employee;
    private $work;


    public function __construct()
    {
        $this->first_aid_bag = new FirstAidBagChecklist();
        $this->medicine = new FirstAidEquipment();
        $this->signature = new OhcSignature();
        $this->document_reference = new InspectionStaticDocno();
        $this->location  = new Location();
        $this->unit = new Unit();
        $this->frequency = new Frequency();
        $this->shift = new Shift();
        $this->employee = new Employee();
        $this->work = new Work();
    }

    public function List(Request $request)
    {
        if (Auth::check()) {
            try {
                $data = $this->first_aid_bag->listApi();
                if (count($data) > 0) {
                    return response()->json([
                        'status' => true,
                        'data' => $data,
                        'message' => 'Data Received Successfully'
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'data' => $data,
                        'message' => 'No Data Found'
                    ], 200);
                }
            } catch (Exception $ex) {
                report($ex);
                return $this->sendError(
                    'Unauthorised.',
                    ['error' => 'Please try again after sometimes'],
                    404
                );
            }
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 404);
        }
    }

    public function View(Request $request)
    {
        try {
            if (Auth::check()) {
                $id = $request->id;
                $inspection = $this->first_aid_bag->selectOne($id);
                $inspection_data = json_decode($inspection->inspection_data, true);
                $inspection_type = OHC_TYPE_EMERGENCY_BUYER_FIRST_AID_BAG_CHECKLIST;

                $medicine_data = [];
                foreach ($inspection_data as $details) {
                    $data = [
                        'medicine_name' => getMedicinename($details['medicine_id']),
                        'available_quantity' => $details['available_quantity'],
                        'expired_date' => Displaydateformat($details['expired_date']),
                        'remarks' => $details['remarks'],
                        'freeze_quantity' => $details['freeze_quantity'],
                        'emp_id' => getUsername($details['emp_id']),
                    ];
                    $medicine_data[] = $data;
                };

                $inspection_details = [
                    'id' => $inspection->id,
                    'date_of_inspection' => Displaydateformat($inspection->inspection_date),
                    'next_due' => Displaydateformat($inspection->next_due),
                    'location_name' => getLocationname($inspection->location),
                    'shift_name' => getShift($inspection->shift_id),
                    'frequency_name' => getFrequencyname($inspection->frequency),
                    'unit_name' => getUnitname($inspection->unit),
                    'frequency' => getFrequencyname($inspection->frequency),
                ];

                $success = [
                    'id' => $inspection->id,
                    'inspection_details' => $inspection_details,
                    'medicine_details' => $medicine_data,
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

            $rules = [
                'inspection_date' => 'required',
                'frequency_id' => 'required',
                'location_id' => 'required',
                'unit_id' => 'required',
                'next_due' => 'required',
                'available_quantity.*' => 'required',
                'expired_date.*' => 'required',
                'emp_id.*' => 'required',
                'remarks.*' => 'required'
            ];

            $messages = [
                'inspection_date.required' => 'Inspection Date is required.',
                'frequency_id.required' => 'Frequency is required.',
                'location_id.required' => 'Location is required.',
                'unit_id.required' => 'Unit is required.',
                'next_due.required' => 'Next Due Date is required.',
                'available_quantity.*.required' => 'Available Quantity is required',
                'expired_date.*.required' => 'Expired Date is required',
                'remarks.*' => 'Remarks is required',
                'emp_id.*' => 'Employee is required',
                'signature_upload' => 'Signature is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            $inspection_data = $this->first_aid_bag->store_api();
            $inspection_details = $this->first_aid_bag->selectOne($inspection_data->id);


            return response()->json([
                'success' => true,
                'message' => 'Inspection data stored successfully',
                'data' => $inspection_data
            ], 201);
        } catch (\Exception $ex) {
            dd($ex);
            report($ex);
            return $this->sendError(
                'Unauthorised.',
                ['error' => 'Please try again after sometimes'],
                404
            );
        }
    }
}
