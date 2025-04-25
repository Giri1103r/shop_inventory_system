<?php

namespace App\Models\Inspection\Environment;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class DgSetStackEmissionMonitoring extends Model
{

    protected $table = 'inspection_environment_dgset_monitoring';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'environment_id',
        'sr_no',
        'dg_no',
        'kva_rating',
        'date_of_monitoring',
        'next_due_date_of_monitoring',
        'location',
        'engine_srno',
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
                    'date_of_monitoring' => DBdateformat($monitorData['date_of_monitoring']),
                    'next_due_date_of_monitoring' => DBdateformat($monitorData['next_due_date_of_monitoring']),
                    'dg_no' =>  $monitorData['dg_no'],
                    'kva_rating' =>  $monitorData['kva_rating'],
                    'location' => $monitorData['location'],
                    'engine_srno' => $monitorData['engine_srno'],
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
        $query = $this->select('inspection_environment_dgset_monitoring.*','inspection_environment_table.environment_no');
        $query = $query->leftJoin('inspection_environment_table', 'inspection_environment_dgset_monitoring.environment_id', '=', 'inspection_environment_table.id');
        $query->orderBy('id', 'DESC');
        return  $query->get();
    }

    public function selectOne($envId)
    {
        $data = $this->select('inspection_environment_dgset_monitoring.*', 'inspection_environment_table.environment_no')->leftJoin('inspection_environment_table', 'inspection_environment_dgset_monitoring.environment_id', '=', 'inspection_environment_table.id')->where('inspection_environment_dgset_monitoring.environment_id', $envId)
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
