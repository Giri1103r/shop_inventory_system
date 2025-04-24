<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class FireAlarmInspection extends Model
{
    protected $table = 'inspection_fire_fire_alarm';

    protected $fillable = [
        'id',
        'document_reference_id',
        'date_of_inspection',
        'location',
        'shift',
        'next_due',
        'observation',
        'unit',
        'frequency',
        'checked_by',
        'verified_by',
        'approved_by',
        'description',
        'remarks',
        'inspection_status',
        'capa_recomendation',
        'capa_remarks',
        'level_one_manager_remarks',
        'level_two_manager_remarks',
        'capa_ehs_remarks',
        'l1_manager_verified_by',
        'l2_manager_verified_by',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_fire_fire_alarm.*', 'inspection_shift_option.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_fire_fire_alarm.id as inspection_id')
            ->leftJoin('masters_location', 'inspection_fire_fire_alarm.location', '=', 'masters_location.id')
            ->leftJoin('inspection_shift_option', 'inspection_fire_fire_alarm.shift', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'inspection_fire_fire_alarm.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_frequency_option', 'inspection_fire_fire_alarm.frequency', '=', 'inspection_frequency_option.id');


        $org_total =  $query;
        $org_total_counts = $org_total->count();



        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('masters_location.location_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_shift_option.shift LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_frequency_option.frequency_name LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_fire_fire_alarm.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_fire_fire_alarm.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_fire_fire_alarm.unit', 'LIKE', '%' . decryptId($request->unit) . '%');
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_fire_fire_alarm.shift', 'LIKE', '%' . decryptId($request->shift) . '%');
        }
        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_fire_fire_alarm.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_fire_fire_alarm.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->date_of_inspection) && $request->date_of_inspection) {
            $query = $query->where('inspection_fire_fire_alarm.date_of_inspection', 'LIKE', '%' . DBdateformat($request->date_of_inspection) . '%');
        }
        if (isset($request->next_due) && $request->next_due) {
            $query = $query->where('inspection_fire_fire_alarm.next_due', 'LIKE', '%' . DBdateformat($request->next_due) . '%');
        }


        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_fire_fire_alarm.inspection_status', decryptId($request->inspection_status));
        }

        if (isset($request->order) && count($request->order) > 0) {
            $columnName = $request->order[0]['column'];
            $columnorder = $request->order[0]['dir'];
            switch ($columnName) {
                case "inspection_status":
                    $query = $query->orderBy('inspection_fire_fire_alarm.inspection_status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('inspection_fire_fire_alarm.created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('inspection_fire_fire_alarm.created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('inspection_fire_fire_alarm.id', 'DESC');
                    break;
            }
        }

        $data_count = $query;
        $total_records = $data_count->count();

        if (isset($request->length) && $request->length != -1) {
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

    public function store()
    {
        $request = request();

        $data = array(
            'document_reference_id' => decryptId($request->document_reference_id),
            'date_of_inspection' => DBdateformat($request->inspection_date),
            'location' => decryptId($request->location_id),
            'shift' => decryptId($request->shift_id),
            'next_due' => DBdateformat($request->next_due),
            'observation' => $request->observation,
            'unit' => decryptId($request->unit_id),
            'frequency' => decryptId($request->frequency_id),
            'inspection_status' => WAITING_FOR_EHS_OFFICER_VERIFICATION,
            'created_by' => Auth::id(),
            'checked_by' => Auth::id(),
        );

        return $this->create($data);
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_fire_fire_alarm.*', 'inspection_shift_option.*', 'masters_unit.*', 'masters_location.*', 'inspection_frequency_option.*', 'inspection_fire_fire_alarm.id as fire_id','inspection_fire_fire_alarm.created_by as checked_by','inspection_static_docno.*','inspection_fire_fire_alarm_details.*')
            ->leftJoin('masters_location', 'inspection_fire_fire_alarm.location', '=', 'masters_location.id')
            ->leftJoin('inspection_shift_option', 'inspection_fire_fire_alarm.shift', '=', 'inspection_shift_option.id')
            ->leftJoin('masters_unit', 'inspection_fire_fire_alarm.unit', '=', 'masters_unit.id')
            ->leftJoin('inspection_static_docno', 'inspection_fire_fire_alarm.document_reference_id', '=', 'inspection_static_docno.id')
            ->leftJoin('inspection_frequency_option', 'inspection_fire_fire_alarm.frequency', '=', 'inspection_frequency_option.id')
            ->leftJoin('inspection_fire_fire_alarm_details', 'inspection_fire_fire_alarm.id', '=', 'inspection_fire_fire_alarm_details.inspection_id');

        if (isset($request->search) && isset($request->search['value']) && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query = $query->where(function ($query) use ($search) {
                $query->orWhereRaw('masters_location.location_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('masters_unit.unit_name LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_shift_option.shift LIKE "%' . $search . '%"');
                $query->orWhereRaw('inspection_frequency_option.frequency_name LIKE "%' . $search . '%"');
            });
        }

        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_fire_fire_alarm.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_fire_fire_alarm.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->unit) && $request->unit) {
            $query = $query->where('inspection_fire_fire_alarm.unit', 'LIKE', '%' . decryptId($request->unit) . '%');
        }
        if (isset($request->shift) && $request->shift) {
            $query = $query->where('inspection_fire_fire_alarm.shift', 'LIKE', '%' . decryptId($request->shift) . '%');
        }
        if (isset($request->location) && $request->location) {
            $query = $query->where('inspection_fire_fire_alarm.location', 'LIKE', '%' . decryptId($request->location) . '%');
        }
        if (isset($request->frequency) && $request->frequency) {
            $query = $query->where('inspection_fire_fire_alarm.frequency', 'LIKE', '%' . decryptId($request->frequency) . '%');
        }
        if (isset($request->date_of_inspection) && $request->date_of_inspection) {
            $query = $query->where('inspection_fire_fire_alarm.date_of_inspection', 'LIKE', '%' . DBdateformat($request->date_of_inspection) . '%');
        }
        if (isset($request->next_due) && $request->next_due) {
            $query = $query->where('inspection_fire_fire_alarm.next_due', 'LIKE', '%' . DBdateformat($request->next_due) . '%');
        }


        if (isset($request->inspection_status) && $request->inspection_status) {
            $query = $query->where('inspection_fire_fire_alarm.inspection_status', decryptId($request->inspection_status));
        }
        $query->orderBy('inspection_fire_fire_alarm.id', 'DESC');

        return  $query->get()->groupBy('inspection_id');
    }



    public function EHSOfficerUpdate($id)
    {

        $request = request();
        if ($request->is_passed == 1) {
            $update_array = [
                'verified_by' => Auth::id(),
                'approved_by' => Auth::id(),
                'inspection_status' => INSPECTION_APPROVED,
                'updated_by' => Auth::id(),
                'remarks' => $request->remarks,
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'verified_by' => Auth::id(),
                'inspection_status' => WAITING_FOR_CAPA_ACTION,
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
            'inspection_status' => WAITING_FOR_CAPA_VERIFICATION,
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
                'inspection_status' => WAITING_FOR_L1_VERIFICATION,
                'capa_ehs_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'inspection_status' => EHS_OFFICER_REJECTED,
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
                'inspection_status' => WAITING_FOR_L2_VERIFICATION,
                'level_one_manager_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'l1_manager_verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'inspection_status' => L1_MANAGER_REJECTED,
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
                'inspection_status' => INSPECTION_APPROVED,
                'level_two_manager_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        } else {
            $update_array = [
                'l2_manager_verified_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'inspection_status' => L2_MANAGER_REJECTED,
                'level_two_manager_remarks' => $remarks,
            ];
            $this->where('id', $id)->update($update_array);
        }
    }


    public function selectOne($id)
    {
        return $this->where('id', $id)->where('status', 1)->where('trash', 'NO')->first();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_fire_alarm'));
    }
}
