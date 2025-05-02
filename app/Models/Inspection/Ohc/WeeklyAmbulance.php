<?php

namespace App\Models\Inspection\Ohc;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class WeeklyAmbulance extends Model
{
    protected $table = 'inspection_ohc_weekly_ambulance_inspection_checklist';

    protected $primaryKey = 'id';

    protected $fillable = [
        'checklist',
        'document_reference_id',
        'date_of_inspection',
        'location',
        'shift',
        'next_due',
        'unit',
        'frequency',
        'checked_by',
        'verified_by',
        'approved_by',
        'approve_status',
        'l1_manager_verified_by',
        'l2_manager_verified_by',
        'level_two_manager_remarks',
        'level_one_manager_remarks',
        'level_two_manager_remarks',
        'l1_manager_verified_by',
        'capa_remarks',
        'capa_recomendation',
        'capa_ehs_remarks',
        'remarks',
        'created_by',
        'updated_by',
        'status',
        'trash',
        'created_at',
        'updated_at'
    ];



    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

    public function list()
    {
        $request = request();
        $search = '';


        $query = $this->select('inspection_ohc_weekly_ambulance_inspection_checklist.*', 'inspection_shift_option.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_ohc_weekly_ambulance_inspection_checklist.id as inspection_id' ,'inspection_ohc_weekly_ambulance_inspection_checklist.created_by as inspection_created_by')
            ->leftJoin('masters_location', 'inspection_ohc_weekly_ambulance_inspection_checklist.location', '=', 'masters_location.id')
            ->leftJoin('inspection_shift_option', 'inspection_ohc_weekly_ambulance_inspection_checklist.shift', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'inspection_ohc_weekly_ambulance_inspection_checklist.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_frequency_option', 'inspection_ohc_weekly_ambulance_inspection_checklist.frequency', '=', 'inspection_frequency_option.id')
            ->leftJoin('inspection_static_docno', 'inspection_ohc_weekly_ambulance_inspection_checklist.document_reference_id', '=', 'inspection_static_docno.id');

        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhere('inspection_shift_option.shift', 'LIKE', '%' . $search . '%')
                      ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                      ->orWhere('masters_location.location_name', 'LIKE', '%' . $search . '%');
            });
        }
        if (isset($request->unit_id) && $request->unit_id) {
           
            $query = $query->where('inspection_ohc_weekly_ambulance_inspection_checklist.unit', decryptId($request->unit_id));
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_ohc_weekly_ambulance_inspection_checklist.shift', decryptId($request->shift));
        }
        if (isset($request->location_id) && $request->location_id) {
            $query = $query->where('inspection_ohc_weekly_ambulance_inspection_checklist.location', decryptId($request->location_id));
        }
        if (isset($request->status) && $request->status) {
            $query = $query->where('inspection_ohc_weekly_ambulance_inspection_checklist.approve_status', decryptId($request->status));
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "rev_date":
                    $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.revision_date', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.doc_no', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.id', 'DESC');
                    break;
            }
        }


        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.id', 'DESC');

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }

        $data = $query->get();

        $datas = array(
            'data' => $data,
            'total_records' => $org_total_counts,
            'filter_records' => $total_records,
        );
        return $datas;
    }

    public function store($responses)
    {

        $request = request();

        $insert_array = [
            'checklist' => json_encode($responses),
            'shift' => decryptId($request->shift),
            'location' => decryptId($request->location_id),
            'document_reference_id' => decryptId($request->document_reference_id),
            'unit' => decryptId($request->unit_id),
            'next_due' => DBdateformat($request->next_due_on),
            'date_of_inspection' => DBdateformat($request->date_of_inspection),
            'approve_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
            'created_by' => Auth::id(),
        ];

        return self::create($insert_array);
    }


    public function EHSOfficerUpdate($id)
    {

        $request = request();
        if ($request->is_passed == 1) {
            $update_array = [
                'verified_by' => Auth::id(),
                'approved_by' => Auth::id(),
                'approve_status' => INSPECTION_APPROVED,
                'updated_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'verified_by' => Auth::id(),
                'approve_status' => WAITING_FOR_CAPA_ACTION,
                'updated_by' => Auth::id(),
                'capa_recomendation' => $request->remarks,
            ];
            $this->where('id', $id)->update($update_array);
        }
    }

    public function capaSubmit($id)
    {
        $request = request();
        $update_array = [
            'capa_remarks' => $request->capa_remarks,
            'updated_by' => Auth::id(),
            'approve_status' => WAITING_FOR_CAPA_VERIFICATION,
        ];
        $this->where('id', $id)->update($update_array);
    }

    public function capaVerifySubmit($id, $status, $remarks)
    {
        $request = request();
        if ($status == 1) {
            $update_array = [
                'verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'approve_status' => WAITING_FOR_L1_VERIFICATION,
                'capa_ehs_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'approve_status' => EHS_OFFICER_REJECTED,
                'capa_ehs_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        }
    }

    public function levelOneManagerSubmit($id, $status, $remarks)
    {
        if ($status == 1) {
            $update_array = [
                'l1_manager_verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'approve_status' => WAITING_FOR_L2_VERIFICATION,
                'level_one_manager_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'l1_manager_verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'approve_status' => L1_MANAGER_REJECTED,
                'level_one_manager_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        }
    }

    public function levelTwoManagerSubmit($id, $status, $remarks)
    {
        if ($status == 1) {
            $update_array = [
                'l2_manager_verified_by' => Auth::id(),
                'approved_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'approve_status' => INSPECTION_APPROVED,
                'level_two_manager_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'l2_manager_verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'approve_status' => L2_MANAGER_REJECTED,
                'level_two_manager_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        }
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_ohc_weekly_ambulance_inspection_checklist.*', 'inspection_shift_option.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_ohc_weekly_ambulance_inspection_checklist.id as inspection_id' ,'inspection_ohc_weekly_ambulance_inspection_checklist.created_by as inspection_created_by',
        'inspection_ohc_weekly_ambulance_inspection_checklist.created_at as inspection_created_at')
        ->leftJoin('masters_location', 'inspection_ohc_weekly_ambulance_inspection_checklist.location', '=', 'masters_location.id')
        ->leftJoin('inspection_shift_option', 'inspection_ohc_weekly_ambulance_inspection_checklist.shift', '=', 'inspection_shift_option.id')
        ->leftJoin('masters_unit', 'inspection_ohc_weekly_ambulance_inspection_checklist.unit', '=', 'masters_unit.id')
        ->leftJoin('inspection_frequency_option', 'inspection_ohc_weekly_ambulance_inspection_checklist.frequency', '=', 'inspection_frequency_option.id')
        ->leftJoin('inspection_static_docno', 'inspection_ohc_weekly_ambulance_inspection_checklist.document_reference_id', '=', 'inspection_static_docno.id');

    // dd($query);
    $org_total =  $query;
    $org_total_counts = $org_total->count();

    if (isset($request->search['value']) && $request->search['value'] != '') {
        $search = $request->search['value'];
        $query = $query->where(function ($query) use ($search) {
            $query->orWhere('inspection_shift_option.shift', 'LIKE', '%' . $search . '%')
                  ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('masters_location.location_name', 'LIKE', '%' . $search . '%');
        });
    }
    if (isset($request->unit) && $request->unit) {
        $query = $query->where('inspection_ohc_weekly_ambulance_inspection_checklist.unit', decryptId($request->unit));
    }
    if (isset($request->shift) && $request->shift) {
        $query = $query->where('inspection_ohc_weekly_ambulance_inspection_checklist.shift', decryptId($request->shift));
    }
    if (isset($request->location_id) && $request->location_id) {
        $query = $query->where('inspection_ohc_weekly_ambulance_inspection_checklist.location', decryptId($request->location_id));
    }
    if (isset($request->status) && $request->status) {
        $query = $query->where('inspection_ohc_weekly_ambulance_inspection_checklist.approve_status', decryptId($request->status));
    }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "rev_date":
                    $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.revision_date', $columnorder);
                    break;
                case "issue_date":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.issue_date', $columnorder);
                    break;
                case "document_number":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.doc_no', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.id', 'DESC');
                    break;
            }
        }

        $query->orderBy('inspection_ohc_weekly_ambulance_inspection_checklist.id', 'DESC');

        return  $query->get();
    }
    public function statuschange($id)
    {
        $request = request();

        $type = $request->types;
        if ($type == 1) {
            $update_data = array(
                'status' => 0,
            );
        } else {
            $update_data = array(
                'status' => 1,
            );
        }

        return $this->where('id', $id)->update($update_data);
    }
    public function WeekambulanceSelectone($id)
    {
        return $this->where('id', $id)->first();
    }
}
