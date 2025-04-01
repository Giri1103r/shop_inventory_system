<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class CertifiedFireFighter extends Model
{

    protected $table = 'inspection_fire_certified_fire_fighter';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'sr_no',
        'emp_name',
        'department_id',
        'emp_code',
        'emp_phone',
        'emp_status',
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


    public function store($environmentId)
    {
        $request = request();
        $firelist = $request->input('fire');
        if (!empty($firelist) && is_array($firelist)) {
            foreach ($firelist as  $fireData) {

                $data = [
                    'sr_no' =>  $fireData['sr_no'],
                    'emp_name' =>  $fireData['emp_name'],
                    'department_id' => decryptId($fireData['department_id']),
                    'emp_code' =>  $fireData['emp_code'],
                    'emp_phone' =>  $fireData['emp_phone'],
                    'emp_status' => decryptId($fireData['emp_status']),
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
        $query = $this->select('inspection_fire_certified_fire_fighter.*', 'masters_location.location_name', 'masters_unit.unit_name', 'inspection_environment_table.environment_no');
        $query = $query->leftJoin('inspection_environment_table', 'inspection_fire_certified_fire_fighter.environment_id', '=', 'inspection_environment_table.id');
        $query = $query->leftJoin('masters_location', 'inspection_fire_certified_fire_fighter.location_id', '=', 'masters_location.id');
        $query = $query->leftJoin('masters_unit', 'inspection_fire_certified_fire_fighter.unit_id', '=', 'masters_unit.id');
     
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($envId)
    {
        $data = $this->select('inspection_fire_certified_fire_fighter.*', 'masters_location.location_name', 'masters_unit.unit_name', 'inspection_environment_table.environment_no')
            ->leftJoin('masters_location', 'inspection_fire_certified_fire_fighter.location_id', '=', 'masters_location.id')->leftJoin('inspection_environment_table', 'inspection_fire_certified_fire_fighter.environment_id', '=', 'inspection_environment_table.id')
            ->leftJoin('masters_unit', 'inspection_fire_certified_fire_fighter.unit_id', '=', 'masters_unit.id')->where('inspection_fire_certified_fire_fighter.environment_id', $envId)->where('inspection_fire_certified_fire_fighter.status', 1)
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

        return $this->where('environment_id', $id)->update($update_data);
    }
}
