<?php

namespace App\Models\OhcManagement\Status;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreatorLog extends Model
{
    protected $table = 'ohc_creator_log';
    protected $primaryKey = 'id';

    protected $fillable = [
        'type',
        'unit_id',
        'inventory_id',
        'department_id',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    public function store( $data ,  $user_medicine_issuance){

        $insert_array = [
        'type'=>TYPE_OHC_ISSUANCE,
        'unit_id' => $user_medicine_issuance->unit_id,
        'inventory_id'=> $data->id,
        'department_id'=> $user_medicine_issuance->department_id,
        'created_by'=>Auth::id(),
        ];
       return $this->create($insert_array);
    }
}
