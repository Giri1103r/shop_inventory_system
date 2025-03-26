<?php

namespace App\Models\Inspection\Environment;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class AmbientNoiseMonitoring extends Model
{

    protected $table = 'inspection_environment_ambient_noise_monitoring';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'environment_id',
        'sr_no',
        'location_id',
        'unit_id',
        'noise_level_dba',
        'date_of_monitoring',
        'next_due_date_of_monitoring',
        'noise_level_dba_day',
        'noise_level_dba_night',
        'date_of_monitoring_dropdown',
        'date_of_monitoring_date',
        'next_due_date_of_monitoring_dropdown',
        'next_due_date_of_monitoring_date',
        'act_rule',
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
   

    public function store($environmentId)
    {
        $request = request();
        $monitors = $request->input('monitoring');
        if (!empty($monitors) && is_array($monitors)) {
            foreach ($monitors as  $monitorData) {

                $data = [
                    'environment_id' => $environmentId,
                    'sr_no' =>  $monitorData['sr_no'],
                    'location_id' => decryptId($monitorData['location_id']),
                    'unit_id' => decryptId($monitorData['unit_id']),
                    'noise_level_dba' =>  $monitorData['noise_level_dba'],
                    'date_of_monitoring' => DBdateformat($monitorData['date_of_monitoring']),
                    'next_due_date_of_monitoring' => DBdateformat($monitorData['next_due_date_of_monitoring']),
                    'noise_level_dba_day' =>  $monitorData['noise_level_dba_day'],
                    'noise_level_dba_night' =>  $monitorData['noise_level_dba_night'],
                    'date_of_monitoring_dropdown' => decryptId($monitorData['date_of_monitoring_dropdown']),
                    'date_of_monitoring_date' => DBdateformat($monitorData['date_of_monitoring_date']),
                    'next_due_date_of_monitoring_dropdown' => decryptId($monitorData['next_due_date_of_monitoring_dropdown']),
                    'next_due_date_of_monitoring_date' => DBdateformat($monitorData['next_due_date_of_monitoring_date']),
                    'act_rule' =>  $monitorData['act_rule'],
                    'remark' =>  $monitorData['remark'],
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
        $query = $this->select('inspection_environment_ambient_noise_monitoring.*', 'masters_location.location_name', 'masters_unit.unit_name', 'inspection_environment_table.environment_no');
        $query = $query->leftJoin('inspection_environment_table', 'inspection_environment_ambient_noise_monitoring.environment_id', '=', 'inspection_environment_table.id');
        $query = $query->leftJoin('masters_location', 'inspection_environment_ambient_noise_monitoring.location_id', '=', 'masters_location.id');
        $query = $query->leftJoin('masters_unit', 'inspection_environment_ambient_noise_monitoring.unit_id', '=', 'masters_unit.id');
        // dd($query);

        if ($request->has('ambient_noise_id') && $request->ambient_noise_id) {
            $query = $query->where('inspection_environment_ambient_noise_monitoring.ambient_noise_id', decryptId($request->ambient_noise_id));
        }
        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('inspection_environment_ambient_noise_monitoring.location_id', decryptId($request->location_id));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('inspection_environment_ambient_noise_monitoring.unit_id', decryptId($request->unit_id));
        }

        if ($request->has('status') && $request->status) {
            $query = $query->where('inspection_environment_ambient_noise_monitoring.status', decryptId($request->status));
        }
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($envId)
    {
        $data = $this->select('inspection_environment_ambient_noise_monitoring.*', 'masters_location.location_name', 'masters_unit.unit_name', 'inspection_environment_table.environment_no')
            ->leftJoin('masters_location', 'inspection_environment_ambient_noise_monitoring.location_id', '=', 'masters_location.id')->leftJoin('inspection_environment_table', 'inspection_environment_ambient_noise_monitoring.environment_id', '=', 'inspection_environment_table.id')
            ->leftJoin('masters_unit', 'inspection_environment_ambient_noise_monitoring.unit_id', '=', 'masters_unit.id')->where('inspection_environment_ambient_noise_monitoring.environment_id', $envId)->where('inspection_environment_ambient_noise_monitoring.status',1)
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
