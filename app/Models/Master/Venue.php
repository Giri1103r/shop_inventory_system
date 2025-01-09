<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venue extends Model
{
    use  HasFactory;


    protected $table = 'training_masters_venue';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name_of_the_conference_hall',
        'unit_id',
        'capacity',
        'projector_or_lcd_availability',
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
        $query = $this->select('training_masters_venue.*', 'masters_unit.unit_name');
        $query = $query->leftJoin('masters_unit', 'training_masters_venue.unit_id', '=', 'masters_unit.id');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('name_of_the_conference_hall', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('capacity', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('from_date') && $request->from_date) {
            $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
            $query = $query->where('training_masters_venue.created_at', '>=', $fromDate);
        }

        if ($request->has('to_date') && $request->to_date) {
            $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();
            $query = $query->where('training_masters_venue.created_at', '<=', $toDate);
        }
        if ($request->has('name_of_the_conference_hall') && $request->name_of_the_conference_hall) {
            $query = $query->where('name_of_the_conference_hall', 'LIKE', '%' . $request->name_of_the_conference_hall . '%');
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('training_masters_venue.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('capacity') && $request->capacity) {
            $query = $query->where('capacity', 'LIKE', '%' . $request->capacity . '%');
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('training_masters_venue.status', decryptId($request->status));
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

    public function UniqueCheck($name_of_the_conference_hall, $unit_id)
    {

        return $this->where('name_of_the_conference_hall',  $name_of_the_conference_hall)->where('unit_id', $unit_id)->get();
    }

    public function ExistuniqueCheck($name_of_the_conference_hall, $unit_id, $id)
    {
        return $this->where('name_of_the_conference_hall',  $name_of_the_conference_hall)->where('unit_id', $unit_id)
            ->where('id', '!=', $id)
            ->get();
    }


    public function store()
    {
        $request = request();

        $insert_array = array(
            'name_of_the_conference_hall' => $request->name_of_the_conference_hall,
            'unit_id' => decryptId($request->unit_id),
            'capacity' => $request->capacity,
            'projector_or_lcd_availability' => $request->projector_or_lcd_availability,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'name_of_the_conference_hall' => $request->name_of_the_conference_hall,
            'unit_id' => decryptId($request->unit_id),
            'capacity' => $request->capacity,
            'projector_or_lcd_availability' => $request->projector_or_lcd_availability,
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
    public function ajaxList($venue_id , $unitId = '')
    {
        $query = $this->select('id', 'name_of_the_conference_hall')->where('status', 1);

        if ($unitId != '') {
            $query->where('unit_id', $unitId);
        }
        if (!empty($unitId) && !empty($venue_id)) {
            $query = $query->where('unit_id', $unitId)->where('status', 1)->orWhere(function ($query) use ($venue_id, $unitId) {
                $query->where('unit_id', $unitId)->where('id', $venue_id);
            });
        }
        $datas = $query->get();

        $list = [];
        foreach ($datas as $data) {
            $listvalue = [];
            $listvalue['id'] = encryptId($data->id);
            $listvalue['name'] = $data->name_of_the_conference_hall;
            $list[] = $listvalue;
        }

        return $list;
    }

    public function ajaxallList($unitId = '')
    {
        $query = $this->select('id', 'name_of_the_conference_hall')->where('status', 1);

        if ($unitId != '') {

            $query = $query->where('unit_id', $unitId);
        }

        $datas = $query->get();

        $list = [];
        foreach ($datas as $data) {
            $listvalue = [];
            $listvalue['id'] = encryptId($data->id);
            $listvalue['name'] = $data->name_of_the_conference_hall;
            $list[] = $listvalue;
        }
        return $list;
    }
    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('training_masters_venue.*', 'masters_unit.unit_name');
        $query = $query->leftJoin('masters_unit', 'training_masters_venue.unit_id', '=', 'masters_unit.id');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;


            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('name_of_the_conference_hall', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('capacity', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('from_date') && $request->from_date) {
            $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
            $query = $query->where('training_masters_venue.created_at', '>=', $fromDate);
        }

        if ($request->has('to_date') && $request->to_date) {
            $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();
            $query = $query->where('training_masters_venue.created_at', '<=', $toDate);
        }
        if ($request->has('name_of_the_conference_hall') && $request->name_of_the_conference_hall) {
            $query = $query->where('name_of_the_conference_hall', 'LIKE', '%' . $request->name_of_the_conference_hall . '%');
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('training_masters_venue.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('capacity') && $request->capacity) {
            $query = $query->where('capacity', 'LIKE', '%' . $request->capacity . '%');
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('training_masters_venue.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select('training_masters_venue.*', 'masters_unit.unit_name')->leftJoin('masters_unit', 'training_masters_venue.unit_id', '=', 'masters_unit.id')
            ->where('training_masters_venue.id', $id)
            ->first();

        return $data;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('training_masters_venue'));
    }
}
