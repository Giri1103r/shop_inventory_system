<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Master\Location;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Log;

use App\Models\User;

class Manufacturer extends Model
{
    use  HasFactory;


    protected $table = 'master_manufacture';
    protected $primaryKey = 'id';

    protected $fillable = [
        'manufacture_id',
        'manufacturer_name',
        'manufacturer_name',
        'license_number',
        'contact_person',
        'email',
        'mobile_no',
        'address',
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
        $query = $this->select('master_manufacture.*');
        $data_count = $query;
        $total_records = $data_count->count();

        if ($request->has('manufacture_id') && $request->manufacture_id) {
            $query = $query->where('master_manufacture.manufacture_id', 'LIKE', '%' . $request->manufacture_id . '%');
        }
        if ($request->has('manufacturer_name') && $request->manufacturer_name) {
            $query = $query->where('master_manufacture.manufacturer_name', 'LIKE', '%' . $request->manufacturer_name . '%');
        }

        if ($request->has('license_number') && $request->license_number) {
            $query = $query->where('master_manufacture.license_number', ($request->license_number));
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('master_manufacture.status', decryptId($request->status));
        }
        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }
        $query->orderBy('id', 'DESC');
        $org_total =  $query;
        $org_total_counts = $org_total->count();
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

        $create_array = [
            'manufacturer_name' => $request->manufacture_name,
            'license_number' => $request->license_number,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'mobile_no' => $request->mobile_no,
            'address' => $request->address,
            'created_by' => Auth::id(),
        ];

        $data = $this->create($create_array);
        return $data;
    }
    public function updates($id)
    {
        $request = request();

        $update_array = [
            'manufacturer_name' => $request->manufacture_name,
            'license_number' => $request->license_number,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'mobile_no' => $request->mobile_no,
            'address' => $request->address,
            'updated_by' => Auth::id(),
        ];

        $data = $this->where('id', $id)->update($update_array);
        return $data;
    }
    public function selectOne($id)
    {
        $data = $this->where('id', $id)->first();
        return $data;
    }

    public function getalldata()
    {
        $data = $this->where('status', 1)->where('trash', 'No')->get();

        return $data;
    }

    public function UniqueCheck($manufacture_name, $l_no , $email)
    {
        return $this->where(function ($q) use ($manufacture_name, $l_no,$email) {
            $q->where('manufacturer_name', $manufacture_name)
                ->orWhere('license_number', $l_no)
                ->orWhere('email', $email);
        });
    }
    public function ExistuniqueCheck($manufacture_name, $l_no, $email, $id)
    {
        return $this->where(function ($q) use ($manufacture_name, $l_no,$email) {
            $q->where('manufacturer_name', $manufacture_name)
                ->orWhere('license_number', $l_no)
                ->orWhere('email', $email);
        })
            ->where('id', '!=', $id);
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('master_manufacture.*');


        if ($request->has('manufacture_id') && $request->manufacture_id) {
            $query = $query->where('master_manufacture.manufacture_id', 'LIKE', '%' . $request->manufacture_id . '%');
        }
        if ($request->has('manufacturer_name') && $request->manufacturer_name) {
            $query = $query->where('master_manufacture.manufacturer_name', 'LIKE', '%' . $request->manufacturer_name . '%');
        }

        if ($request->has('license_number') && $request->license_number) {
            $query = $query->where('master_manufacture.license_number', ($request->license_number));
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('master_manufacture.status', decryptId($request->status));
        }
      
        $query->orderBy('id', 'DESC');

        return $query;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('master_manufacture'));
        static::created(function ($model) {
            $uniqueId =  'MAF -' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['manufacture_id' => $uniqueId]);
        });
    }
}
