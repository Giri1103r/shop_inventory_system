<?php

namespace App\Http\Controllers\Api\Permit;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Permit\SafetyApproveReject;
use App\Models\Permit\SafetyPermit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Permit\SafetyPermitExtension;
use App\Models\Permit\Statuslog;
class SafetyPermitController extends BaseController
{
    private $safetypermit;
    private $pperequest;
    private $approvereject;
    private $statuslog;
    private $safetyPermitExtension;


    public function __construct()
    {
        $this->safetypermit = new SafetyPermit();
        $this->approvereject = new SafetyApproveReject();
        $this->statuslog = new Statuslog();
        $this->safetyPermitExtension = new SafetyPermitExtension();
    }
    public function list(Request $request)
    {
        if (Auth::user()) {
            $search = '';
            $empid = Auth::id();

            $user = Auth::user();
            $empId = $user->employee_id;
            $userRole = $user->role;
            $unit_id = $user->unit_id;
            $empid = $user->id;
            $userRole = string_to_array($userRole);
            if (isAdmin()) {
                $safety_permit_array =SafetyPermit::select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status');
            } elseif (in_array(ROLE_EHS_OFFICER, $userRole)) {
                $safety_permit_array =SafetyPermit::select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status');
            } elseif (in_array(ROLE_PLANT_HEAD, $userRole)) {
                $safety_permit_array =SafetyPermit::select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status')->where('ptw_safety.unit_id', $unit_id);
            } elseif (in_array(ROLE_EHS_HEAD, $userRole)) {
                $safety_permit_array =SafetyPermit::select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status');
            } else {
                $safety_permit_array =SafetyPermit::select('ptw_safety.*', 'masters_unit.unit_name', 'ptw_status.status_name', 'ptw_status.bg_color')->leftJoin('masters_unit', 'masters_unit.id', '=', 'ptw_safety.unit_id')->leftJoin('ptw_status', 'ptw_status.id', '=', 'ptw_safety.permit_status')->where('ptw_safety.created_by', $empid);
            }
            if (!empty($request->search['value'])) {
                $search = $request->search['value'];
                $safety_permit_array = $safety_permit_array->where(function ($query) use ($search) {
                    $query->orWhereRaw('permit_id LIKE ?', ["%{$search}%"]);
                });
            }

            $safety_permit_array = $safety_permit_array->orderBy('ptw_safety.id', 'DESC')->paginate($request->input('per_page', 10));

            $safety_permit_list = $safety_permit_array->toArray();

            if (empty($safety_permit_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }

            $data_array = [];
            foreach ($safety_permit_list['data'] as $listdata) {
                $data = [];
                $data['id'] = $listdata['id'] ?? '';
                $data['Work Permit No'] = $listdata['permit_id'] ?? '';
                $data['Unit'] = $listdata['unit_id'] ?? '';
                $data['Date'] = $listdata['date'] ?? '';
                $data['Exact Job Location'] = $listdata['exact_location_job'] ?? '';
                $data['Approve Status'] = $listdata['status_name'] ?? '';
                $data['Approved By'] = getUsername($listdata['approved_by'] ?? '');
                $data['Verified By'] = getUsername($listdata['verified_by'] ?? '');
                $data['Created By'] = getUsername($listdata['created_by'] ?? '');
                $data['Created Date'] = Displaydateformat($listdata['created_at'] ?? '');

                $data_array[] = $data;
            }

            $safety_permit_details = [
                'per_page' => $safety_permit_list['per_page'] ?? 0,
                'current_page' => $safety_permit_list['current_page'] ?? 0,
                'from' => $safety_permit_list['from'] ?? 0,
                'to' => $safety_permit_list['to'] ?? 0,
                'total' => $safety_permit_list['total'] ?? 0,
                'total_page' => $safety_permit_list['last_page'] ?? 0,
                'list' => $data_array,
            ];


            $success = [
                'safety_permit_details' => $safety_permit_details
            ];


            return $this->sendResponse($success, 'Safety Permit Details');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    // view

    public function view(Request $request)
    {
        try {
            if (Auth::user()) {
                $id = $request->id;

                $safetypermit = $this->safetypermit->selectOne($id);
                $workmaninvolved = $this->safetypermit->workmaninvolved($id);
                $stateIsolationLoto = json_decode($safetypermit->state_isolation_loto);
                $confined_space_entry = json_decode($safetypermit->confined_space_entry);

                $status_log = $this->statuslog->selectOne($id);

                $getEhSverification =   $this->approvereject->getEhSverification($id);
                $getEhsapproval =   $this->approvereject->getEhsapproval($id);
                $getplantheadapproval =   $this->approvereject->getplantheadapproval($id);
                $getsafetyPermitExtension =   $this->safetyPermitExtension->permitextensionelectOne($id);
                $getpermitextensionapproval =   $this->approvereject->getpermitextensionapproval($id);




                $success = [

                    'id' => $safetypermit->id,
                    'Work Permit No' => $safetypermit->permit_id,
                    'Date' => $safetypermit->date,
                    'Time (From)' =>$safetypermit->time_from,
                    'Time (To)' =>$safetypermit->time_to,
                    'Unit' => getUnitname($safetypermit->unit_id),
                    'Exact location of job' => $safetypermit->exact_location_job,
                    'Job Location & Area' => $safetypermit->job_location_area,
                    'Created By' => getusername($safetypermit->created_by),
                    'Created At' => Displaydateformat($safetypermit->created_at),
                    'Sub Permit' => ($safetypermit->sub_permit),


                ];

                return $this->sendResponse($success, 'Safety Permit Details');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {
            dd($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
