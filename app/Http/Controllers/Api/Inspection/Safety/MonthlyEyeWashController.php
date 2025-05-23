<?php

namespace App\Http\Controllers\Api\Inspection\Safety;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\Inspection\Safety\SafetyInspection;
use App\Models\Inspection\Safety\SafetyStatusLog;
use App\Models\Inspection\Safety\SignatureUpload;
use App\Models\Inspection\Safety\EyeWashInspectionDetails;
use App\Models\Inspection\Safety\MonthlyEyeWashInspection;

class MonthlyEyeWashController extends Controller
{
    private $inspection;
    private $inspection_details;
    private $statusLog;
    private $signature;

    public function __construct()
    {
        $this->inspection = new MonthlyEyeWashInspection();
        $this->inspection_details = new EyeWashInspectionDetails();
        $this->statusLog = new SafetyStatusLog();
        $this->signature = new SignatureUpload();
    }

    public function List(Request $request)
    {
        if (Auth::check()) {
            try {
                $data = $this->inspection->listApi();
                if (count($data) > 0) {
                    return response()->json([
                        'success' => true,
                        'data' => $data,
                        'message' => 'Data Retrieved Successfully',
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'data' => $data,
                        'message' => 'No Data Found',
                    ], 200);
                }
            } catch (Exception $ex) {
                report($ex);
                return $this->sendError(
                    'Unauthorised.',
                    ['error' => 'Please try again after sometimes'],
                    406
                );
            }
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function Add(Request $request)
    {
        try {
            $rules = [
                'inspection_date' => 'required',
                'location_id' => 'required',
                'shift_id' => 'required',
                'next_due' => 'required',
                'unit_id' => 'required',
                'frequency_id' => 'required',
                'sr_no.*' => 'required',
                'location.*' => 'required',
                'resource_code.*' => 'required',
                'condition.*' => 'required',
                'value.*' => 'required',
                'hfsov.*' => 'required',
                'foot_pedal.*' => 'required',
                'eyewash_heads.*' => 'required',
                'receptacle.*' => 'required',
                'water.*' => 'required',
                'quality.*' => 'required',
                'pressure.*' => 'required',
                'temperature.*' => 'required',
            ];

            $messages = [
                'inspection_date.required' => 'Inspection Date is required',
                'location_id.required' => 'Location is required',
                'shift_id.required' => 'Shift is required',
                'next_due.required' => 'Next due date is required',
                'unit_id.required' => 'Unit is required',
                'location.*.required' => 'Location is required',
                'resource_code.*.required' => 'Resource code is required',
                'condition.*.required' => 'Condition is required',
                'value.*.required' => 'Valve is required',
                'hfsov.*.required' => 'Hand free stay open value is required',
                'foot_pedal.*.required' => 'Foot Pedal Value is required',
                'eyewash_heads.*.required' => 'Eye wash heads is required',
                'receptacle.*.required' => 'Receptable name is requried',
                'water.*.required' => 'Water quality is required',
                'quality.*.required' => 'Quality is required',
                'pressure.*.required' => 'Pressure is required',
                'temperature.*.required' => 'Temperature is required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            $store_eyewash_inspection = $this->inspection->storeApi();
            $inspection_id = $store_eyewash_inspection->id;
            $inspection_details = $this->inspection->selectOne($inspection_id);
            $store_inspection_details = $this->inspection_details->storeApi($inspection_id);
            $signature_update = $this->signature->signatureUpload_api(EYE_WASH_INSPECTION, $store_eyewash_inspection->id);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Monthly EyeWash Inspection';
            $notificationData = array(
                'notification_type' => SAFETY_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Monthly EyeWash Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $inspection_id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('safety/eyewash/monthly/verification/view/' . encryptId($inspection_id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Monthly Eyewash Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('safety/eyewash/monthly/verification/verification/' . encryptId($inspection_id) . '/ehs');
                $details = array(
                    'safety_type' => 'Monthly Eyewash Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection_details
                );
                Mail::to($email_id)->queue(new SafetyInspection($details));
            }

            $insert_array = [
                'type' => EYE_WASH_INSPECTION,
                'inspection_id' => $inspection_id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);

            $data = [
                'inspection' => $inspection_details,
            ];
            return $this->sendResponse($data, 'Inspection Created');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
