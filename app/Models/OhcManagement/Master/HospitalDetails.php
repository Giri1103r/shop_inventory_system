<?php

namespace App\Models\OhcManagement\Master;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class HospitalDetails extends Model
{
    use  HasFactory;


    protected $table = 'ohc_master_hospital_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'hospital_name',
        'mobile_no',
        'address',
        'tel_no',
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

    public function list()
    {

        $request = request();
        $search = '';
        $query = $this->select('ohc_master_hospital_details.*');


        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if (!empty($request->search) && isset($request->search['value']) && $request->search['value'] !== '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('ohc_master_hospital_details.hospital_name', $search )
                    ->orWhere('ohc_master_hospital_details.address', $search)
                    ->orWhere('ohc_master_hospital_details.mobile_no',$search )
                    ->orWhere('ohc_master_hospital_details.tel_no',  $search );

            });
        }



        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_master_hospital_details.status', decryptId($request->status));
        }

        if ($request->has('hospital_name') && $request->hospital_name) {

            $query = $query->where('ohc_master_hospital_details.hospital_name', ($request->hospital_name));
        }

        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_master_hospital_details.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_hospital_details.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_hospital_details.created_at', '<=', $endDate);
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

    public function uniqueCheck($hospital_name,$mobile_no)
    {

        return $this->where('hospital_name', $hospital_name)->orwhere('mobile_no',$mobile_no)->get();
    }

    public function existUniqueCheck($hospital_name, $mobile_no, $id)
    {
        return $this->where(function ($query) use ($hospital_name, $mobile_no) {
                $query->where('hospital_name', $hospital_name)
                      ->orWhere('mobile_no', $mobile_no);
            })
            ->where('id', '!=', $id)
            ->get();
    }



    public function store()
    {
        $request = request();
        $insert_array = array(
            'hospital_name'         => ($request->hospital_name),
            'mobile_no'   => ($request->mobile_no),
            'tel_no'   => ($request->tel_no),
            'address'   => ($request->address),
            'created_by' => Auth::id(),

        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'hospital_name'  => ($request->hospital_name),
            'mobile_no' => ($request->mobile_no),
            'address' => ($request->address),
            'tel_no' => ($request->tel_no),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
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
        $query = $this->select('.*');
        $query = $this->select('ohc_master_hospital_details.*');


        if (!empty($request->search) && isset($request->search['value']) && $request->search['value'] !== '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('ohc_master_hospital_details.hospital_name', $search )
                    ->orWhere('ohc_master_hospital_details.address', $search)
                    ->orWhere('ohc_master_hospital_details.mobile_no',$search )
                    ->orWhere('ohc_master_hospital_details.tel_no',  $search );

            });
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('ohc_master_hospital_details.status', decryptId($request->status));
        }
        if ($request->has('hospital_name') && $request->hospital_name) {

            $query = $query->where('ohc_master_hospital_details.hospital_name', ($request->hospital_name));
        }

        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('ohc_master_hospital_details.created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_hospital_details.created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('ohc_master_hospital_details.created_at', '<=', $endDate);
        }

        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'ohc_master_hospital_details.*'
        )
            ->where('ohc_master_hospital_details.id', $id)
            ->first();

        return $data;
    }

    public function getHospitalname()
    {

        $data = $this->where('status', 1)

            ->get();

        return $data;
    }



}
