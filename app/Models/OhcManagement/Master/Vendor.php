<?php

namespace App\Models\OhcManagement\Master;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Vendor extends Model
{
    use  HasFactory;


    protected $table = 'ohc_master_vendor';
    protected $primaryKey = 'id';

    protected $fillable = [
        'vendor_name',
        'address',
        'license_no',
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
        $query = $this->select('ohc_master_vendor.*');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('vendor_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('license_no', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('vendor_name') && $request->vendor_name) {
            $query = $query->where('vendor_name',  $request->vendor_name);
        }
        if ($request->has('licence_no') && $request->licence_no) {
            $query = $query->where('license_no', 'LIKE', $request->licence_no );
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '<=', $endDate);
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('status', decryptId($request->status));
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

    public function uniqueCheck($vendor_name,$license_no)
    {
        return $this->where('vendor_name', $vendor_name)
                    ->orWhere('license_no', $license_no)
                    ->exists();
    }

    public function existUniqueCheck($vendor_name, $license_no, $id)
    {
        return $this->where(function ($query) use ($vendor_name, $license_no) {
                        $query->where('vendor_name', $vendor_name)
                              ->orWhere('license_no', $license_no); // Fixed here
                    })
                    ->where('id', '!=', $id)
                    ->exists();
    }
    public function store()
    {
        $request = request();

        $insert_array = array(
            'license_no' => $request->license_no,
            'vendor_name' => $request->vendor_name,
            'address' => $request->address,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'license_no' => $request->license_no,
            'vendor_name' => $request->vendor_name,
            'address' => $request->address,
            'updated_by' => Auth::id()
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
        $query = $this->select('ohc_master_vendor.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhere('license_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('vendor_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('vendor_name') && $request->vendor_name) {
            $query = $query->where('vendor_name', 'LIKE', '%' . $request->vendor_name . '%');
        }
        if ($request->has('licence_no') && $request->licence_no) {
            $query = $query->where('license_no', 'LIKE', $request->licence_no );
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($request->has('from_date') && !empty($request->from_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '>=', $startDate);
        } elseif ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '<=', $endDate);
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('status', decryptId($request->status));
        }

        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'ohc_master_vendor.*'
        )
            ->where('ohc_master_vendor.id', $id)
            ->first();

        return $data;
    }


  public function getVendordata(){
    return $this->where('status',1)->get();
  }
}
