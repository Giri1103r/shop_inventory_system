<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IncidentInvestigation extends Model
{
    use  HasFactory;


    protected $table = 'ims_initial_incident_investigation';
    protected $primaryKey = 'id';

    protected $fillable = [
        'incident_id',
        'witness',
        'anything_damaged',
        'hira',
        'moc',
        'prca',
        'immediate_action',
        'capa',
        'responsible_person',
        'target_date',
        'remarks',
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
        if (is_array($request->reporting_media)) {
            $reporting_media = implode(',', array_map(function ($item) {
                return $item;
            }, $request->reporting_media));
        } else {

            $reporting_media = decryptId($request->reporting_media);
        }
        $insert_array = array(
            'incident_date_time' => DBdatetimeformat($request->incident_date_time),
            'unit_id' => decryptId($request->unit_id),
            'shift' => $request->shift,
            'location_id' => decryptId($request->location_id),
            'exact_location' => $request->exact_location,
            'iir_type' => $request->iir_type,
            'reported_name' => decryptId($request->reported_name),
            'designation' => $request->designation,
            'department' => $request->department,
            'employee_code' => $request->employee_code,
            'time_of_reporting' => $request->time_of_reporting,
            'reporting_media' => $reporting_media,
            'reporting_media_others' => $request->reporting_media_others,
            'brief_description' => $request->brief_description,
            'incident_status' => STATUS_INCIDENT_REPORT,
            'created_by' => Auth::id()
        );
        return $this->create($insert_array);
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ims_initial_incident'));

        static::created(function ($model) {

            $uniqueId = 'INCIDENT-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->update(['sr_no' => $uniqueId]);
        });
    }
}
