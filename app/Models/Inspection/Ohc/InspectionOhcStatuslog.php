<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InspectionOhcStatuslog extends Model
{
    protected $table = 'inspection_ohc_status_log';

    protected $primaryKey = 'id';

    protected $fillable = [
        'reference_id',
        'from_status',
        'to_status',
        'type',
        'remarks',
        'approved_by',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
    public function store($data){
        $request = request();

        $insert_array = [
            'reference_id' =>$data['reference_id'],
            'from_status' =>$data['from_status'],
            'to_status' =>$data['to_status'],
            'type' =>$data['type'],
            'remarks' =>$data['remarks'],
            'created_by' => $data['created_by'],
            'approved_by' => $data['approved_by'] ?? null ,

        ];

        return self::create($insert_array);
    }
    public function floormanger($id ,$floortype,$type){
        return $this->where('reference_id',$id)->where('type',$type)->where('from_status',$floortype)->first();
    }
    public function safetyofficer($id ,$safetytype,$type){
        return $this->where('reference_id',$id)->where('type',$type)->where('from_status',$safetytype)->first();
    }
    public function  getStatuslog($id, $type){
        return $this->where('reference_id',$id)->where('type',$type)->get();
    }
}
