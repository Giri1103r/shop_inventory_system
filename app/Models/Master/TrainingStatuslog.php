<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TrainingStatuslog extends Model
{
    protected $table = 'training_statuslog';
    protected $primaryKey = 'id';

    protected $fillable = [
        'training_schedule_id',
        'training_status',
        'remarks',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    //  STORE
    public function storestatus($id , $training_status)
    {
        $request = request();
        $insert_data = [
            'training_schedule_id' => $id,
            'training_status' => $training_status,
            'remarks' => $request->remark ?? '',
            'created_by' => Auth::id(),
        ];
        return $this->insert($insert_data);
    }



    public function getstatusdetails($id)
    {
        $data = Statuslog::where('reference_id', $id)->where('type', TYPE_PPE_REQUEST)->latest('id')->first();
        return $data;
    }

    public function statuslog($id){
        $data = Statuslog::where('reference_id', $id)->where('type', TYPE_PPE_REQUEST)->get();
        return $data;
    }

    public function statuslogdetails( $id){
        $data = Statuslog::where('reference_id', $id)->where('type', TYPE_PPE_REQUEST)->latest('id')->first();
        return $data;
    }


    public function getexemptionstatusdetails($id)
    {
        $data = Statuslog::where('reference_id', $id)->where('type', TYPE_PPE_EXEMPTION)->get();
        return $data;
    }
}
