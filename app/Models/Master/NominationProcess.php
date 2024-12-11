<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NominationProcess extends Model
{
    use  HasFactory;


    protected $table = 'training_nomination_process';
    protected $primaryKey = 'id';

    protected $fillable = [
        'emp_id',
        'emp_name',
        'email',
        'department_id',
        'employee_type',
        'last_training_attended_on',
        'topic_id',
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
        $query = $this->select('training_nomination_process.*', 'masters_employee.emp_id','masters_department.department_name', 'training_masters_topic.topic_name');
        $query = $query->leftJoin('masters_employee', 'training_nomination_process.emp_id', '=', 'masters_employee.id');
        $query = $query->leftJoin('masters_department', 'training_nomination_process.department_id', '=', 'masters_department.id');
        $query = $query->leftJoin('training_masters_topic', 'training_nomination_process.topic_id', '=', 'training_masters_topic.id');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_employee.emp_id', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('email') && $request->email) {
            $query = $query->where('training_nomination_process.email', 'LIKE', '%' . $request->email . '%');
        }
        if ($request->has('topic_id') && $request->topic_id) {
            $query = $query->where('training_nomination_process.topic_id', decryptId($request->topic_id));
        }
        if ($request->has('emp_id') && $request->emp_id) {
            $query = $query->where('training_nomination_process.emp_id', decryptId($request->emp_id));
        }
        if ($request->has('emp_name') && $request->emp_name) {
            $query = $query->where('training_nomination_process.emp_name', decryptId($request->emp_name));
        }

        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('training_nomination_process.department_id', decryptId($request->department_id));
        }

        if ($request->has('status') && $request->status) {
            $query = $query->where('training_nomination_process.status', 'LIKE', '%' . decryptId($request->status) . '%');
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

    public function store()
    {
        $request = request();

        $insert_array = array(
            'emp_id' => decryptId($request->emp_id),
            'emp_name' => $request->emp_name,
            'email' => $request->email,
            'department_id' => decryptId($request->department_id),
            'employee_type' => $request->employee_type,
            'last_training_attended_on' => DBdateformat($request->last_training_attended_on),
            'topic_id' => decryptId($request->topic_id),
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'emp_id' => decryptId($request->emp_id),
            'emp_name' => $request->emp_name,
            'email' => $request->email,
            'department_id' => decryptId($request->department_id),
            'employee_type' => $request->employee_type,
            'last_training_attended_on' => DBdateformat($request->last_training_attended_on),
            'topic_id' => decryptId($request->topic_id),
            'updated_by' => Auth::id(),
            'updated_at' => now(),
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

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('training_nomination_process.*', 'masters_employee.emp_id', 'masters_employee.emp_name', 'masters_department.department_name', 'training_masters_topic.topic_name');
        $query = $query->leftJoin('masters_employee', 'training_nomination_process.emp_id', '=', 'masters_employee.id');
        $query = $query->leftJoin('masters_department', 'training_nomination_process.department_id', '=', 'masters_department.id');
        $query = $query->leftJoin('training_masters_topic', 'training_nomination_process.topic_id', '=', 'training_masters_topic.id');
        if (!empty($request->search)) {
            $query->where(function ($subQuery) use ($request) {
                $search = $request->search;
                $subQuery
                    ->orWhere('masters_employee.emp_name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('email') && $request->email) {
            $query = $query->where('training_nomination_process.email', 'LIKE', '%' . $request->email . '%');
        }
        if ($request->has('topic_id') && $request->topic_id) {
            $query = $query->where('training_nomination_process.topic_id', decryptId($request->topic_id));
        }
        if ($request->has('emp_id') && $request->emp_id) {
            $query = $query->where('training_nomination_process.emp_id', decryptId($request->emp_id));
        }
        if ($request->has('emp_name') && $request->emp_name) {
            $query = $query->where('training_nomination_process.emp_name', decryptId($request->emp_name));
        }

        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('training_nomination_process.department_id', decryptId($request->department_id));
        }

        if ($request->has('status') && $request->status) {
            $query = $query->where('training_nomination_process.status', 'LIKE', '%' . decryptId($request->status) . '%');
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('training_nomination_process'));
    }
}
