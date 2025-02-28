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


    public function store()
    {
        $request = request();
    
        $insert_array = array(
            'accident_id' =>  decryptId($request->accident_id) ?? null,
            'incident_id' =>  decryptId($request->incident_id) ?? null,
            'fire_id' =>  decryptId($request->fire_id) ?? null,
            'invesigation_id' =>  $request->accident_report_id ?? null,
            'hira_id' =>  decryptId($request->fire_inicdent_report_id) ?? null,
            'moc_id' =>  decryptId($request->fire_inicdent_report_id) ?? null,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    public function updateinvestigation($incident_Id, $invesigation_id)
    {
        $request = request();

        $update_array = array(
            'invesigation_id' => $invesigation_id,
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
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('accident_id', $accident_id)->update($update_array);
    }
}
