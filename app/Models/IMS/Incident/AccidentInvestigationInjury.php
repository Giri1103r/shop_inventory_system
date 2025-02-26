<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccidentInvestigationInjury extends Model
{
    use  HasFactory;


    protected $table = 'ims_accident_investigation_injury';
    protected $primaryKey = 'id';

    protected $fillable = [
        'accident_id',
        'accident_investigation_id',
        'injury_person_type',
        'injury_person_id',
        'injury_person_name',
        'injury_person_designation',
        'injury_person_department_id',
        'nature_of_injury',
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

    public function store($accident_investigationId)
    {
        $request = request();
        // dd($request->all());
        $IncidentBodyParts = new AccidentBodyParts();

        $injuryPerson = $request->input('injury_person');

        if (!empty($injuryPerson) && is_array($injuryPerson)) {
            foreach ($injuryPerson as $injuryPersonData) {
                $insert_array = [
                    'accident_id' => decryptId($request->accident_id),
                    'accident_investigation_id' => $accident_investigationId,
                    'injury_person_type' => $injuryPersonData['injury_person_type'],
                    'injury_person_id' => decryptId($injuryPersonData['injury_person_id']),
                    'injury_person_name' => $injuryPersonData['injury_person_name'],
                    'injury_person_designation' => $injuryPersonData['injury_person_designation'],
                    'injury_person_department_id' => decryptId($injuryPersonData['injury_person_department_id']),
                    'nature_of_injury' => $injuryPersonData['nature_of_injury'],
                    'created_by' => Auth::id()
                ];
                $saveinjuryData = $this->create($insert_array);

                $inj_person_arr[] = $injuryPersonData['injury_person_id'];

                $injdata = [
                    'injury_id' => $saveinjuryData->id,
                    'status' => 'Y',
                ];

                $updtinjBody = $IncidentBodyParts->where(['injury_person_id' => $injuryPersonData['injury_person_id'], 'status' => 'T'])->update($injdata);
            }
        }
        $IncidentBodyParts->updateStatusForIncident(decryptId($accident_investigationId), $inj_person_arr);
    }
}
