<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use App\Scopes\TrashScope;
use Carbon\Carbon;

class FirstAidRecordDetails extends Model
{
    use  HasFactory;

    protected $table = 'inspection_ohc_first_aid_record_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'document_reference_id',
        'month',
        'year',
        'overall_total_number_of_first_aid',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

    public function list()
    {
        $request = request();
        $search = '';

        $query = $this->select('inspection_ohc_first_aid_record_details.*');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('month', 'LIKE', '%' . $search . '%')
                    ->orWhereYear('year', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_first_aid_record_details.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_first_aid_record_details.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_first_aid_record_details.created_at', [$startDate, $endDate]);
        }
        if ($request->has('month') && $request->month) {
            $query = $query->where('month', 'LIKE', '%' . $request->month . '%');
        }
        if ($request->has('year') && $request->year) {
            $query = $query->where('year', 'LIKE', '%' . $request->year . '%');
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
            'document_reference_id' => decryptId($request->document_reference_id),
            'month' => $request->month,
            'year' => $request->year,
            'created_by' => Auth::id(),
            'overall_total_number_of_first_aid' => $request->overall_total_number_of_first_aid,
        );

        return $this->create($insert_array);
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->where('status', 1)->where('trash', 'NO')->first();
    }

    public function UniqueCheck($month, $year)
    {
        return $this->where('month', $month)
            ->where('year', $year)
            ->get();
    }

    public function ExistuniqueCheck($month, $year, $id)
    {
        return $this->where('month', $month)
            ->where('year', $year)
            ->where('id', '!=', $id)
            ->get();
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

        $query = $this->select(
            'inspection_ohc_first_aid_record_details.*',
            'inspection_ohc_first_aid_record_checklist.*',
            'masters_department.*',
            'masters_unit.*',
            'inspection_ohc_first_aid_record_details.created_by as checked_by',
            'inspection_ohc_first_aid_record_details.id as first_aid_record_id'
        )
            ->leftJoin('inspection_ohc_first_aid_record_checklist', 'inspection_ohc_first_aid_record_details.id', '=', 'inspection_ohc_first_aid_record_checklist.ohc_first_aid_record_details_id')
            ->leftJoin('masters_department', 'inspection_ohc_first_aid_record_checklist.department', '=', 'masters_department.id')
            ->leftJoin('masters_unit', 'inspection_ohc_first_aid_record_checklist.unit', '=', 'masters_unit.id')
            ->where('inspection_ohc_first_aid_record_details.trash', 'NO');


        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhere('month', 'LIKE', '%' . $search . '%')
                    ->orWhereYear('year', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('month') && $request->month) {
            $query = $query->where('month', 'LIKE', '%' . $request->month . '%');
        }
        if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_first_aid_record_details.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_ohc_first_aid_record_details.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_ohc_first_aid_record_details.created_at', [$startDate, $endDate]);
        }
        if ($request->has('year') && $request->year) {
            $query = $query->where('year', 'LIKE', '%' . $request->year . '%');
        }
        $query->orderBy('inspection_ohc_first_aid_record_details.id', 'DESC');

        $data = $query->get();

        if ($data) {
            return $data = $data->groupBy('inspection_ohc_first_aid_record_details_id');
        } else {
            return $data;
        }
    }

    // protected static function booted()
    // {
    //     static::addGlobalScope(new TrashScope('inspection_ohc_first_aid_record_details'));
    // }
}
