<?php

namespace App\Http\Controllers\Api\Inspection\Fire;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\Inspection\Fire\FireInspection;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\FireFileUpload;
use App\Models\Inspection\Fire\FireExtinguisher;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\FireExtinguisherType;
use App\Models\Inspection\Fire\FireExtinguisherDetails;

class FireExtinguisherController extends Controller
{
    private $inspection;
    private $inspection_details;
    private $fire_extinguisher_type;
    private $statusLog;
    private $files;
    private $signature;

    public function __construct()
    {
        $this->inspection = new FireExtinguisher();
        $this->inspection_details = new FireExtinguisherDetails();
        $this->fire_extinguisher_type = new FireExtinguisherType();
        $this->statusLog = new FireStatusLog();
        $this->files = new FireFileUpload();
        $this->signature = new FireSignatureUpload();
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
                'issue_date' => 'required',
                'rev_date' => 'required',
                'inspection_date' => 'required',
                'location_id' => 'required',
                'shift_id' => 'required',
                'next_due' => 'required',
                'unit_id' => 'required',
                'frequency_id' => 'required',
                'sr_no.*' => 'required',
                'department.*' => 'required',
                'location.*' => 'required',
                'description.*' => 'required',
                'type.*' => 'required',
                'capacity.*' => 'required',
                'quantity.*' => 'required',
                'cylinder_pressure.*' => 'required',
                'discharge_tube.*' => 'required',
                'approach.*' => 'required',
                'safety_pin.*' => 'required',
                'observation.*' => 'required',
                'remarks.*' => 'required',
            ];

            $messages = [
                'issue_date.required' => 'Issue Date is required',
                'rev_date.required' => 'Revision Data is required',
                'inspection_date.required' => 'Inspection Date is required',
                'location_id.required' => 'Location is required',
                'shift_id.required' => 'Shift is required',
                'next_due.required' => 'Next due date is required',
                'unit_id.required' => 'Unit is required',
                'department.*.required' => 'Department is required',
                'location.*.required' => 'Location is required',
                'description.*.required' => 'Description is required',
                'type.*.required' => 'Fire Extinguisher Type is required',
                'capacity.*.required' => 'Capacity is required',
                'quantity.*.required' => 'Quantity is required',
                'cylinder_pressure.*.required' => 'Cylinder Pressure is required',
                'discharge_tube.*.required' => 'Discharge Tube is required',
                'approach.*.required' => 'Approach is required',
                'observation.required' => 'Observation is required',
                'quantity.*.required' => 'Quantity is required',
                'remarks.*.required' => 'Remarks is required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $inspection = $this->inspection->storeApi();
            $inspection_type = FIRE_EXTINGUISHER_INSPECTION;
            $id = $inspection->id;

            $inspection_details = $this->inspection_details->storeApi($id);
            $inspection_file = $this->files->file_upload_api($inspection_type, $id);

            // $checklist_store = $this->checklist_follow->store($inspection_type, $id);

            $signature_update = $this->signature->CheckedBySignatureApi($id, $inspection_type);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Fire Extinguisher Inspection';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Fire Extinguisher Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/fire_extinguisher-inspection/verification/' . encryptId($id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Fire Extinguisher Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/fire_extinguisher-inspection/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'fire_type' => 'Fire Extinguisher Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => FIRE_EXTINGUISHER_INSPECTION,
                'inspection_id' => $id,
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
