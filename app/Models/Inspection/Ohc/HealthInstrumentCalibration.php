<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class HealthInstrumentCalibration extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'inspection_ohc_health_instrument_calibration_track_sheet';


    protected $fillable = [
        'doc_no',
        'issue_date',
        'revision_date',
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
         
        $query = $this->select('inspection_ohc_health_instrument_calibration_track_sheet.*');

        $org_total =  $query;
        $org_total_counts = $org_total->count();
        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('inspection_ohc_health_instrument_calibration_track_sheet.doc_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_ohc_health_instrument_calibration_track_sheet.issue_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('inspection_ohc_health_instrument_calibration_track_sheet.revision_date', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->has('doc_no') && $request->doc_no) {
            $query = $query->where('inspection_ohc_health_instrument_calibration_track_sheet.doc_no', 'LIKE', '%' . $request->doc_no . '%');
        }
        if ($request->has('issue_date') && $request->issue_date) {

            $formattedDate = DBdateformat($request->issue_date);
            $query = $query->whereDate('inspection_ohc_health_instrument_calibration_track_sheet.issue_date', $formattedDate);
        }

        if ($request->has('revision_date') && $request->revision_date) {

            $query = $query->where('inspection_ohc_health_instrument_calibration_track_sheet.revision_date', 'LIKE', '%' . $request->revision_date . '%');
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
            'doc_no' => $request->document_no,
            'issue_date' => DBdateformat($request->issue_date),
            'revision_date' => $request->review_date,
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

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_ohc_health_instrument_calibration_track_sheet.*');

        if ($request->search != null || $request->search != '') {
            $search = $request->search;
            
            $query->where(function ($query) use ($search) {
                $query
                ->orWhere('inspection_ohc_health_instrument_calibration_track_sheet.doc_no', 'LIKE', '%' . $search . '%')
                ->orWhere('inspection_ohc_health_instrument_calibration_track_sheet.issue_date', 'LIKE', '%' . $search . '%')
                ->orWhere('inspection_ohc_health_instrument_calibration_track_sheet.revision_date', 'LIKE', '%' . $search . '%')
                ->orWhere('inspection_ohc_health_instrument_calibration_track_sheet.status', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('document_number') && $request->document_number) {

            $query = $query->where('inspection_ohc_health_instrument_calibration_track_sheet.doc_no', 'LIKE', '%' . $request->document_number . '%');
        }

        if ($request->has('issue_date') && $request->issue_date) {

            $formattedDate = DBdateformat($request->issue_date);
            $query = $query->whereDate('issue_date', $formattedDate);
        }
        if ($request->has('revision_date') && $request->revision_date) {

            $query = $query->where('revision_date', 'LIKE', '%' . $request->revision_date . '%');
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('status',  decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');

        return  $query->get();
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
}

