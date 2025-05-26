<?php

namespace App\Http\Controllers\Api\Inspection\Ohc;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Http\Controllers\Api\BaseController;
use App\Models\Inspection\Ohc\EmergencyBuyerFirstAidChecklist;

class EmergencyBuyerFirstAidBagChecklistController extends BaseController
{
    private $emergency_buyer_first_aid_bag;
    private $signature;


    public function __construct()
    {
        $this->emergency_buyer_first_aid_bag = new EmergencyBuyerFirstAidChecklist();
        $this->signature = new OhcSignature();
    }

    public function List(Request $request)
    {
        if (Auth::check()) {
            try {
                $data = $this->emergency_buyer_first_aid_bag->listApi();
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
                $inspection = $this->emergency_buyer_first_aid_bag->selectOne($id);
                $inspection_data = json_decode($inspection->inspection_data);
                $inspection_type = OHC_TYPE_EMERGENCY_BUYER_FIRST_AID_BAG_CHECKLIST;
                $inspection_file = GetOHCSignature($inspection->created_by, $inspection->id, $inspection_type);

                $medicine_data = [];
                foreach ($inspection_data as $details) {
                    $data = [
                        'medicine_name' => getMedicinename($details->medicine_id),
                        'available_quantity' => $details->available_quantity,
                        'expired_date' => Displaydateformat($details->expired_date),
                        'remarks' => $details->remarks
                    ];
                    $medicine_data[] = $data;
                };

                $inspection_details = [
                    'id' => $inspection->id,
                    'date_of_inspection' => Displaydateformat($inspection->date_of_inspection),
                    'location_first_aid_bag' => $inspection->location_first_aid_bag,
                    'shift_name' => getShift($inspection->shift_is),
                    'due_date' => Displaydateformat($inspection->due_date),
                    'unit_name' => getUnitname($inspection->unit_id),
                    'frequency' => getFrequencyname($inspection->frequency_id),
                    'medicine_details' => $medicine_data,
                    'signature' => admin_url($inspection_file),
                    'remark_by' => $inspection->remark_by

                ];

                $success = [
                    'id' => $inspection->id,
                    'inspection_details' => $inspection_details
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
                'date_of_inspection' => 'required',
                'location_first_aid_bag' => 'required',
                'shift' => 'required',
                'next_due_date' => 'required',
                'unit_id' => 'required',
                'frequency_id' => 'required',
                'medicine_id.*' => 'required',
                'freeze_quantity.*' => 'required',
                'available_quantity.*' => 'required',
                'expired_date.*' => 'required',
                'remarks.*' => 'required',
                'remark_by' => 'required',
            ];

            $messages = [
                'date_of_inspection.required' => 'Date of inspection is required.',
                'location_first_aid_bag.required' => 'Location of the first aid bag is required.',
                'shift.required' => 'Shift selection is required.',
                'next_due_date.required' => 'Next due date is required.',
                'unit_id.required' => 'Unit selection is required.',
                'frequency_id.required' => 'Frequency is required.',
                'medicine_id.*.required' => 'Medicine ID is required.',
                'freeze_quantity.*.required' => 'Freeze quantity is required.',
                'available_quantity.*.required' => 'Available quantity is required.',
                'expired_date.*.required' => 'Expired date is required.',
                'remarks.*.required' => 'Remarks are required.',
                'remark_by.required' => 'Remark by is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            $inspection_data = $this->emergency_buyer_first_aid_bag->storeApi();
            $inspection_type = OHC_TYPE_EMERGENCY_BUYER_FIRST_AID_BAG_CHECKLIST;
            $inspection_details = $this->emergency_buyer_first_aid_bag->selectOne($inspection_data->id);
            $files = $this->signature->requestorsignatureUpload_api($inspection_type, $inspection_details->id);


            return response()->json([
                'success' => true,
                'message' => 'Inspection data stored successfully',
                'data' => $inspection_data
            ], 201);
        } catch (\Exception $ex) {
            report($ex);
            return $this->sendError(
                'Unauthorised.',
                ['error' => 'Please try again after sometimes'],
                404
            );
        }
    }
}
