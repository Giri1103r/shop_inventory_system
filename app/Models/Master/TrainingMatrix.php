<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingMatrix extends Model
{
    use  HasFactory;


    protected $table = 'training_matrix';
    protected $primaryKey = 'id';

    protected $fillable = [
        'topic_id',
        'trainer_id',
        'training_offered_for',
        'unit_id',
        'department_id',
        'mode_of_training',
        'training_evaluation',
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
        $query = $this->select('training_matrix.*', 'masters_unit.unit_name', 'masters_employee.emp_name', 'masters_department.department_name', 'training_masters_topic.topic_name');
        $query = $query->leftJoin('masters_unit', 'training_matrix.unit_id', '=', 'masters_unit.id');
        $query = $query->leftJoin('masters_employee', 'training_matrix.trainer_id', '=', 'masters_employee.id');
        $query = $query->leftJoin('masters_department', 'training_matrix.department_id', '=', 'masters_department.id');
        $query = $query->leftJoin('training_masters_topic', 'training_matrix.topic_id', '=', 'training_masters_topic.id');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_employee.emp_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('training_masters_topic.topic_name', 'LIKE', '%' . $search . '%');
            });
        }


        if ($request->has('topic_id') && $request->topic_id) {
            $query = $query->where('training_matrix.topic_id', decryptId($request->topic_id));
        }
        if ($request->has('trainer_id') && $request->trainer_id) {
            $query = $query->where('training_matrix.trainer_id', decryptId($request->trainer_id));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('training_matrix.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('training_matrix.department_id', decryptId($request->department_id));
        }
        if ($request->has('training_offered_for') && $request->training_offered_for) {
            $query = $query->where('training_offered_for', 'LIKE', '%' . $request->training_offered_for . '%');
        }
        if ($request->has('mode_of_training') && $request->mode_of_training) {
            $query = $query->where('mode_of_training', 'LIKE', '%' . $request->mode_of_training . '%');
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('training_matrix.status', 'LIKE', '%' . decryptId($request->status) . '%');
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

    public function UniqueCheck($data)
    {

        return $this->where($data['param'],  $data['value'])->get();
    }

    public function ExistuniqueCheck($data)
    {
        return $this->where($data['param'],  $data['value'])
            ->where('id', '!=', decryptId($data['id']))
            ->get();
    }

    public function getuique($trainerId, $topicId,$unitId,$departmentId) {

        if ($trainerId && $topicId) {
            return TrainingMatrix::where('topic_id', $topicId)
                ->where('trainer_id', '=', $trainerId)
                // ->where('unit_id',$unitId)
                // ->where('deaprtment_id',$departmentId)
                ->exists();
        }
        return false;
    }


    public function store()
    {
        $request = request();
        $decryptedDepartmentIds = array_map('decryptId', $request->department_id);

        $commaSeparatedDepartments = implode(',', $decryptedDepartmentIds);

        $insertArray = [
            'topic_id' => decryptId($request->topic_id),
            'trainer_id' => decryptId($request->trainer_id),
            'training_offered_for' => decryptId($request->training_offered_for),
            'unit_id' => decryptId($request->unit_id),
            'department_id' => $commaSeparatedDepartments,
            'mode_of_training' => decryptId($request->mode_of_training),
            'training_evaluation' => decryptId($request->training_evaluation),
            'created_by' => Auth::id(),
        ];
        return $this->create($insertArray);
    }

    public function updates($id)
    {

        $request = request();
        $decryptedDepartmentIds = array_map('decryptId', $request->department_id);
        $commaSeparatedDepartments = implode(',', $decryptedDepartmentIds);
        $update_array = array(
            'topic_id' => decryptId($request->topic_id),
            'trainer_id' => decryptId($request->trainer_id),
            'training_offered_for' => decryptId($request->training_offered_for),
            'unit_id' => decryptId($request->unit_id),
            'department_id' =>  $commaSeparatedDepartments,
            'mode_of_training' => decryptId($request->mode_of_training),
            'training_evaluation' => decryptId($request->training_evaluation),
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
        $query = $this->select('training_matrix.*', 'masters_unit.unit_name', 'masters_employee.emp_name', 'masters_department.department_name', 'training_masters_topic.topic_name');
        $query = $query->leftJoin('masters_unit', 'training_matrix.unit_id', '=', 'masters_unit.id');
        $query = $query->leftJoin('masters_employee', 'training_matrix.trainer_id', '=', 'masters_employee.id');
        $query = $query->leftJoin('masters_department', 'training_matrix.department_id', '=', 'masters_department.id');
        $query = $query->leftJoin('training_masters_topic', 'training_matrix.topic_id', '=', 'training_masters_topic.id');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_employee.emp_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('training_masters_topic.topic_name', 'LIKE', '%' . $search . '%');
            });
        }


        if ($request->has('topic_id') && $request->topic_id) {
            $query = $query->where('training_matrix.topic_id', decryptId($request->topic_id));
        }
        if ($request->has('trainer_id') && $request->trainer_id) {
            $query = $query->where('training_matrix.trainer_id', decryptId($request->trainer_id));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('training_matrix.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('training_matrix.department_id', decryptId($request->department_id));
        }
        if ($request->has('training_offered_for') && $request->training_offered_for) {
            $query = $query->where('training_offered_for', 'LIKE', '%' . $request->training_offered_for . '%');
        }
        if ($request->has('mode_of_training') && $request->mode_of_training) {
            $query = $query->where('mode_of_training', 'LIKE', '%' . $request->mode_of_training . '%');
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('training_matrix.status', 'LIKE', '%' . decryptId($request->status) . '%');
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select('training_matrix.*', 'masters_unit.unit_name', 'masters_employee.emp_name', 'masters_department.department_name', 'training_masters_topic.topic_name')->leftJoin('masters_unit', 'training_matrix.unit_id', '=', 'masters_unit.id')->leftJoin('training_masters_topic', 'training_matrix.topic_id', '=', 'training_masters_topic.id')->leftJoin('masters_department', 'training_matrix.department_id', '=', 'masters_department.id')->leftJoin('masters_employee', 'training_matrix.trainer_id', '=', 'masters_employee.id')
            ->where('training_matrix.id', $id)
            ->first();

        return $data;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('training_matrix'));
    }
}
