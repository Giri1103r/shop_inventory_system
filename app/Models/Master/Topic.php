<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Topic extends Model
{
    use  HasFactory;


    protected $table = 'training_masters_topic';
    protected $primaryKey = 'id';

    protected $fillable = [
        'topic_id',
        'topic_name',
        'no_of_questions',
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
        $query = $this->select('training_masters_topic.*');
        // dd($query);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('topic_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('topic_name', 'LIKE', '%' . $search . '%');
            });
        }
        
        if ($request->has('from_date') && $request->from_date) {
            $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
            $query = $query->where('training_masters_topic.created_at', '>=', $fromDate);
        }
        
        if ($request->has('to_date') && $request->to_date) {
            $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();
            $query = $query->where('training_masters_topic.created_at', '<=', $toDate);
        }
        

        if ($request->has('topic_id') && $request->topic_id) {
            $query = $query->where('topic_id', 'LIKE', '%' . $request->topic_id . '%');
        }
        if ($request->has('topic_name') && $request->topic_name) {
            $query = $query->where('topic_name', 'LIKE', '%' . $request->topic_name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('training_masters_topic.status', decryptId($request->status));
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

        return $this->where('topic_name',  $data)->get();
    }

    public function ExistuniqueCheck($data, $id)
    {
        return $this->where('topic_name',  $data)
            ->where('id', '!=', $id)
            ->get();
    }

    public function store()
    {
        $request = request();

        $insert_array = array(
            'topic_id' => $request->topic_id,
            'topic_name' => $request->topic_name,
            'no_of_questions' => $request->no_of_questions,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'topic_id' => $request->topic_id,
            'topic_name' => $request->topic_name,
            'no_of_questions' => $request->no_of_questions,
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
        $query = $this->select('training_masters_topic.*');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('topic_id LIKE "%' . $search . '%"')
                    ->orWhereRaw('topic_name LIKE "%' . $search . '%"');
            });
        }
        if ($request->has('topic_id') && $request->topic_id) {
            $query = $query->where('topic_id', 'LIKE', '%' . $request->topic_id . '%');
        }
        if ($request->has('topic_name') && $request->topic_name) {
            $query = $query->where('topic_name', 'LIKE', '%' . $request->topic_name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('training_masters_topic.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'training_masters_topic.*'
        )
            ->where('training_masters_topic.id', $id)
            ->first();

        return $data;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('training_masters_topic'));

        static::created(function ($model) {

            $uniqueId = 'TOPIC-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['topic_id' => $uniqueId]);
        });
    }
}
