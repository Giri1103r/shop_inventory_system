<?php

namespace App\Models\Permit;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkmanInvolved extends Model
{
    use  HasFactory;


    protected $table = 'ptw_safety_workman_involved';
    protected $primaryKey = 'id';

    protected $fillable = [
        'permit_id',
        'emp_id',
        'workman_name',
        'workman_desig',
        'workman_dept',
        'nature_of_job',
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
        $query = $this->select(
            'ptw_safety_workman_involved.*');



        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('work_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%');
            });
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




    public function getempIds($permit_id){
        return WorkmanInvolved::where('permit_id', $permit_id)->pluck('emp_id')->toArray();
    }

    public function getworkmanDetails( $id){
        return WorkmanInvolved::where('permit_id',  $id)->get();
    }

    public function store($permit_id)
    {
        $request = request();


        $empIds = $request->input('emp_id');
        $workmanNames = $request->input('workman_name');
        $workmanDesigs = $request->input('workman_desig');
        $workmanDepts = $request->input('workman_dept');
        $natureOfJobs = $request->input('nature_of_job');

        foreach ($empIds as $index => $empId) {
            $insert_array = array(
                'permit_id' => $permit_id,
                'emp_id' => $empId,
                'workman_name' => $workmanNames[$index],
                'workman_desig' => $workmanDesigs[$index],
                'workman_dept' => $workmanDepts[$index],
                'nature_of_job' => $natureOfJobs[$index],
                'created_by' => Auth::id()
            );

            $this->insert($insert_array);
        }
    }

    // public function updates($id)
    // {

    //     $request = request();

    //     $update_array = array(
    //         'work_name' => $request->work_name,
    //         'description' => $request->description,
    //         'updated_by' => Auth::id()
    //     );

    //     return $this->where('id', $id)->update($update_array);
    // }

    // public function statuschange($id)
    // {
    //     $request = request();

    //     $type = $request->types;
    //     if ($type == 1) {
    //         $update_data = array(
    //             'status' => 0,
    //         );
    //     } else {
    //         $update_data = array(
    //             'status' => 1,
    //         );
    //     }

    //     return $this->where('id', $id)->update($update_data);
    // }

    // public function deleterecord($id)
    // {

    //     $update_data = array(
    //         'status' => 0,
    //         'trash' => 'YES',
    //     );

    //     return $this->where('id', $id)->update($update_data);
    // }

    // public function exportdata()
    // {
    //     $request = request();
    //     $search = '';
    //     $query = $this->select(
    //         'ptw_masters_typeofwork.*',
    //         'ptw_masters_typeofwork_upload.file_path',
    //         'ptw_masters_typeofwork.id as typeid'
    //     )
    //     ->leftJoin(
    //         'ptw_masters_typeofwork_upload',
    //         'ptw_masters_typeofwork_upload.typeofwork_id',
    //         '=',
    //         'ptw_masters_typeofwork.id'
    //     )
    //     ->where('ptw_masters_typeofwork_upload.trash', 'NO');
    //     if ($request->search != null || $request->search != '') {
    //         $search = $request->search;

    //         $query->where(function ($query) use ($search) {
    //             $query
    //                 ->orWhere('work_name', 'LIKE', '%' . $search . '%')
    //                 ->orWhere('description', 'LIKE', '%' . $search . '%');
    //         });
    //     }

    //     if ($request->has('work_name') && $request->work_name) {
    //         $query = $query->where('work_name', 'LIKE', '%' . $request->work_name . '%');
    //     }
    //     if ($request->has('status') && $request->status) {

    //         $query = $query->where('ptw_masters_typeofwork.status', decryptId($request->status));
    //     }
    //     $query->orderBy('typeid', 'DESC');
    //     return  $query->get();
    // }

    // public function selectOne($id)
    // {

    //     $data =  $this->select('ptw_masters_typeofwork.*', 'ptw_masters_typeofwork_upload.file_path',)->leftjoin('ptw_masters_typeofwork_upload', 'ptw_masters_typeofwork_upload.typeofwork_id', '=', 'ptw_masters_typeofwork.id')
    //         ->where('ptw_masters_typeofwork.id', $id)
    //         ->first();

    //     return $data;
    // }


    // public function gettypework()
    // {

    //     $data =  $this->select('ptw_masters_typeofwork.*','ptw_masters_typeofwork_upload.file_path')
    //     ->leftjoin('ptw_masters_typeofwork_upload', 'ptw_masters_typeofwork_upload.typeofwork_id', '=', 'ptw_masters_typeofwork.id')->where('ptw_masters_typeofwork.status',1)->where('ptw_masters_typeofwork_upload.status',1)
    //     ->get();
    //     return $data;
    // }


    // public function getprotectiveequip()
    // {

    //     $data =  $this->select('ptw_masters_typeofwork.*','ptw_masters_typeofwork_upload.file_path')
    //     ->leftjoin('ptw_masters_typeofwork_upload', 'ptw_masters_typeofwork_upload.typeofwork_id', '=', 'ptw_masters_typeofwork.id')->where('ptw_masters_typeofwork.status',1)->where('ptw_masters_typeofwork_upload.status',1)
    //     ->get();
    //     return $data;
    // }


    // protected static function booted()
    // {
    //     static::addGlobalScope(new TrashScope('ptw_masters_typeofwork'));

    //     // static::created(function ($model) {

    //     //     $uniqueId = 'CMP-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
    //     //     $model->update(['company_id' => $uniqueId]);
    //     // });
    // }
}
