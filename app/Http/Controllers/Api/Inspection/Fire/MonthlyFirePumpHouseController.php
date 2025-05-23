<?php

namespace App\Http\Controllers\Api\Inspection\Fire;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Api\BaseController;
use App\Mail\Inspection\Fire\FireInspection;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\FireFileUpload;
use App\Models\Inspection\InspectionStaticDocno;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\Fire\MonthlyFirePumpHouseInspection;

class MonthlyFirePumpHouseController extends BaseController
{
    private $inspection;
    private $statusLog;
    private $files;
    private $signature;
    private $valve_type;
    private $document_reference;

    public function __construct()
    {
        $this->inspection = new MonthlyFirePumpHouseInspection();
        $this->statusLog = new FireStatusLog();
        $this->files = new FireFileUpload();
        $this->signature = new FireSignatureUpload();
        $this->document_reference = new InspectionStaticDocno();
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
            $monthly_fire_inspection = $this->inspection->storeApi();
            $id = $monthly_fire_inspection->id;
            $inspection_type = MONTHLY_FIRE_PUMP;

            $signature_update = $this->signature->CheckedBySignatureApi($id, $inspection_type);

            $ehsOfficer = GetEHSOfficer();
            $ehsOfficers = $ehsOfficer->pluck('id')->toArray();
            $mailsubject = 'Monthly Fire Pump House Inspection';
            $notificationData = array(
                'notification_type' => FIRE_INSPECTION,
                'module_type' => 3,
                'notification_message' => $mailsubject,
                'mobile_notification' => json_encode(array(
                    'title' => $mailsubject,
                    'message' => "Fire Associate create the Monthly Fire Pump House Inspection",
                    'icon' =>  admin_url('public/assets/icons/occupational-therapy.png'),
                    'id' => $monthly_fire_inspection->id,
                    'module' => 1,
                )),
                'web_link' =>  admin_url('fire/monthly-fire-pump-house-inspection/view/' . encryptId($monthly_fire_inspection->id)),
                'assigned_user' => array_to_string($ehsOfficers),
                'created_by' => Auth::id(),
            );
            notificationSave($notificationData);

            $title = 'Fire Associate create the Monthly Fire Pump House Inspection';
            foreach ($ehsOfficers as $user) {
                $email_id = getUseremail($user);
                $url = admin_url('fire/monthly-fire-pump-house-inspection/verification/' . encryptId($id) . '/ehs');
                $details = array(
                    'fire_type' => 'Monthly Fire Pump House Inspection',
                    'email' => $email_id,
                    'mail_subject' => $mailsubject,
                    'title' => $title,
                    'url' => $url,
                    'data' => $monthly_fire_inspection
                );
                Mail::to($email_id)->queue(new FireInspection($details));
            }

            $insert_array = [
                'type' => MONTHLY_FIRE_PUMP,
                'inspection_id' => $monthly_fire_inspection->id,
                'from_status' => 0,
                'to_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
                'created_by' => Auth::id(),
            ];
            $this->statusLog->create($insert_array);

            $data = [
                'inspection' => $monthly_fire_inspection,
            ];
            return $this->sendResponse($data, 'Inspection Created');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
