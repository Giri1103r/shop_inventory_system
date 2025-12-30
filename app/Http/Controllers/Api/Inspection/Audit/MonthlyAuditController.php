<?php

namespace App\Http\Controllers\Api\Inspection\Audit;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Models\Inspection\audit\MonthlyAuditPlan;

class MonthlyAuditController extends BaseController
{
    private $monthly_audit_plan;

    public function __construct()
    {
        $this->monthly_audit_plan = new MonthlyAuditPlan();
    }

    public function list()
    {
        if (Auth::check()) {
            try {
                $data = $this->monthly_audit_plan->listApi();
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

    public function view(Request $request)
    {
        try {

            if (Auth::check()) {
                $id = $request->id;
                $monthly_audit_plan = $this->monthly_audit_plan->selectOne($id);

                $audit_details = [
                    'id' => $monthly_audit_plan->id,
                    'auditee_name' => $monthly_audit_plan->auditee_name,
                    'unit_name' => getUnitname($monthly_audit_plan->unit_id),
                    'task_name' => getTaskName($monthly_audit_plan->task_id),
                    'compliance_category' => getCategoryType($monthly_audit_plan->compliance_category_id),
                    'reference_doc_no' => $monthly_audit_plan->reference_doc_no,
                    'frequency_name' => getFrequencyName($monthly_audit_plan->frequency_id),
                    'audit_plan_status' => $monthly_audit_plan->audit_plan_status == 1 ? 'Yes' : 'No',
                    'direct_in_direct' => $monthly_audit_plan->direct_in_direct == 1 ? 'Direct' : 'Indirect',
                    'points' => $monthly_audit_plan->points,
                    'auditor_name' => $monthly_audit_plan->auditor_name,
                    'audit_date' =>Displaydateformat($monthly_audit_plan->audit_date),
                    'audit_time' => $monthly_audit_plan->audit_time,
                    'remarks' => $monthly_audit_plan->remarks,
                    'created_by' => getUsername($monthly_audit_plan->created_by),
                    'created_date' => Displaydateformat($monthly_audit_plan->created_at)
                ];
                $success = [
                    'id' => $monthly_audit_plan->id,
                    'data' => $audit_details
                ];

                return $this->sendResponse($success, 'Monthly Audit Plan Retrieved Successfully');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 404);
            }
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError(
                'Unauthorised.',
                ['error' => 'Please try again after sometimes'],
                406
            );
        }
    }

    public function store(Request $request)
    {
        try {

             $rules = [
                'monthly_audit.*.auditee_name' => 'required',
                'monthly_audit.*.unit_id' => 'required',
                'monthly_audit.*.task_name' => 'required',
                'monthly_audit.*.compliance_category' => 'required',
                'monthly_audit.*.reference_doc_no' => 'required',
                'monthly_audit.*.frequency_id' => 'required',
                'monthly_audit.*.direct_in_direct' => 'required',
                'monthly_audit.*.points' => 'required',
                'monthly_audit.*.remark' => 'required',
            ];

            $messages = [
                'monthly_audit.*.auditee_name.required' => 'Auditee name is required.',
                'monthly_audit.*.unit_id.required' => 'Unit ID is required.',
                'monthly_audit.*.task_name.required' => 'Task name is required.',
                'monthly_audit.*.compliance_category.required' => 'Compliance category is required.',
                'monthly_audit.*.reference_doc_no.required' => 'Reference document number is required.',
                'monthly_audit.*.frequency_id.required' => 'Frequency ID is required.',
                'monthly_audit.*.direct_in_direct.required' => 'Direct/Indirect field is required.',
                'monthly_audit.*.points.required' => 'Points field is required.',
                'monthly_audit.*.remark.required' => 'Remark must required',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
               return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            $data = $this->monthly_audit_plan->storeApi();

             $success = [
                "success" => $data,
            ];
            return $this->sendResponse($success, 'Monthly Audit Plan Created');
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError(
                'Unauthorised.',
                ['error' => 'Please try again after sometimes'],
                406
            );
        }
    }
}
