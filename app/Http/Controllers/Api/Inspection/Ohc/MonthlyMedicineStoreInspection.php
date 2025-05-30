<?php

namespace App\Http\Controllers\Api\Inspection\Ohc;

use App\Http\Controllers\Api\BaseController;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\Inspection\Ohc\MonthlyMedicineStore;

class MonthlyMedicineStoreInspection extends BaseController
{
    private $medicine_checklist;
    private $medicine;
    private $signature;


    public function __construct()
    {
        $this->medicine_checklist = new MonthlyMedicineStore();
        $this->medicine = new Medicine();
        $this->signature = new OhcSignature();
    }
    public function List(Request $request)
    {
        if (Auth::check()) {
            try {
                $data = $this->medicine_checklist->listApi();
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
                $inspection = $this->medicine_checklist->selectOne($id);
                $inspection_data = json_decode($inspection->inspection_data, true);


                $medicine_data = [];
                foreach ($inspection_data as $details) {
                    $data = [
                        'medicine_name' => getMedicinename($details['medicine_id']),
                        'available_quantity' => $details['available_quantity'],
                        'expired_date' => Displaydateformat($details['expired_date']),
                        'remarks' => $details['remarks'],
                        'emp_name' => ($details['emp_id']),
                    ];
                    $medicine_data[] = $data;
                };


                $inspection_details = [
                    'id' => $inspection->id,
                    'date_of_inspection' => Displaydateformat($inspection->inspection_date),
                    'next_due' => Displaydateformat($inspection->next_due),
                ];


                $approval_array[] = null;
                if ($inspection->inspection_status != OBSERVATION_PENDING) {
                    $inspection_type = OHC_TYPE_MONTHLY_MEDICINE_STORE;
                    $verified_by = GetOHCSignature($inspection->updated_by, $inspection->id, $inspection_type);
                        $approval_array = [
                            'approval_remarks' => $inspection->approval_remarks,
                            'approved_by' => getUsername($inspection->updated_by),
                            'approved_at' => Displaydateformat($inspection->updated_at),
                            'approval_signature' => admin_url($verified_by),
                        ];
                }

                $success = [
                    'id' => $inspection->id,
                    'inspection_details' => $inspection_details,
                    'medicine_details' => $medicine_data,
                    'approval_array' => $approval_array,
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
                'next_due' => 'required',
                'available_quantity.*' => 'required',
                'expired_date.*' => 'required',
                'emp_id.*' => 'required',
                'remarks.*' => 'required',
            ];

            $messages = [
                'inspection_date.required' => 'Inspection Date is required.',
                'next_due.required' => 'Next Due Date is required.',
                'available_quantity.*.required' => 'Available Quantity is required',
                'expired_date.*.required' => 'Expired Date is required',
                'remarks.*' => 'Remarks is required',
                'emp_id.*' => 'Employee is required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            $inspection_data = $this->medicine_checklist->store_api();
            $inspection_details = $this->medicine_checklist->selectOne($inspection_data->id);


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
