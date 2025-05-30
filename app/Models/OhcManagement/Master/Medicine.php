<?php

namespace App\Models\OhcManagement\Master;

use App\Models\User;
use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Medicine extends Model
{
    use  HasFactory;


    protected $table = 'ohc_master_medicine';
    protected $primaryKey = 'id';

    protected $fillable = [
        'medicine',
        'pack',
        'hsn',
        'unit_id',
        'threshold_limit',
        'expiry_date',
        'remarks',
        'approve_status',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('ohc_master_medicine.*');

        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];
            $formattedDate = null;
            if (\DateTime::createFromFormat('d-m-Y', $search) !== false) {
                $formattedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
            }
            $query->where(function ($query) use ($search, $formattedDate) {
                $query
                    ->orWhere('medicine', 'LIKE', '%' . $search . '%')

                    ->orWhere('hsn', 'LIKE', '%' . $search . '%')
                    ->orWhere('threshold_limit', 'LIKE', '%' . $search . '%')
                    ->orWhere('remarks', 'LIKE', '%' . $search . '%')

                    ->orWhere('pack', 'LIKE', '%' . $search . '%');

                if ($formattedDate) {
                    $query->orWhere('expiry_date', 'LIKE', '%' . $formattedDate . '%');
                }
            });
        }


        if ($request->has('medicine') && $request->medicine) {
            $query = $query->where('medicine', 'LIKE', '%' . $request->medicine . '%');
        }
        if ($request->has('expire_date') && $request->expire_date) {
            $query = $query->where('ohc_master_medicine.expiry_date', 'LIKE', '%' . DBdateformat($request->expire_date) . '%');
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_master_medicine.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_medicine.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_medicine.created_at', '<=', $endDate);
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_master_medicine.status', decryptId($request->status));
        }
        if ($request->has('approve_status') && $request->approve_status) {

            $query = $query->where('ohc_master_medicine.approve_status', decryptId($request->approve_status));
        }
        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('id', 'DESC');

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

    public function listApi()
    {
        $request = request();
                $perPage = $request->input('per_page', 10);

        $search = '';
        $query = $this->select('ohc_master_medicine.*')->where('status', 1);

        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search != null || $request->search != '') {
            $search = $request->search;
            $formattedDate = null;
            if (\DateTime::createFromFormat('d-m-Y', $search) !== false) {
                $formattedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
            }
            $query->where(function ($query) use ($search, $formattedDate) {
                $query
                    ->orWhere('medicine', 'LIKE', '%' . $search . '%')

                    ->orWhere('hsn', 'LIKE', '%' . $search . '%')
                    ->orWhere('threshold_limit', 'LIKE', '%' . $search . '%')
                    ->orWhere('remarks', 'LIKE', '%' . $search . '%')

                    ->orWhere('pack', 'LIKE', '%' . $search . '%');

                if ($formattedDate) {
                    $query->orWhere('expiry_date', 'LIKE', '%' . $formattedDate . '%');
                }
            });
        }

        $paginatedData = $query->orderBy('ohc_master_medicine.id')->paginate($perPage);

        $medicine_data = $paginatedData->toArray();

        $medicine_data_array = [];
        $refined_data = [];

        foreach ($medicine_data['data'] as $index => $data) {
            $medicine_data_array['id'] = $data['id'];
            $medicine_data_array['medicine_name'] = $data['medicine'];

            $refined_data[$index] = $medicine_data_array;
        }

         $response = [
            'per_page' => $paginatedData->perPage(),
            'current_page' => $paginatedData->currentPage(),
            'from' => $paginatedData->firstItem(),
            'to' => $paginatedData->lastItem(),
            'total' => $paginatedData->total(),
            'total_page' => $paginatedData->lastPage(),
            'list' => $refined_data,
        ];

        return $response;
    }

    public function uniqueCheck($medicine_name)
    {

        return $this->where('medicine', $medicine_name)->get();
    }

    public function existUniqueCheck($medicine_name, $id)
    {
        return $this->where('medicine', $medicine_name)
            ->where('id', '!=', $id)
            ->get();
    }
    public function HsnuniqueCheck($hsn)
    {

        return $this->where('hsn',  $hsn)->get();
    }


    public function existHsnUniqueCheck($hsn, $id)
    {
        return $this->where('hsn',  $hsn)
            ->where('id', '!=', $id)
            ->get();
    }
    public function store()
    {
        $request = request();


        $user = User::where('status', 1)->where('id', Auth::id())->first();


        $approveStatus = ($user && (checkUserRole(ROLE_SUPERADMIN) ||  checkUserRole(ROLE_EHS_HEAD)))
            ? STATUS_OHC_EHS_HEAD_APPROVED
            : STATUS_OHC_EHS_HEAD_APPROVAL_PENDING;
        $Status = ($user && (checkUserRole(ROLE_SUPERADMIN) ||  checkUserRole(ROLE_EHS_HEAD)))
            ? 1
            : 0;

        $insert_array = [
            'medicine' => $request->medicine,
            'pack' => $request->pack,
            'threshold_limit' => $request->threshold_limit,
            'remarks' => $request->remarks,
            'approve_status' => $approveStatus,
            'created_by' => Auth::id(),
            'status' =>  $Status,
        ];

        return $this->create($insert_array);
    }


    public function updates($id)
    {

        $request = request();
        $user = User::where('status', 1)->first();
        $approveStatus = ($user && (checkUserRole(ROLE_SUPERADMIN) ||  checkUserRole(ROLE_EHS_HEAD)))
            ? STATUS_OHC_EHS_HEAD_APPROVED
            : STATUS_OHC_EHS_HEAD_APPROVAL_PENDING;
        $Status = ($user && (checkUserRole(ROLE_SUPERADMIN) ||  checkUserRole(ROLE_EHS_HEAD)))
            ? 1
            : 0;
        $update_array = array(
            'medicine' => $request->medicine,
            'pack' => $request->pack,
            'threshold_limit' => $request->threshold_limit,
            'remarks' => $request->remarks,
            'updated_by' => Auth::id(),
            'approve_status' =>   $approveStatus,
            'status' => $Status,
        );

        return $this->where('id', $id)->update($update_array);
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

        $update_data = array(
            'status' => 0,
            'trash' => 'YES',
        );

        return $this->where('id', $id)->update($update_data);
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('ohc_master_medicine.*');

        if (!empty($request->search) && isset($request->search['value']) && $request->search['value'] !== '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('medicine', 'LIKE', '%' . $search . '%')
                    ->orWhere('expiry_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('hsn', 'LIKE', '%' . $search . '%')
                    ->orWhere('threshold_limit', 'LIKE', '%' . $search . '%')
                    ->orWhere('pack', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('medicine') && $request->medicine) {
            $query->where('medicine', 'LIKE', '%' . $request->medicine . '%');
        }

        if ($request->has('expire_date') && $request->expire_date) {
            $query->where('ohc_master_medicine.expiry_date', 'LIKE', '%' . DBdateformat($request->expire_date) . '%');
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_master_medicine.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_medicine.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_medicine.created_at', '<=', $endDate);
        }
        if ($request->has('approve_status') && $request->approve_status) {

            $query = $query->where('ohc_master_medicine.approve_status', decryptId($request->approve_status));
        }

        if ($request->has('status') && $request->status) {
            $query->where('ohc_master_medicine.status', decryptId($request->status));
        }

        $query->orderBy('id', 'DESC');

        return $query->get();
    }


    public function selectOne($id)
    {

        $data = $this->select(
            'ohc_master_medicine.*'
        )
            ->where('ohc_master_medicine.id', $id)->where('trash', 'NO')
            ->first();

        return $data;
    }


    public function getMedicineData()
    {
        return $this->where('status', 1)->where('trash', 'no')->get();
    }
    public function hsnajaxData($medicineID)
    {
        return $this->where('id', $medicineID)->select('id', 'hsn', 'threshold_limit')->first();
    }

    public function ajaxList($unitId = '')
    {
        $query = $this->select('id', 'medicine', 'pack')->where('status', 1);


        if (!empty($unitId)) {
            $query = $query->where(function ($q) use ($unitId) {
                $q->where('unit_id', $unitId)->where('status', 1);
            });
        }
        $datas = $query->get();

        $list = [];
        foreach ($datas as $data) {
            $listvalue = [];
            $listvalue['id'] = encryptId($data->id);
            $listvalue['name'] = $data->medicine;
            $listvalue['pack'] = $data->pack;
            $list[] = $listvalue;
        }

        return $list;
    }

    public function stocklist($medicineid)
    {
        return $this->where('id', $medicineid)->where('status', 1)->first();
    }

    // stock update approval

    public function approval($id, $updateData)
    {
        $request = request();

        if ($request->action == 'approve') {
            return $this->where('id', $id)->update([
                'approve_status' => $updateData['approve_status'],
                'approver_name' => Auth::id(),
                'status' => 1
            ]);
        } elseif ($request->action == 'reject') {
            return $this->where('id', $id)->update([
                'trash' => 'YES',
                'approver_name' => Auth::id(),
                'approve_status' => $updateData['approve_status']
            ]);
        }
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ohc_master_medicine'));
    }
}
