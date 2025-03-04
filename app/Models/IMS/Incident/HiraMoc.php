<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HiraMoc extends Model
{
    use  HasFactory;


    protected $table = 'ims_master_incident_hiramoc';
    protected $primaryKey = 'id';

    protected $fillable = [
        'accident_id',
        'incident_id',
        'fire_id',
        'invesigation_id',
        'hira_id',
        'moc_id',
        'hiramoc_status',
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


    public function gethiramocdetails()
    {
        $request = request();

        $accident_id = decryptId($request->accident_id);
        $incident_id = decryptId($request->incident_id);
        $fire_id = decryptId($request->fire_id);
        $hira_id = decryptId($request->hira_id);
        $moc_id = decryptId($request->moc_id);

       

        $query = $this->select('ims_master_incident_hiramoc.*');

        if ($accident_id) {
                $query->where(['accident_id'=> $accident_id , 'hiramoc_status' => 'T' ]);
        } elseif ($incident_id) {
            
                $query->where(['incident_id'=> $incident_id , 'hiramoc_status' => 'T' ]);
          
        }elseif ($fire_id) {
                $query->where(['fire_id'=> $fire_id , 'hiramoc_status' => 'T' ]);
        }

        $get_data = $query->get(); 

        return response()->json($get_data);
    }
    public function store()
    {
        $request = request();
    
        $insert_array = array(
            'accident_id' =>  decryptId($request->accident_id) ?? null,
            'incident_id' =>  decryptId($request->incident_id) ?? null,
            'fire_id' =>  decryptId($request->fire_id) ?? null,
            'hira_id' =>  decryptId($request->fire_inicdent_report_id) ?? null,
            'moc_id' =>  decryptId($request->fire_inicdent_report_id) ?? null,
            'hiramoc_status' => 'T',
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updateinvestigation($incident_Id, $invesigation_id)
    {
        $request = request();

        $update_array = array(
            'invesigation_id' => $invesigation_id,
            'hiramoc_status' => 'Y',
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('incident_id', $incident_Id)->update($update_array);
    }
    public function updateAccidentInvestigation($accident_id, $invesigation_id)
    {
        $request = request();

        $update_array = array(
            'invesigation_id' => $invesigation_id,
            'hiramoc_status' => 'Y',
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('accident_id', $accident_id)->update($update_array);
    }

    public function delete_temprow($accidentId, $incidentId, $fireId)
    {
        $this->where(function($query) use ($accidentId, $incidentId, $fireId) {
            $query->orWhere('accident_id', $accidentId)
                  ->orWhere('incident_id', $incidentId)
                  ->orWhere('fire_id', $fireId);
        })
        ->where('hiramoc_status', 'T')
        ->delete();
    }
    

}
