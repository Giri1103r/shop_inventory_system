<?php

namespace App\Models\Inspection\Ohc;
use App\Scopes\TrashScope;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class HealthInstrumentCalibration extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'inspection_ohc_health_instrument_calibration_track_sheet';


    protected $fillable = [
        'health_auto_id',
        'document_reference_id',
        'unit_id',
        'created_by',
        'updated_by',
        'updated_at',
        'created_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];


    public function list(){

        $request = request();
        $search = '';
         
        $query = $this->select('inspection_ohc_health_instrument_calibration_track_sheet.*','masters_unit.unit_name')
                      ->leftjoin('masters_unit','masters_unit.id','=','inspection_ohc_health_instrument_calibration_track_sheet.unit_id');

        $org_total =  $query;
        $org_total_counts = $org_total->count();
        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_ohc_health_instrument_calibration_track_sheet.health_auto_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('health_instrument_id') && $request->health_instrument_id) {
            $query = $query->where('inspection_ohc_health_instrument_calibration_track_sheet.health_auto_id', 'LIKE', '%' . $request->health_instrument_id . '%');
        }
        if ($request->has('unit_id') && $request->unit_id) {

            $query = $query->where('inspection_ohc_health_instrument_calibration_track_sheet.unit_id',  decryptId($request->unit_id));
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('inspection_ohc_health_instrument_calibration_track_sheet.status',  decryptId($request->status));
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

    public function store(){

        $request = request();
        $insert_array = array(
            'document_reference_id' => decryptId($request->document_reference_id),
            'unit_id' => decryptId($request->unit_id),
            'created_by' => Auth::id()
        );
        $data =  $this->create($insert_array);
        return $data;
    }


    public function selectOne($id){
           $data = $this->select(
            'inspection_ohc_health_instrument_calibration_track_sheet.id as instrument_id', 
            'inspection_ohc_health_instrument_calibration_track_sheet_details.id as instrument_detail_id', 
            'inspection_ohc_health_instrument_calibration_track_sheet.*','inspection_ohc_health_instrument_calibration_track_sheet_details.*')
            ->leftjoin('inspection_ohc_health_instrument_calibration_track_sheet_details','inspection_ohc_health_instrument_calibration_track_sheet_details.health_instrument_id','=','inspection_ohc_health_instrument_calibration_track_sheet.id')
            ->where('inspection_ohc_health_instrument_calibration_track_sheet.id',$id)
            ->get();
            return $data;
                  
    }

     public function getDocumentId($id)
    {
        return $this->where('id', $id)->first();
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_ohc_health_instrument_calibration_track_sheet.*','inspection_ohc_health_instrument_calibration_track_sheet_details.*','masters_unit.unit_name','inspection_ohc_health_instrument_calibration_track_sheet.id as instrument_id','inspection_ohc_health_instrument_calibration_track_sheet_details.id as instrument_detail_id','inspection_static_docno.*')
        ->leftjoin('inspection_ohc_health_instrument_calibration_track_sheet_details','inspection_ohc_health_instrument_calibration_track_sheet_details.health_instrument_id','=','inspection_ohc_health_instrument_calibration_track_sheet.id')
        ->leftjoin('masters_unit','masters_unit.id','=','inspection_ohc_health_instrument_calibration_track_sheet.unit_id')
        ->leftJoin('inspection_static_docno', 'inspection_ohc_health_instrument_calibration_track_sheet.document_reference_id', '=', 'inspection_static_docno.id');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;
            
            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_ohc_health_instrument_calibration_track_sheet.health_auto_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('health_instrument_id') && $request->health_instrument_id) {
            $query = $query->where('inspection_ohc_health_instrument_calibration_track_sheet.health_auto_id', 'LIKE', '%' . $request->health_instrument_id . '%');
        }
        if ($request->has('unit_id') && $request->unit_id) {

            $query = $query->where('inspection_ohc_health_instrument_calibration_track_sheet.unit_id',  decryptId($request->unit_id));
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('inspection_ohc_health_instrument_calibration_track_sheet.status',  decryptId($request->status));
        }

        $query->orderBy('inspection_ohc_health_instrument_calibration_track_sheet.id', 'DESC');
        $results = $query->get();
        $query = $results->groupBy('unit_name');

        return  $query;
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

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_ohc_health_instrument_calibration_track_sheet'));
        static::created(function ($model) {

            $uniqueId = 'HEALTH-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['health_auto_id' => $uniqueId]);
        });
    }
}

