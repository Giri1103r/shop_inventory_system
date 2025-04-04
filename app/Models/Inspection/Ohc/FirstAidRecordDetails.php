<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use App\Scopes\TrashScope;

class FirstAidRecordDetails extends Model
{
    use  HasFactory;

    protected $table = 'ohc_first_aid_record_details';

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
        $query = $this->select('ohc_first_aid_record_details.*');

        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('month', 'LIKE', '%' . $search . '%')
                    ->orWhere('year', 'LIKE', '%' . $search . '%');
            });
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
            'month' =>$request->month,
            'year' => $request->year,
            'created_by' => Auth::id(),
            'overall_total_number_of_first_aid' => $request->overall_total_number_of_first_aid,
        );

        return $this->create($insert_array);
    }

    public function selectOne($id)
    {
        return $this->where('id', $id)->first();
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
        $query = $this->select('ohc_first_aid_record_details.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhere('month', 'LIKE', '%' . $search . '%')
                    ->orWhere('year', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('month') && $request->month) {
            $query = $query->where('month', 'LIKE', '%' . $request->month . '%');
        }
        if ($request->has('year') && $request->year) {
            $query = $query->where('year', 'LIKE', '%' . $request->year . '%');
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ohc_first_aid_record_details'));
    }

}
