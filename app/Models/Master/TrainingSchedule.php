<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingSchedule extends Model
{
    use  HasFactory;


    protected $table = 'masters_training_schedule';
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
        $query = $this->select('masters_training_schedule.*', 'masters_unit.unit_name', 'masters_employee.emp_name', 'masters_department.department_name', 'masters_topic.topic_name');
        $query = $query->leftJoin('masters_unit', 'masters_training_schedule.unit_id', '=', 'masters_unit.id');
        $query = $query->leftJoin('masters_employee', 'masters_training_schedule.trainer_id', '=', 'masters_employee.id');
        $query = $query->leftJoin('masters_department', 'masters_training_schedule.department_id', '=', 'masters_department.id');
        $query = $query->leftJoin('masters_topic', 'masters_training_schedule.topic_id', '=', 'masters_topic.id');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('topic_name', 'LIKE', '%' . $search . '%');
            });
        }

      
        if ($request->has('topic_id') && $request->topic_id) {
            $query = $query->where('masters_training_schedule.topic_id', decryptId($request->topic_id));
        }
        if ($request->has('trainer_id') && $request->trainer_id) {
            $query = $query->where('masters_training_schedule.trainer_id', decryptId($request->trainer_id));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('masters_training_schedule.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('masters_training_schedule.department_id', decryptId($request->department_id));
        }
        if ($request->has('training_offered_for') && $request->training_offered_for) {
            $query = $query->where('training_offered_for', 'LIKE', '%' . $request->training_offered_for . '%');
        }
        if ($request->has('mode_of_training') && $request->mode_of_training) {
            $query = $query->where('mode_of_training', 'LIKE', '%' . $request->mode_of_training . '%');
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('status', 'LIKE', '%' . decryptId($request->status) . '%');
        }
        $data_count = $query->count();
        $total_records = $data_count;

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

    public function store()
    {
        $request = request();

        $insert_array = array(
            'topic_id' => decryptId($request->topic_id),
            'trainer_id' => decryptId($request->trainer_id),
            'training_offered_for' => decryptId($request->training_offered_for),
            'unit_id' => decryptId($request->unit_id),
            'department_id' => decryptId($request->department_id),
            'mode_of_training' => decryptId($request->mode_of_training),
            'training_evaluation' => decryptId($request->training_evaluation),
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'topic_id' => decryptId($request->topic_id),
            'trainer_id' => decryptId($request->trainer_id),
            'training_offered_for' => decryptId($request->training_offered_for),
            'unit_id' => decryptId($request->unit_id),
            'department_id' => decryptId($request->department_id),
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
        $query = $this->select('masters_training_schedule.*', 'masters_unit.unit_name', 'masters_employee.emp_name', 'masters_department.department_name', 'masters_topic.topic_name');
        $query = $query->leftJoin('masters_unit', 'masters_training_schedule.unit_id', '=', 'masters_unit.id');
        $query = $query->leftJoin('masters_employee', 'masters_training_schedule.trainer_id', '=', 'masters_employee.id');
        $query = $query->leftJoin('masters_department', 'masters_training_schedule.department_id', '=', 'masters_department.id');
        $query = $query->leftJoin('masters_topic', 'masters_training_schedule.topic_id', '=', 'masters_topic.id');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('name_of_the_conference_hall', 'LIKE', '%' . $search . '%');
            });
        }


        if ($request->has('name_of_the_conference_hall') && $request->name_of_the_conference_hall) {
            $query = $query->where('name_of_the_conference_hall', 'LIKE', '%' . $request->name_of_the_conference_hall . '%');
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('masters_training_schedule.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('capacity') && $request->capacity) {
            $query = $query->where('capacity', 'LIKE', '%' . $request->capacity . '%');
        }
        if ($request->has('projector_or_lcd_availability') && $request->projector_or_lcd_availability) {
            $query = $query->where('projector_or_lcd_availability', 'LIKE', '%' . $request->projector_or_lcd_availability . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_training_schedule.status', decryptId($request->status));
        }

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select('masters_training_schedule.*', 'masters_unit.unit_name', 'masters_employee.emp_name', 'masters_department.department_name', 'masters_topic.topic_name')->leftJoin('masters_unit', 'masters_training_schedule.unit_id', '=', 'masters_unit.id')->leftJoin('masters_topic', 'masters_training_schedule.topic_id', '=', 'masters_topic.id')->leftJoin('masters_department', 'masters_training_schedule.department_id', '=', 'masters_department.id')->leftJoin('masters_employee', 'masters_training_schedule.trainer_id', '=', 'masters_employee.id')
            ->where('masters_training_schedule.id', $id)
            ->first();

        return $data;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('masters_training_schedule'));
    }
}
