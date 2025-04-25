<?php

namespace App\Models\Inspection\Environment;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class WorkNoiseMonitoring extends Model
{

    protected $table = 'inspection_environment_work_noise_monitoring';
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
        'noise_level_dba_dropdown',
        'noise_level_dba_no',
        'date_of_monitoring_date',
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
                    'noise_level_dba_dropdown' =>  decryptId($monitorData['noise_level_dba_dropdown']),
                    'noise_level_dba_no' =>  $monitorData['noise_level_dba_no'],
                    'date_of_monitoring_date' => DBdateformat($monitorData['date_of_monitoring_date']),
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
        $query = $this->select('inspection_environment_work_noise_monitoring.*', 'masters_location.location_name', 'masters_unit.unit_name', 'inspection_environment_table.environment_no');
        $query = $query->leftJoin('inspection_environment_table', 'inspection_environment_work_noise_monitoring.environment_id', '=', 'inspection_environment_table.id');
        $query = $query->leftJoin('masters_location', 'inspection_environment_work_noise_monitoring.location_id', '=', 'masters_location.id');
        $query = $query->leftJoin('masters_unit', 'inspection_environment_work_noise_monitoring.unit_id', '=', 'masters_unit.id');

        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($envId)
    {
        $data = $this->select('inspection_environment_work_noise_monitoring.*', 'masters_location.location_name', 'masters_unit.unit_name', 'inspection_environment_table.environment_no')
            ->leftJoin('masters_location', 'inspection_environment_work_noise_monitoring.location_id', '=', 'masters_location.id')->leftJoin('inspection_environment_table', 'inspection_environment_work_noise_monitoring.environment_id', '=', 'inspection_environment_table.id')
            ->leftJoin('masters_unit', 'inspection_environment_work_noise_monitoring.unit_id', '=', 'masters_unit.id')->where('inspection_environment_work_noise_monitoring.environment_id', $envId)
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
