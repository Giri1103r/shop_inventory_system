<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class FireSafetyEquipment extends Model
{

    protected $table = 'inspection_fire_fire_safety_equipmentr';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'doc_no_id',
        'fire_id',
        'sr_no',    
        'name_of_fire_safety',
        'resource_code',
        'series_code',
        'unit_id',
        'allotted_series_code',
        'total_allotted_code',
        'remark',
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


    public function store($fireId)
    {
        $request = request();
        $firelist = $request->input('fire');
        if (!empty($firelist) && is_array($firelist)) {
            foreach ($firelist as  $fireData) {

                $data = [
                    'doc_no_id' => decryptId($request->docNo_id),
                    'fire_id' => $fireId,
                    'sr_no' =>  $fireData['sr_no'],
                    'name_of_fire_safety' =>  $fireData['name_of_fire_safety'],
                    'resource_code' =>  $fireData['resource_code'],
                    'series_code' =>  $fireData['series_code'],
                    'unit_id' => decryptId($fireData['unit_id']),
                    'allotted_series_code' =>  $fireData['allotted_series_code'],
                    'total_allotted_code' =>  $fireData['total_allotted_code'],
                    'remark' => $fireData['remark'],
                    'created_by' => Auth::id(),
                ];
                $this->create($data);
            }
        }
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('inspection_fire_fire_safety_equipmentr.*', 'masters_unit.unit_name',  'inspection_fire_table.fire_no');
        $query = $query->leftJoin('inspection_fire_table', 'inspection_fire_fire_safety_equipmentr.fire_id', '=', 'inspection_fire_table.id');
        $query = $query->leftJoin('masters_unit', 'inspection_fire_fire_safety_equipmentr.unit_id', '=', 'masters_unit.id');
     
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($fireId)
    {
        $data = $this->select('inspection_fire_fire_safety_equipmentr.*', 'masters_unit.unit_name','inspection_fire_table.fire_no')
            ->leftJoin('masters_unit', 'inspection_fire_fire_safety_equipmentr.unit_id', '=', 'masters_unit.id')->leftJoin('inspection_fire_table', 'inspection_fire_fire_safety_equipmentr.fire_id', '=', 'inspection_fire_table.id')
           ->where('inspection_fire_fire_safety_equipmentr.fire_id', $fireId)->where('inspection_fire_fire_safety_equipmentr.status', 1)
            ->get();
        return $data;
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

        return $this->where('fire_id', $id)->update($update_data);
    }
}
