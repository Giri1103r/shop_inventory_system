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

            $audit_assessment_array = AuditAssessment::select('inspection_audit_assessment.*', 'masters_employee.emp_name')->leftjoin('masters_employee', 'masters_employee.id', '=', 'inspection_audit_assessment.floor_executive');

            $org_total =  $audit_assessment_array;
            $org_total_counts = $org_total->count();

            if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
                $search = $request->search['value'];
                $audit_assessment_array->where(function ($query) use ($search) {
                    $query->orWhereRaw('audit_id LIKE "%' . $search . '%"')
                        ->orWhereRaw('floor_name LIKE "%' . $search . '%"');
                });
            }

            $audit_assessment_array = $audit_assessment_array->orderBy('inspection_audit_assessment.id', 'DESC')->paginate($request->input('per_page', 10));

            $audit_assessment_list = $audit_assessment_array->toArray();

            if (empty($audit_assessment_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }

            if (empty($audit_assessment_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }


            $data_array = [];
            foreach ($audit_assessment_list['data'] as $listdata) {
                $data = [];
                $data['id'] = $listdata['id'] ?? '';
                $data['audit_id'] = ($listdata['audit_id'] ?? '');
                $data['audit_date'] = Displaydateformat($listdata['audit_date'] ?? '');
                $data['floor_name'] = ($listdata['floor_name'] ?? '');
                $data['floor_executive'] = getUsername($listdata['floor_executive'] ?? '');
                $data['status'] = $listdata['status'] == 1 ? 'Active' : 'In-Active';
                $data['created_by'] = getUsername($listdata['created_by'] ?? '');
                $data['created_at'] = Displaydateformat($listdata['created_at'] ?? '');



                $data_array[] = $data;
            }


            $audit_assessment_details = [
                'per_page' => $audit_assessment_list['per_page'] ?? 0,
                'current_page' => $audit_assessment_list['current_page'] ?? 0,
                'from' => $audit_assessment_list['from'] ?? 0,
                'to' => $audit_assessment_list['to'] ?? 0,
                'total' => $audit_assessment_list['total'] ?? 0,
                'total_page' => $audit_assessment_list['last_page'] ?? 0,
                'list' => $data_array,
            ];

            $success = [
                'audit_assessment_details' => $audit_assessment_details
            ];

            return $this->sendResponse($success, 'Audit Assessment Details ');
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


                ];

                return $this->sendResponse($success, 'Audit Assessment Details');
            }
        } catch (Exception $ex) {
            dd($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
