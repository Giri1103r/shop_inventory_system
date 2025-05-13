<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Fire extends Model
{

    protected $table = 'inspection_fire_table';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'type',
        'fire_no',
        'approval_status',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list($type)
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_fire_table.*')->where('type', $type);
        $org_total =  $query;
        $org_total_counts = $org_total->count();

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->where('fire_no', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('fire_no') && $request->fire_no) {
            $query = $query->where('id', decryptId($request->fire_no));
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('status', decryptId($request->status));
        }
         if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_fire_table.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_fire_table.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_fire_table.created_at', [$startDate, $endDate]);
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

    public function store($env_no, $type)
    {
        $request = request();
        $insert_array = [
            'type' => $type,
            'fire_no' => $env_no,
            'approval_status' => 1,
            'created_by' => Auth::id(),
        ];
        return $this->create($insert_array);
    }

    public function exportdata($type)
    {
        $request = request();
        $search = '';
        // dd($type);
        $query = $this->select('inspection_fire_table.*', 'inspection_static_docno.*');

        if ($type == 2) {
            $query->addSelect('inspection_fire_certified_fire_fighter.*')
                ->leftJoin('inspection_fire_certified_fire_fighter', 'inspection_fire_certified_fire_fighter.fire_id', '=', 'inspection_fire_table.id')
                ->leftJoin('inspection_static_docno', function ($join) {
                    $join->on('inspection_static_docno.id', '=', 'inspection_fire_certified_fire_fighter.doc_no_id');
                });
                $query->where('inspection_static_docno.type', 'CertifiedFireFighter');
        } elseif ($type == 3) {
            $query->addSelect('inspection_fire_fire_safety_equipmentr.*')
                ->leftJoin('inspection_fire_fire_safety_equipmentr', 'inspection_fire_fire_safety_equipmentr.fire_id', '=', 'inspection_fire_table.id')
                ->leftJoin('inspection_static_docno', function ($join) {
                    $join->on('inspection_static_docno.id', '=', 'inspection_fire_fire_safety_equipmentr.doc_no_id');
                });
                $query->where('inspection_static_docno.type', 'FireSafetyEquipment');
        }

        $query->where('inspection_fire_table.type', $type);

        if ($request->has('fire_no') && $request->fire_no) {
            $query = $query->where('inspection_fire_table.id', decryptId($request->fire_no));
        }
        if ($request->has('status') && $request->status) {
            $query = $query->where('inspection_fire_table.status', decryptId($request->status));
        }
         if ($request->has('from_date') && !empty($request->from_date)) {

            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_fire_table.created_at', '>=', $startDate);
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->where('inspection_fire_table.created_at', '<=', $endDate);
        }
        if ($request->has('from_date') && !empty($request->from_date) && $request->has('to_date') && !empty($request->to_date)) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('inspection_fire_table.created_at', [$startDate, $endDate]);
        }
        $query->orderBy('inspection_fire_table.id', 'DESC');
        $data = $query->get();
        if ($data) {
            $groupedData = $data->groupBy('fire_id');
        }
        return  $groupedData;
    }

    public function selectOne($id, $type)
    {
        $data = $this->select('inspection_fire_table.*')->where('type', $type)->where('id', $id)->where('status', 1)
            ->first();
        return $data;
    }

    public function statuschange($id, $envtype)
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

        return $this->where('type', $envtype)->where('id', $id)->update($update_data);
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_table'));

        static::creating(function ($model) {
            $uniqueId = str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $code = match ($model->type) {
                1 => 'DAILY-FIREPUMP',
                2 => 'CERTIFIED-FIREFIGHTER',
                3 => 'FIRESAFETY-EQ',
                4 => 'FIRE-PRENOC',
                5 => 'FIRE-MODULAR',
                default => 'GENERIC',
            };

            $lastenv = self::withoutGlobalScope(TrashScope::class)
                ->where('fire_no', 'like', "$code-%")
                ->orderBy('fire_no', 'desc')
                ->first();

            $nextNumber = 1;

            if ($lastenv) {
                // Extract the last four-digit number after the hyphen
                preg_match('/-(\d{5})$/', $lastenv->fire_no, $matches);
                if (!empty($matches[1])) {
                    $nextNumber = (int) $matches[1] + 1;
                }
            }

            $model->fire_no = sprintf('%s-%05d', $code, $nextNumber);
        });
    }
}
