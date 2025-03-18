<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OccupationHealthInspection extends Model
{
    protected $table = 'inspection_ohc_occupational_health_inspection';

    protected $primaryKey = 'id';

    protected $fillable = [
        'reference_id',
        'checklist',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function store($responses)
    {

        $request = request();

        $insert_array = [
            'checklist' => json_encode($responses),

            'created_by' => Auth::id(),
        ];

        return self::create($insert_array);
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
    public function Selectone($id)
    {
        return $this->where('reference_id', $id)->first();
    }
}

