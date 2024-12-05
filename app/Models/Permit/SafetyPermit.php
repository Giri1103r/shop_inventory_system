<?php

namespace App\Models\Permit;

use App\Models\Master\Employee;
use App\Models\Master\ContractorCompanyUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Scopes\TrashScope;

class SafetyPermit extends Model
{
    use HasFactory;

    protected $table = 'ptw_safety';
    protected $primaryKey = 'id';

    protected $fillable = [
        'permit_id',
        'permit_type',
        'location',
        'sub_permit',
        'desc_work',
        'unit',
        'workers',
        'types_hotwork',
        'risk_assess_no',
        'flames_spark',
        'work_from_date',
        'work_from_time',
        'work_to_date',
        'work_to_time',
        'cil_cont_name',
        'company_name',
        'cil_cont_date',
        'cil_cont_time',
        'permit_extension',
        'permit_extended',
        'permit_extension_status',
        'permit_status',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    protected function casts(): array
    {
        return [
            'uauc_notification' => 'string',
        ];
    }


    public function list()
    {

        $request = request();

        $search = '';
        $id = Auth::id();

        // $employeelocation = Employee::where('login_id', $id)->value('location');

        $query = $this->select('ptw_safety.*');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('permit_id LIKE "%' . $search . '%"');
            });
        }

        // if ($request->has('permit_id') && $request->permit_id) {
        //     $query = $query->where('permit_id', 'LIKE', '%' . $request->permit_id . '%');
        // }
        // if ($request->has('loc_id') && $request->loc_id) {

        //     $loc_id = decryptId($request->loc_id);
        //     $query = $query->where('ptw_safety.location', 'LIKE', '%' . $loc_id . '%');
        // }

        // if ($request->has('sub_permit') && $request->sub_permit) {
        //     $query = $query->where('ptw_safety.sub_permit', 'LIKE', '%' . $request->sub_permit . '%');
        // }

        // if ($request->has('status') && $request->status) {

        //     $status = decryptId($request->status);
        //     $query = $query->where('ptw_safety.permit_status', 'LIKE', '%' . $status . '%');
        // }


        // if ($request->has('hot_status') && $request->hot_status) {
        //     $query = $query->where('ptw_safety.permit_status', $request->hot_status);
        // }
        // $sql = $query->toSql();
        // $bindings = $query->getBindings();

        // // Use vsprintf to replace the placeholders with the bindings
        // $fullSql = vsprintf(str_replace('?', '%s', $sql), array_map(function ($binding) {
        //     return is_numeric($binding) ? $binding : "'$binding'";
        // }, $bindings));

        // dd($fullSql);

        // if (isset($request->order)) {

        //     $columnName = $request->order[0]['name'];
        //     $columnorder = $request->order[0]['dir'];

        //     switch ($columnName) {

        //         case "permit_id":
        //             $query = $query->orderBy('permit_id', $columnorder);
        //             break;
        //         case "status":
        //             $query = $query->orderBy('status', $columnorder);
        //             break;
        //         case "created_by":
        //             $query = $query->orderBy('created_by', $columnorder);
        //             break;
        //         case "created_date":
        //             $query = $query->orderBy('created_at', $columnorder);
        //             break;
        //         default:
        //             $query = $query->orderBy('id', 'DESC');
        //             break;
        //     }
        // }

        $data_count = $query;
        $total_records = $data_count->count();


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


    public function permitextensionlist()
    {

        $request = request();
        $search = '';

        $query = $this->select('ptw_safety.*');

        $query = $query->leftJoin('hotcold_permit_extension', 'hotcold_permit_extension.permit_id', '=', 'ptw_safety.id');
        $query = $query->where('ptw_safety.trash', '=', 'NO');
        $query = $query->groupBy('hotcold_permit_extension.permit_id');
        $query = $query->orderBy('hotcold_permit_extension.permit_id', 'desc')
            ->orderBy('ptw_safety.id', 'desc');


        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('permit_id LIKE "%' . $search . '%"');
            });
        }

        if ($request->has('sub_permit') && $request->sub_permit) {
            $query = $query->where('ptw_safety.sub_permit', 'LIKE', '%' . $request->sub_permit . '%');
        }


        // if (isset($request->order)) {

        //     $columnName = $request->order[0]['name'];
        //     $columnorder = $request->order[0]['dir'];

        //     switch ($columnName) {

        //         case "permit_id":
        //             $query = $query->orderBy('permit_id', $columnorder);
        //             break;
        //         case "created_by":
        //             $query = $query->orderBy('created_by', $columnorder);
        //             break;
        //         case "created_date":
        //             $query = $query->orderBy('created_at', $columnorder);
        //             break;
        //         default:
        //             $query = $query->orderBy('id', 'DESC');
        //             break;
        //     }
        // }

        $data_count = $query;
        $total_records = $data_count->count();


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

    public function store($ptw_status)
    {
        $request = request();
        $sub_permit = is_array($request->sub_permit) ? implode(',', $request->sub_permit) : $request->sub_permit;


        $insert_array = array(
            'permit_id' => getsequence('permit'),
            'permit_type' => $request->permit_type,
            'location' => $request->location,
            'sub_permit' => $sub_permit,
            'desc_work' => $request->desc_work,
            'unit' => $request->unit,
            'workers' => $request->workers,
            'types_hotwork' => $request->types_hotwork,
            'risk_assess_no' => $request->risk_assess_no,
            'flames_spark' => $request->flames_spark,
            'work_from_date' => DBdateformat($request->work_from_date),
            'work_from_time' => $request->work_from_time,
            'work_to_date' => DBdateformat($request->work_to_date),
            'work_to_time' => $request->work_to_time,
            'cil_cont_name' => $request->cil_cont_name,
            'company_name' => $request->company_name,
            'cil_cont_date' => DBdateformat($request->cil_cont_date),
            'cil_cont_time' => $request->cil_cont_time,
            'permit_status' => $ptw_status,
            'permit_extension' => null,
            'permit_extended' => null,
            'permit_extension_status' => null,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id, $ptw_status)
    {
        $request = request();

        $update_array = array(
            'permit_id' => $request->permit_id,
            'permit_type' => $request->permit_type,
            'location' => $request->location,
            'sub_permit' => $request->sub_permit,
            'desc_work' => $request->desc_work,
            'unit' => $request->unit,
            'workers' => $request->workers,
            'types_hotwork' => $request->types_hotwork,
            'risk_assess_no' => $request->risk_assess_no,
            'flames_spark' => $request->flames_spark,
            'work_from_date' => DBdateformat($request->work_from_date),
            'work_from_time' => $request->work_from_time,
            'work_to_date' => DBdateformat($request->work_to_date),
            'work_to_time' => $request->work_to_time,
            'cil_cont_name' => $request->cil_cont_name,
            'company_name' => $request->company_name,
            'cil_cont_date' => DBdateformat($request->cil_cont_date),
            'cil_cont_time' => $request->cil_cont_time,
            'permit_status' => $ptw_status,
            'permit_extension' => null,
            'permit_extended' => null,
            'permit_extension_status' => null,
            'updated_by' => Auth::id()
        );

        return $this->where('id', $id)->update($update_array);
    }

    public function permitstatus($ptw_status, $id)
    {

        $confined_status = [
            'permit_status' => $ptw_status,
        ];

        return $this->where('id', $id)->update($confined_status);
    }

    public function subpermitstatus($ptwID, $subpermitstatus)
    {

        $request = request();
        $update_data = array(
            'permit_status' => $subpermitstatus,
        );


        return $this->where('id', $ptwID)->update($update_data);
    }


    public function permitCompletion($id)
    {

        $extenstion = [
            'permit_extension' => 1,
        ];

        return $this->where('id', $id)->update($extenstion);
    }

    public function permit_extended($id)
    {
        $currentValue = $this->where('id', $id)->value('permit_extended');
        $newValue = $currentValue ? $currentValue + 1 : 1;

        $extenstion = [
            'permit_extended' => $newValue,
        ];

        return $this->where('id', $id)->update($extenstion);
    }

    public function permit_extended_status($id, $ptw_status)
    {

        if ($ptw_status == 14) {
            $extenstion_status = [
                'permit_extension_status' => 1,
            ];

            return $this->where('id', $id)->update($extenstion_status);
        } else {
            $extenstion_status = [
                'permit_extension_status' => 0,
            ];

            return $this->where('id', $id)->update($extenstion_status);
        }
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

    public function deleterecord($id)
    {
        $update_data = [
            'status' => 0,
            'trash' => 'YES',
        ];

        $main_result = $this->where('id', $id)->update($update_data);

        $confined_result = ConfinedPtw::where('permit_id', $id)->update($update_data);
        $lifting_result = LifitingPtw::where('permit_id', $id)->update($update_data);
        $wah_result = WahPtw::where('permit_id', $id)->update($update_data);

        return [
            'main_result' => $main_result,
            'confined_result' => $confined_result,
            'lifting_result' => $lifting_result,
            'wah_result' => $wah_result,
        ];
    }


    public function selectOne($id)
    {

        $data =  $this->select('ptw_safety.*', 'ptw_hot_cold_file.file_path',)->leftjoin('ptw_hot_cold_file', 'ptw_hot_cold_file.ptw_hot_cold_id', '=', 'ptw_safety.id')
            ->where('ptw_safety.id', $id)
            ->first();

        return $data;
    }



    public function permitapprovestatus($ptw_status, $id)
    {

        $permit_status = [
            'permit_status' => $ptw_status,
        ];

        return $this->where('id', $id)->update($permit_status);
    }
    public function statusCount($type, $params = [])
    {

        $query = $this;

        $id = Auth::id();

        $employeelocation = Employee::where('login_id', $id)->value('location');
        $contractor = ContractorCompanyUser::where('login_id', $id)->first(['login_id', 'id']);

        if ($id == 1) {
            $query = $this->select('ptw_safety.*', 'ptw_hot_cold_status.status_name', 'ptw_hot_cold_status.bg_color', 'master_operation_location_type.location_type_name');
            $query = $query->leftJoin('ptw_hot_cold_status', 'ptw_hot_cold_status.id', '=', 'ptw_safety.permit_status');

            $query = $query->leftJoin('master_operation_location_type', 'master_operation_location_type.id', '=', 'ptw_safety.location');
        } elseif (isset($contractor) && ($id == $contractor->login_id)) {
            $query = $this->select('ptw_safety.*', 'ptw_hot_cold_status.status_name', 'ptw_hot_cold_status.bg_color', 'master_operation_location_type.location_type_name');
            $query = $query->leftJoin('ptw_hot_cold_status', 'ptw_hot_cold_status.id', '=', 'ptw_safety.permit_status');

            $query = $query->leftJoin('master_operation_location_type', 'master_operation_location_type.id', '=', 'ptw_safety.location');
            $query = $query->where('ptw_safety.created_by', $contractor->login_id);
        } else {
            $query = $this->select('ptw_safety.*', 'ptw_hot_cold_status.status_name', 'ptw_hot_cold_status.bg_color', 'master_operation_location_type.location_type_name');
            $query = $query->leftJoin('ptw_hot_cold_status', 'ptw_hot_cold_status.id', '=', 'ptw_safety.permit_status');

            $query = $query->leftJoin('master_operation_location_type', 'master_operation_location_type.id', '=', 'ptw_safety.location');
            $query = $query->where('ptw_safety.location', $employeelocation);
        }


        if (isset($params['location_ids']) && $params['location_ids']) {
            $query = $query->whereIn('location', $params['location_ids']);
        }
        if (isset($params['from_date']) && isset($params['to_date'])) {
            $query = $query->whereBetween('work_from_date', [DBdateformat($params['from_date']), DBdateformat($params['to_date'])]);
        } elseif (isset($params['from_date'])) {
            $query = $query->where('work_from_date', '>=', DBdateformat($params['from_date']));
        } elseif (isset($params['to_date'])) {
            $query = $query->where('work_to_date', '<=', DBdateformat($params['to_date']));
        }

        switch ($type) {
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 13:
            case 6:
            case 7:
            case 11:
            case 14:
            case 16:
            case 15:
            case 17:
            case 8:
            case 12:
            case 9:
                $query = $query->where('permit_status', $type);

                break;
        }

        $count = $query->count();

        return $count;
    }
    public function exportdata()
    {
        $request = request();
        $search = '';

        $id = Auth::id();

        $employeelocation = Employee::where('login_id', $id)->value('location');
        $contractor = ContractorCompanyUser::where('login_id', $id)->first(['login_id', 'id']);

        // dd($contractor);

        if ($id == 1) {
            $query = $this->select('ptw_safety.*', 'ptw_hot_cold_status.status_name', 'ptw_hot_cold_status.bg_color', 'master_operation_location_type.location_type_name');
            $query = $query->leftJoin('ptw_hot_cold_status', 'ptw_hot_cold_status.id', '=', 'ptw_safety.permit_status');

            $query = $query->leftJoin('master_operation_location_type', 'master_operation_location_type.id', '=', 'ptw_safety.location');
        } elseif (isset($contractor) && ($id == $contractor->login_id)) {
            $query = $this->select('ptw_safety.*', 'ptw_hot_cold_status.status_name', 'ptw_hot_cold_status.bg_color', 'master_operation_location_type.location_type_name');
            $query = $query->leftJoin('ptw_hot_cold_status', 'ptw_hot_cold_status.id', '=', 'ptw_safety.permit_status');

            $query = $query->leftJoin('master_operation_location_type', 'master_operation_location_type.id', '=', 'ptw_safety.location');
            $query = $query->where('ptw_safety.created_by', $contractor->login_id);
        } else {
            $query = $this->select('ptw_safety.*', 'ptw_hot_cold_status.status_name', 'ptw_hot_cold_status.bg_color', 'master_operation_location_type.location_type_name');
            $query = $query->leftJoin('ptw_hot_cold_status', 'ptw_hot_cold_status.id', '=', 'ptw_safety.permit_status');

            $query = $query->leftJoin('master_operation_location_type', 'master_operation_location_type.id', '=', 'ptw_safety.location');
            $query = $query->where('ptw_safety.location', $employeelocation);
        }


        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('permit_id LIKE "%' . $search . '%"');
            });
        }

        if ($request->has('permit_id') && $request->permit_id) {
            $query = $query->where('permit_id', 'LIKE', '%' . $request->permit_id . '%');
        }
        if ($request->has('sub_permit') && $request->sub_permit) {

            $sub_permit = $request->sub_permit;
            $query = $query->where('ptw_safety.sub_permit', 'LIKE', '%' . $sub_permit . '%');
        }

        if ($request->has('status') && $request->status) {

            $status = decryptId($request->status);
            $query = $query->where('ptw_safety.permit_status', 'LIKE', '%' . $status . '%');
        }
        if ($request->has('loc_id') && $request->loc_id) {

            $loc_id = decryptId($request->loc_id);
            $query = $query->where('ptw_safety.location', 'LIKE', '%' . $loc_id . '%');
        }

        if (isset($request->order)) {

            $columnName = $request->order[0]['name'];
            $columnorder = $request->order[0]['dir'];

            switch ($columnName) {

                case "permit_id":
                    $query = $query->orderBy('permit_id', $columnorder);
                    break;
                case "status":
                    $query = $query->orderBy('status', $columnorder);
                    break;
                case "created_by":
                    $query = $query->orderBy('created_by', $columnorder);
                    break;
                case "created_date":
                    $query = $query->orderBy('created_at', $columnorder);
                    break;
                default:
                    $query = $query->orderBy('id', 'DESC');
                    break;
            }
        }


        return  $query->get();
    }

    public function uniqueCheck($data)
    {
        return $this->where('uauc_notification',  $data)->get();
    }

    public function existUniqueCheck($data, $id)
    {
        return $this->where('uauc_notification',  $data)
            ->where('id', '!=', $id)
            ->get();
    }

    public function ajaxList($companyId = '')
    {
        $query = $this->select('id', 'uauc_notification');

        if ($companyId != '') {

            $query = $query->where('company_id', $companyId);
        }

        $datas = $query->get();

        $list = [];
        foreach ($datas as $data) {
            $listvalue = [];
            $listvalue['id'] = encryptId($data->id);
            $listvalue['name'] = $data->uauc_notification;

            $list[] = $listvalue;
        }
        return $list;
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ptw_safety'));

        static::created(function ($model) {

            $uniqueId = 'UAUC-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['uauc_notification_id' => $uniqueId]);
        });
    }
}
