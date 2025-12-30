<?php

namespace App\Http\Controllers\Api\Inspection\Audit;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Employee;
use App\Models\Inspection\audit\AuditAssessment;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\ChecklistSubType;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Master\ChecklistOptionType;
use App\Models\Inspection\Master\Shift;
use App\Models\Inspection\InspectionStaticDocno;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuditAssessmentController extends BaseController
{
    private $checklist_type;
    private $checklist_subtype;
    private $checklist_subtypedata;
    private $checklist_subtypename;
    private $audit_assessment;
    private $upload_log;
    private $checklist_option;
    private $shift;
    private $document_reference;


    public function __construct()
    {
        $this->audit_assessment = new AuditAssessment();
        $this->checklist_type = new ChecklistType();
        $this->checklist_subtype = new ChecklistSubType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_subtypename = new ChecklistSubTypeDataName();
        $this->checklist_subtypedata = new ChecklistSubTypeData();
        $this->shift = new Shift();
        $this->document_reference = new InspectionStaticDocno();
    }

    public function list()
    {
        if (Auth::user()) {
            $request = request();
            if ($request->has('search')) {
                if ($request->search != '' && $request->search != null) {
                    $search = $request->search;
                }
            }
            $query = AuditAssessment::select('inspection_audit_assessment.*', 'masters_employee.emp_name')->leftjoin('masters_employee', 'masters_employee.id', '=', 'inspection_audit_assessment.floor_executive');

            $org_total_counts = $query->count();

            if (!empty($search)) {
                $search = ($search);
                $query->where(function ($query) use ($search) {
                    $query->orWhere('inspection_audit_assessment.audit_date', $search)
                        ->orWhere('inspection_audit_assessment.floor_name', $search)
                        ->orWhere('inspection_audit_assessment.audit_id', $search);
                });
            }

            $query_array = $query->orderBy('inspection_audit_assessment.id', 'DESC')->paginate($request->input('per_page', 10));

            $audit_assessment = $query_array->toArray();

            if (empty($audit_assessment['data'])) {
                return $this->sendError('No records found.', [], 404);
            }


            $data_array = [];
            foreach ($audit_assessment['data'] as $datas) {
                $data = [];
                $data['id'] = $datas['id'] ?? '';
                $data['audit_id'] = ($datas['audit_id']);
                $data['audit_date'] = Displaydateformat($datas['audit_date'] ?? '');
                $data['floor_name'] = ($datas['floor_name'] ?? '');
                $data['floor_executive'] = getEmployeename($datas['floor_executive'] ?? '');
                $data['created_by'] = getUsername($datas['created_by'] ?? '');
                $data['created_at'] = Displaydateformat($datas['created_at'] ?? '');
                $data_array[] = $data;
            }

            $audit_assessment_details = [
                'per_page' => $inspection_list['per_page'] ?? 0,
                'current_page' => $inspection_list['current_page'] ?? 0,
                'from' => $inspection_list['from'] ?? 0,
                'to' => $inspection_list['to'] ?? 0,
                'total' => $inspection_list['total'] ?? 0,
                'total_page' => $inspection_list['last_page'] ?? 0,
                'list' => $data_array,
            ];

            $success = [
                'audit_assessment_details' => $audit_assessment_details
            ];

            return $this->sendResponse($success, 'Audit Assessment Details');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }




    public function view(Request $request)
    {
        try {
            if (Auth::user()) {
                $id = $request->id;

                $details = $this->audit_assessment->selectOne($id);


                $checklist =  json_decode($details->checklist, true);

                $formattedChecklist = [];

                foreach ($checklist as $subcategory => $questions) {
                    $subtypeName = GetSubChecklistTypeName($subcategory);

                    foreach ($questions as $questionId => $answer) {
                        $formattedChecklist[$subtypeName][] = [
                            'question' => GetChecklistTypeDate($questionId),
                            'checked' => $answer
                        ];
                    }
                }



                $success = [
                    'id' => $details->id,
                    'audit_id' => $details->audit_id,
                    'floor_name' => $details->floor_name,
                    'audit_date' => Displaydateformat($details->audit_date),
                    'shift_id' => getShift($details->shift_id),
                    'floor_executive' => getUsername($details->floor_executive),
                    'checklist' => $formattedChecklist,
                    'total_score' => $details->total_score,
                    'obtained_score' => $details->obtained_score,
                ];

                return $this->sendResponse($success, 'Audit Assessment Details');
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }


    public function store(Request $request)
    {


        try {

            if (Auth::user()) {

                $rules = [
                    'shift_id' => 'required',
                    'floor_name' => 'required',
                    'audit_date' => 'required',
                    'floor_executive' => 'required',



                ];
                $messages = [
                    'shift_id' => 'Shift is required',
                    'floor_name' => 'Floor name is required',
                    'audit_date' => 'Audit Date is required',
                    'floor_executive' => 'Floor Executive is required',


                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                if ($validator->fails()) {
                    return $this->sendError('Validation Error', $validator->errors(), 422);
                }


                $audit_assessment =  $this->audit_assessment->store_api();
                $success = [
                    'audit_assessment' => $audit_assessment,
                ];
                return $this->sendResponse($success, 'Audit Assessment Details Created Successfully');
            }
        } catch (Exception $ex) {
            dd($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
