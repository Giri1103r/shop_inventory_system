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
            'created_by' => Auth::id(),
        ];
       
        return self::create($insert_array);
    }
}
