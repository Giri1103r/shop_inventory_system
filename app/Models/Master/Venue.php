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


    protected $table = 'masters_venue';
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
        $query = $this->select('masters_venue.*', 'masters_unit.unit_name');
        $query = $query->leftJoin('masters_unit', 'masters_venue.unit_id', '=', 'masters_unit.id');
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('name_of_the_conference_hall', 'LIKE', '%' . $search . '%');
            });
        }


        if ($request->has('name_of_the_conference_hall') && $request->name_of_the_conference_hall) {
            $query = $query->where('name_of_the_conference_hall', 'LIKE', '%' . $request->name_of_the_conference_hall . '%');
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('masters_venue.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('capacity') && $request->capacity) {
            $query = $query->where('capacity', 'LIKE', '%' . $request->capacity . '%');
        }
        if ($request->has('projector_or_lcd_availability') && $request->projector_or_lcd_availability) {
            $query = $query->where('projector_or_lcd_availability', 'LIKE', '%' . $request->projector_or_lcd_availability . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('status', decryptId($request->status));
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

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('masters_venue.*', 'masters_unit.unit_name');
        $query = $query->leftJoin('masters_unit', 'masters_venue.unit_id', '=', 'masters_unit.id');
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
            $query = $query->where('masters_venue.unit_id', decryptId($request->unit_id));
        }
        if ($request->has('capacity') && $request->capacity) {
            $query = $query->where('capacity', 'LIKE', '%' . $request->capacity . '%');
        }
        if ($request->has('projector_or_lcd_availability') && $request->projector_or_lcd_availability) {
            $query = $query->where('projector_or_lcd_availability', 'LIKE', '%' . $request->projector_or_lcd_availability . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_venue.status', decryptId($request->status));
        }

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select('masters_venue.*', 'masters_unit.unit_name')->leftJoin('masters_unit', 'masters_venue.unit_id', '=', 'masters_unit.id')
            ->where('masters_venue.id', $id)
            ->first();

        return $data;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('masters_venue'));
    }
}
