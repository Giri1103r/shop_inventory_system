<?php

namespace App\Models\IMS\Master;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HiraApproval extends Model
{
    use  HasFactory;


    protected $table = 'ims_master_hira_ehs_approval';
    protected $primaryKey = 'id';

    protected $fillable = [
        'hira_id',
        'reviewer_emp_id',
        'reviewer_name',
        'date',
        'remark',
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
    public function ehsapproval($id)
    {
        $request = request();

        $insert_array = array(
            'hira_id' =>  decryptId($id),
            'date' => DBdateformat($request->date),
            'reviewer_emp_id' => $request->reviewer_emp_id,
            'reviewer_name' => $request->reviewer_name,
            'remark' => $request->remark,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
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
        $query = $this->select('ims_master_hira.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('sr_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('services', 'LIKE', '%' . $search . '%')
                    ->orWhere('hazard_type', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('sr_no') && $request->sr_no) {
            $query = $query->where('sr_no',  $request->sr_no);
        }
        if ($request->has('services') && $request->services) {
            $query = $query->where('services', 'LIKE', $request->services);
        }
        if ($request->has('hazard_type') && $request->hazard_type) {
            $query = $query->where('hazard_type', 'LIKE', $request->hazard_type);
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
            'ims_master_hira.*'
        )
            ->where('ims_master_hira.id', $id)
            ->first();

        return $data;
    }




    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ims_master_hira'));

        static::created(function ($model) {

            $uniqueId = 'HIRA-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['sr_no' => $uniqueId]);
        });
    }
}
