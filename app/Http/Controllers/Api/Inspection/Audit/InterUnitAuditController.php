<?php

namespace App\Http\Controllers\Api\Inspection\Audit;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inspection\audit\InterUnitAudit;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\ChecklistSubType;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Master\ChecklistOptionType;
use App\Models\Inspection\Master\Shift;
use App\Models\Master\Unit;
use Exception;
use Illuminate\Support\Facades\Auth;

class InterUnitAuditController extends BaseController
{
    private $checklist_type;
    private $checklist_subtype;
    private $checklist_subtypedata;
    private $checklist_subtypename;
    private $inter_unit_audit;
    private $upload_log;
    private $checklist_option;
    private $shift;
    private $unit;

    public function __construct()
    {
        $this->inter_unit_audit = new InterUnitAudit();
        $this->checklist_type = new ChecklistType();
        $this->checklist_subtype = new ChecklistSubType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_option = new ChecklistOptionType();
        $this->checklist_subtypename = new ChecklistSubTypeDataName();
        $this->checklist_subtypedata = new ChecklistSubTypeData();
        $this->shift = new Shift();
        $this->unit = new Unit();
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

            $audit_inter_unit_array = InterUnitAudit::select('inspection_audit_inter_unit.*', 'masters_unit.unit_name')->leftjoin('masters_unit', 'masters_unit.id', '=', 'inspection_audit_inter_unit.unit_id');
            $org_total =  $audit_inter_unit_array;
            $org_total_counts = $org_total->count();

            if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
                $search = $request->search['value'];
                $query = $audit_inter_unit_array->where(function ($query) use ($search) {
                    $query->orWhereRaw('audit_id LIKE "%' . $search . '%"');
                    $query->orWhereRaw('safety_officer LIKE "%' . $search . '%"');
                    // $query->orWhereRaw('audit_date LIKE "%' . $search . '%"');
                    $query->orWhereRaw("DATE_FORMAT(inspection_audit_inter_unit.audit_date, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
                    $query->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"');
                });
            }




            $audit_inter_unit_array = $audit_inter_unit_array->orderBy('inspection_audit_inter_unit.id', 'DESC')->paginate($request->input('per_page', 10));

            $audit_inter_unit_list = $audit_inter_unit_array->toArray();

            if (empty($audit_inter_unit_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }

            if (empty($audit_inter_unit_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }


            $data_array = [];
            foreach ($audit_inter_unit_list['data'] as $listdata) {
                $data = [];
                $data['id'] = $listdata['id'] ?? '';
                $data['audit_id'] = ($listdata['audit_id'] ?? '');
                $data['audit_date'] = Displaydateformat($listdata['audit_date'] ?? '');
                $data['safety_officer'] = ($listdata['safety_officer'] ?? '');
                $data['unit_id'] = ($listdata['unit_name'] ?? '');
                $data['status'] = $listdata['status'] == 1 ? 'Active' : 'In-Active';
                $data['created_by'] = getUsername($listdata['created_by'] ?? '');
                $data['created_at'] = Displaydateformat($listdata['created_at'] ?? '');



                $data_array[] = $data;
            }


            $audit_inter_unit_details = [
                'per_page' => $audit_inter_unit_list['per_page'] ?? 0,
                'current_page' => $audit_inter_unit_list['current_page'] ?? 0,
                'from' => $audit_inter_unit_list['from'] ?? 0,
                'to' => $audit_inter_unit_list['to'] ?? 0,
                'total' => $audit_inter_unit_list['total'] ?? 0,
                'total_page' => $audit_inter_unit_list['last_page'] ?? 0,
                'list' => $data_array,
            ];

            $success = [
                'audit_inter_unit_details' => $audit_inter_unit_details
            ];

            return $this->sendResponse($success, 'Audit Inter Unit Details');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    public function view(Request $request)
    {
        try {
            if (Auth::user()) {
                $id = $request->id;
                $inter_unit_audit = $this->inter_unit_audit->selectOne($id);

                $inter_unit_audit_checklist = json_decode($inter_unit_audit->checklist, true);

                $formattedChecklist = [];

                foreach ($inter_unit_audit_checklist as $subcategory => $questions) {
                    $subtypeName = GetSubChecklistTypeName($subcategory);

                    foreach ($questions as $questionId => $answer) {
                        $formattedChecklist[$subtypeName][] = [
                            'question' => GetChecklistTypeDate($questionId),
                            'checked' => $answer
                        ];
                    }
                }

                $audit_inter_unit_monthly = [
                    'audit_id' => $inter_unit_audit->audit_id,
                    'safety_officer' => $inter_unit_audit->safety_officer,
                    'audit_date' => Displaydateformat($inter_unit_audit->audit_date),
                    'unit_id' => getUnitname($inter_unit_audit->unit_id),

                ];

                // $success = [
                //     'audit_inter_unit_monthly' => $audit_inter_unit_monthly,

                // ];

                // return $this->sendResponse($success, 'Audit Inter Unit Monthly Details');
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
