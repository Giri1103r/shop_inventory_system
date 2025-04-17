<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InjuryDetails extends Model
{
    use  HasFactory;


    protected $table = 'ims_injury_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'incident_id',
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

    public function store($incident_id)
    {
        $request = request();
        $IncidentBodyParts = new IncidentBodyParts();

        $injuryPerson = $request->input('injury_person');
        if (!empty($injuryPerson) && is_array($injuryPerson)) {
            foreach ($injuryPerson as $injuryPersonData) {
                $insert_array = [
                    'incident_id' => $incident_id,
                    'injury_person_type' => decryptId($injuryPersonData['injury_person_type']),
                    'injury_person_id' => decryptId($injuryPersonData['injury_person_id']),
                    'injury_person_name' => $injuryPersonData['injury_person_name'],
                    'injury_person_designation' => $injuryPersonData['injury_person_designation'],
                    'injury_person_department_id' => $injuryPersonData['injury_person_department_id'],
                    'nature_of_injury' => decryptId($injuryPersonData['nature_of_injury']),
                    'created_by' => Auth::id()
                ];
                $saveinjuryData = $this->create($insert_array);

                if (decryptId($injuryPersonData['injury_person_type']) == 1 || decryptId($injuryPersonData['injury_person_type']) == 2) {

                    $inj_person_arr[] = decryptId($injuryPersonData['injury_person_id']);
                } elseif (decryptId($injuryPersonData['injury_person_type']) == 3) {
                    $inj_person_arr[] = $injuryPersonData['injury_person_name'];
                }

                $injdata = [
                    'injury_id' => $saveinjuryData->id,
                    'status' => 'Y',
                ];
                if (decryptId($injuryPersonData['injury_person_type']) == 1 || decryptId($injuryPersonData['injury_person_type']) == 2) {

                    $updtinjBody = $IncidentBodyParts->where(['injury_person_id' => $saveinjuryData->injury_person_id, 'status' => 'T'])->update($injdata);
                } elseif (decryptId($injuryPersonData['injury_person_type']) == 3) {
                    $updtinjBody = $IncidentBodyParts->where(['injury_person_name' => $saveinjuryData->injury_person_name, 'status' => 'T'])->update($injdata);
                }
            }
        }
        $IncidentBodyParts->updateStatusForIncident(decryptId($incident_id), $inj_person_arr);
    }

    public function deleterecord($id)
    {

        $update_data = array(
            'status' => 0,
            'trash' => 'YES',
        );

        return $this->where('id', $id)->update($update_data);
    }

    public function getBodypartsInjuryPerson($id)
    {
        return $this->select(
            'ims_accident_investigation_injury.*',
            DB::raw('COALESCE(masters_employee.emp_name, masters_work.emp_name) as emp_name'),
            DB::raw('COALESCE(masters_employee.emp_id, masters_work.emp_id) as emp_id'),
            DB::raw('COALESCE(masters_employee.designation, masters_work.designation) as designation'),
            'ims_accident_body_parts.imgMapdata',
            'ims_accident_body_parts.body_part_image'
        )
            ->leftJoin('masters_employee', function ($join) {
                $join->on('ims_accident_investigation_injury.injury_person_id', '=', 'masters_employee.id')
                    ->where('ims_accident_investigation_injury.injury_person_type', '=', 1);
            })
            ->leftJoin('masters_work', function ($join) {
                $join->on('ims_accident_investigation_injury.injury_person_id', '=', 'masters_work.id')
                    ->where('ims_accident_investigation_injury.injury_person_type', '=', 2);
            })
            ->leftJoin('ims_accident_body_parts', 'ims_accident_investigation_injury.id', '=', 'ims_accident_body_parts.injury_id')
            ->where('accident_investigation_id', $id)
            ->get();
    }
}
