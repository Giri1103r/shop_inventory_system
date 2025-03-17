<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class WeeklyAmbulanceChecklist extends Model
{
    protected $table = 'inspection_ohc_weekly_ambulance_inspection_checklist';
    protected $primaryKey = 'id';

    protected $fillable = [
        'weekly_ambulance_id',
        'checklist_sub_type_data_name_id',
        'weekly_ambulance_status',
        'remarks',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function store($weekly_ambulance_details, $data)
    {
        $insert_array = [
            'weekly_ambulance_id' => $weekly_ambulance_details->id,
            'checklist_sub_type_data_name_id' => json_encode($data),

            'created_by' => Auth::id(),
        ];

        return $this->create($insert_array);
    }

    public function WeekambulanceSelectone($id){
        return $this->where('weekly_ambulance_id',$id)->first();
    }


}
