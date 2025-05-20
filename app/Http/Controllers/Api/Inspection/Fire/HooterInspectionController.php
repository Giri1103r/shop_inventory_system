<?php

namespace App\Http\Controllers\Api\Inspection\Fire;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\BaseController;
use Spatie\IcalendarGenerator\Enums\Display;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\Fire\HooterInspection;
use App\Models\Inspection\Fire\HooterInspectionDetails;

class HooterInspectionController extends BaseController
{
    private $hooter;
    private $hooter_details;
    private $statusLog;


    public function __construct()
    {
        $this->hooter = new HooterInspection();
        $this->hooter_details = new HooterInspectionDetails();
        $this->statusLog = new FireStatusLog();
    }

    public function List(Request $request)
    {
        if (Auth::check()) {
            try {
                $data = $this->hooter->listApi();
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
        } catch (Exception $ex) {
            report($ex);
            return $this->sendError(
                'Unauthorised.',
                ['error' => 'Please try again after sometimes'],
                406
            );
        }
    }

    public function View(Request $request)
    {
        try {
            if (Auth::check()) {
                $id = $request->id;
                $inspection = $this->hooter->selectOne($id);
                $details = $this->hooter_details->GetDetails($inspection->id);
                $inspection_type = HOOTER_INSPECTION;

                // Hooter Main Section
                $inspection_main = [
                    'id' => $inspection->id,
                    'document_reference_id' => $inspection->document_reference_id,
                    'date_of_inspection' => Displaydateformat($inspection->date_of_inspection),
                    'location_name' => getLocationname($inspection->location),
                    'shift_name' => getShiftname($inspection->shift),
                    'next_due' => Displaydateformat($inspection->next_due),
                    'unit_name' => getUnitname($inspection->unit),
                    'frequency_name' => getFrequencyname($inspection->frequency),
                    'inspection_status' => GetStatusValue($inspection->inspection_status),
                    'remarks' => $inspection->remarks,
                    'created_at' => Displaydateformat($inspection->created_at),
                    'updated_at' => Displaydateformat($inspection->updated_at),
                    'observation' => $inspection->observation == 1 ? 'Yes' : 'No',
                    'inspection_status' => GetStatusValue($inspection->inspection_status),
                    'capa_recomendation' => $inspection->capa_recomendation,
                    'capa_remarks' => $inspection->capa_remarks,
                    'capa_ehs_remarks' => $inspection->capa_ehs_remarks,
                    'level_one_manager_remarks' => $inspection->level_one_manager_remarks,
                    'level_two_manager_remarks' => $inspection->level_two_manager_remarks,
                    'level_two_manager_remarks' => $inspection->level_two_manager_remarks,
                    'remarks' => $inspection->remarks,
                    'checked_by' => getUsername($inspection->checked_by),
                    'verified_by' => getUsername($inspection->verified_by),
                    'approved_by' => getUsername($inspection->verified_by),
                    'checked_by_id' => $inspection->checked_by,
                    'verified_by_id' => $inspection->verified_by,
                    'approved_by_id' => $inspection->approved_by,
                    'created_by_id' => $inspection->created_by,
                    'created_by' => getUsername($inspection->created_by),
                ];

                // Inspection Sub Data
                $inspection_details = [];

                foreach ($details as $index => $values) {
                    $inspection_details[$index + 1] = [
                        'id' => $values->id,
                        'sr_no' => $values->sr_no,
                        'department_name' => GetDeptName($values->department),
                        'blinking_light' => $values->blinking_light == 1 ? 'Yes' : 'No',
                        'connection' => $values->connection == 1 ? 'Yes' : 'No',
                        'audiobility' => $values->audiobility == 1 ? 'Yes' : 'No',
                        'resource_code' => $values->resource_code,
                        'quantity' => $values->quantity,
                        'condition_of_hooter' => $values->condition_of_hooter,
                        'remarks' => $values->remarks,
                    ];
                }

                // Status Wise Logs
                $ehs_officer_verification = [];
                $ehs_officer_approval = [];
                $capa_recommendation = [];
                $fire_associate_action = [];
                $capa_ehs_remarks = [];
                $level_one_remarks = [];
                $level_two_remarks = [];


                if (isset($inspection->verified_by)) {
                    $ehs_officer_signature = GetFireSignature($inspection->verified_by, $id, $inspection_type);
                    $ehs_officer_verification[] = [
                        'verified_by' => getUsername($inspection->verified_by),
                        'date' => Displaydateformat($inspection->created_at),
                        'verified_by_signature' => admin_url($ehs_officer_signature),
                    ];
                }

                if (isset($inspection->approved_by)) {
                    $ehs_approved_signature = GetFireSignature($inspection->approved_by, $id, $inspection_type);
                    $ehs_officer_approval[] = [
                        'approved_by' => getUsername($inspection->approved_by),
                        'date' => Displaydateformat($inspection->created_at),
                        'approved_by_signature' => admin_url($ehs_approved_signature),
                    ];
                }

                if (isset($inspection->capa_recomendation)) {
                    $capa_recommendation[] = [
                        'capa_recommendation' => $inspection->capa_recommendation,
                        'remarks' => $inspection->remarks,
                    ];
                }

                if (isset($inspection->capa_remarks)) {
                    $fire_associate_signature = GetFireSignature($inspection->created_by, $id, $inspection_type);
                    $fire_associate_action[] = [
                        'name' => getUsername($inspection->created_by),
                        'date' => Displaydateformat($inspection->created_at),
                        'signature' => admin_url($fire_associate_signature),
                        'capa_remarks' => $inspection->capa_remarks,
                    ];
                }

                if (isset($inspection->capa_ehs_remarks)) {
                    $ehs_capa_signature = GetFireSignature($inspection->verified_by, $id, $inspection_type);
                    $capa_ehs_remarks[] = [
                        'name' => getUsername($inspection->created_by),
                        'date' => Displaydateformat($inspection->created_at),
                        'ehs_capa_signature' => admin_url($ehs_capa_signature),
                        'capa_ehs_remarks' => $inspection->capa_ehs_remarks,
                    ];
                }

                if (isset($inspection->level_one_manager_remarks)) {
                    $level_one_signature = GetFireSignature($inspection->l1_manager_verified_by, $id, $inspection_type);
                    $level_one_remarks[] = [
                        'name' => getUsername($inspection->l1_manager_verified_by),
                        'date' => Displaydateformat($inspection->created_at),
                        'level_one_signature' => admin_url($level_one_signature),
                        'remarks' => $inspection->level_one_manager_remarks,
                    ];
                }

                if (isset($inspection->level_two_manager_remarks)) {
                    $level_two_signature = GetFireSignature($inspection->l2_manager_verified_by, $id, $inspection_type);
                    $level_two_remarks[] = [
                        'name' => getUsername($inspection->l2_manager_verified_by),
                        'date' => Displaydateformat($inspection->created_at),
                        'level_two_signature' => admin_url($level_two_signature),
                        'remarks' => $inspection->level_two_manager_remarks,
                    ];
                }



                // Approval Logs
                $status_logs = $this->statusLog->selectOne($id, $inspection_type);
                $logs = [];

                foreach ($status_logs as $log_index => $status) {
                    $logs[$log_index] = [
                        'id' => $status->id,
                        'type' => $status->type,
                        'inspection_id' => $status->inspection_id,
                        'from_status' => getInspectionStatus($status->from_status),
                        'to_status' => getInspectionStatus($status->to_status),
                        'remarks' => $status->remarks ?? 'N/A',
                        'approved_by' => $status->approved_by ? getUsername($status->approved_by) : '-',
                        'created_by' => $status->created_by ? getUsername($status->created_by) : '-',
                        'created_at' => Displaydateformat($status->created_at),
                    ];
                }





                $data = array(
                    'inspection_main' => $inspection_main,
                    'inspection_details' => $inspection_details,
                    'logs' => $logs,
                    'ehs_officer_verification' => $ehs_officer_verification,
                    'ehs_officer_approval' => $ehs_officer_approval,
                    'capa_recommendation' => $capa_recommendation,
                    'fire_associate_action' => $fire_associate_action,
                    'capa_ehs_remarks' => $capa_ehs_remarks,
                    'level_one_remarks' => $level_one_remarks,
                    'level_two_remarks' => $level_two_remarks,
                );

                return $this->sendResponse($data, 'Hooter Inspection Details');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
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
}
