<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingCalendar extends Model
{
    use  HasFactory;


    protected $table = 'training_calendar';
    protected $primaryKey = 'id';

    protected $fillable = [
        'from_date',
        'to_date',
        'venue_id',
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
        $query = $this->select('training_calendar.*', 'training_masters_venue.name_of_the_conference_hall');
        $query = $query->leftJoin('training_masters_venue', 'training_calendar.venue_id', '=', 'training_masters_venue.id');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('from_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('to_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('training_masters_venue.name_of_the_conference_hall', 'LIKE', '%' . $search . '%');
            });
        }


        if ($request->has('from_date') && $request->from_date) {
            $query = $query->where('from_date', 'LIKE', '%' . $request->from_date . '%');
        }
        if ($request->has('to_date') && $request->to_date) {
            $query = $query->where('to_date', 'LIKE', '%' . $request->to_date . '%');
        }
        if ($request->has('venue_id') && $request->venue_id) {
            $query = $query->where('training_calendar.venue_id', decryptId($request->venue_id));
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('training_calendar.status', decryptId($request->status));
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

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'from_date' => DBdatetimeformat($request->from_date),
            'to_date' => DBdatetimeformat($request->to_date),
            'venue_id' => decryptId($request->venue_id),
            'updated_by' => Auth::id()
        );
        return $this->where('id', $id)->update($update_array);
    }
    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('training_calendar.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('from_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('to_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('training_masters_venue.name_of_the_conference_hall', 'LIKE', '%' . $search . '%');
            });
        }


        if ($request->has('from_date') && $request->from_date) {
            $query = $query->where('from_date', 'LIKE', '%' . $request->from_date . '%');
        }
        if ($request->has('to_date') && $request->to_date) {
            $query = $query->where('to_date', 'LIKE', '%' . $request->to_date . '%');
        }
        if ($request->has('venue_id') && $request->venue_id) {
            $query = $query->where('training_calendar.venue_id', decryptId($request->venue_id));
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('training_calendar.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'training_calendar.*'
        )
            ->where('training_calendar.id', $id)
            ->first();

        return $data;
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('training_calendar'));
    }
}
