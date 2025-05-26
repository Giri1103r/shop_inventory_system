<?php

namespace App\Http\Controllers\Api\Inspection\Audit;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\Unit;
use App\Models\Master\Department;
use App\Models\Inspection\audit\AuditAnalysis;
use App\Models\Inspection\audit\AuditAnalysisChecklist;
use App\Models\Inspection\MSDSCheckList;
use Exception;
use App\Models\Inspection\InspectionStaticDocno;
use Illuminate\Support\Facades\Auth;

class AuditAnalysisController extends BaseController
{

    private $unit;
    private $department;
    private $auditAnalysis;
    private $auditAnalysisCheckList;
    private $static_docno;

    public function __construct()
    {
        $this->auditAnalysis = new AuditAnalysis();
        $this->auditAnalysisCheckList = new AuditAnalysisChecklist();
        $this->department = new Department();
        $this->unit = new Unit();
        $this->static_docno = new InspectionStaticDocno();
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

            $audit_analysis_array = AuditAnalysis::select('inspection_audit_analysis.*');

            $org_total =  $audit_analysis_array;
            $org_total_counts = $org_total->count();

            if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
                $search = $request->search['value'];
                $audit_analysis_array->where(function ($query) use ($search) {
                    $query->orWhereRaw('audit_analysis_id LIKE "%' . $search . '%"')
                        ->orWhereRaw('audit_analysis LIKE "%' . $search . '%"');
                });
            }

            $audit_analysis_array = $audit_analysis_array->orderBy('inspection_audit_analysis.id', 'DESC')->paginate($request->input('per_page', 10));

            $audit_analysis_list = $audit_analysis_array->toArray();

            if (empty($audit_analysis_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }

            if (empty($audit_analysis_list['data'])) {
                return $this->sendError('No records found.', [], 404);
            }


            $data_array = [];
            foreach ($audit_analysis_list['data'] as $listdata) {
                $data = [];
                $data['id'] = $listdata['id'] ?? '';
                $data['audit_analysis_id'] = ($listdata['audit_analysis_id'] ?? '');
                $data['audit_analysis'] = ($listdata['audit_analysis'] ?? '');
                $data['status'] = $listdata['status'] == 1 ? 'Active' : 'In-Active';
                $data['created_by'] = getUsername($listdata['created_by'] ?? '');
                $data['created_at'] = Displaydateformat($listdata['created_at'] ?? '');



                $data_array[] = $data;
            }


            $audit_analysis_details = [
                'per_page' => $audit_analysis_list['per_page'] ?? 0,
                'current_page' => $audit_analysis_list['current_page'] ?? 0,
                'from' => $audit_analysis_list['from'] ?? 0,
                'to' => $audit_analysis_list['to'] ?? 0,
                'total' => $audit_analysis_list['total'] ?? 0,
                'total_page' => $audit_analysis_list['last_page'] ?? 0,
                'list' => $data_array,
            ];

            $success = [
                'audit_analysis_details' => $audit_analysis_details
            ];

            return $this->sendResponse($success, 'Audit Analysis Details');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
}
