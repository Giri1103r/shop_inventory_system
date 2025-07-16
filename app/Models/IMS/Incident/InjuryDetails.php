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

    public function store($incident_id, $random_id)
    {
        $request = request();
        $IncidentBodyParts = new IncidentBodyParts();
        $injuryPerson = $request->input('injury_person');
        $inj_person_arr = [];

        if (!empty($injuryPerson) && is_array($injuryPerson)) {
            // dd($injuryPerson);
            foreach ($injuryPerson as $injuryPersonData) {

                if (!empty($injuryPersonData['injury_detail_id'])) {
                    $exists = $this->where('id', $injuryPersonData['injury_detail_id'])->exists();
                    // dd($exists);
                    if ($exists) {
                        continue;
                    }
                }
                try {
                    $personType = decryptId($injuryPersonData['injury_person_type'] ?? '');

                    $insert_array = [
                        'incident_id' => $incident_id,
                        'injury_person_type' => $personType,
                        'injury_person_id' => ($personType == 3) ? 0 : decryptId($injuryPersonData['injury_person_id'] ?? 0),
                        'injury_person_name' => $injuryPersonData['injury_person_name'] ?? '',
                        'injury_person_designation' => $injuryPersonData['injury_person_designation'] ?? null,
                        'injury_person_department_id' => $injuryPersonData['injury_person_department_id'] ?? null,
                        'nature_of_injury' => isset($injuryPersonData['nature_of_injury']) ? decryptId($injuryPersonData['nature_of_injury']) : null,
                        'created_by' => Auth::id()
                    ];

                    $saveinjuryData = $this->create($insert_array);

                    // Track for updateStatusForIncident
                    if ($personType == 1 || $personType == 2) {
                        $inj_person_arr[] = decryptId($injuryPersonData['injury_person_id'] ?? 0);
                    } elseif ($personType == 3) {
                        $inj_person_arr[] = $injuryPersonData['injury_person_name'] ?? '';
                    }

                    // Update temporary records
                    $injdata = [
                        'incident_id' => $incident_id,
                        'injury_id' => $saveinjuryData->id,
                        'status' => 'Y',
                    ];

                    $updateConditions = [
                        'random_id' => $random_id,
                        'injured_person_type' => $personType,
                        'status' => 'T'
                    ];

                    if ($personType == 1 || $personType == 2) {
                        $updateConditions['injury_person_id'] = $saveinjuryData->injury_person_id;
                    } else {
                        $updateConditions['injury_person_name'] = $saveinjuryData->injury_person_name;
                    }

                    $IncidentBodyParts->where($updateConditions)->update($injdata);
                } catch (\Exception $e) {
                    continue; // Skip if error occurs
                }
            }
        }

        if (!empty($inj_person_arr)) {
            $IncidentBodyParts->updateStatusForIncident(
                $random_id,
                $incident_id,
                $personType ?? null,
                $inj_person_arr
            );
        }
    }
    // public function storeinjuryApi($incident_id, $random_id)
    // {
    //     $request = request();
    //     $IncidentBodyParts = new IncidentBodyParts();
    //     $injuryPerson = $request->input('injuredPerson');
    //     $inj_person_arr = [];
    //     if (!empty($injuryPerson) && is_array($injuryPerson)) {

    //         foreach ($injuryPerson as $injuryPersonData) {

    //             if (!empty($injuryPersonData['injury_detail_id'])) {
    //                 $exists = $this->where('id', $injuryPersonData['injury_detail_id'])->exists();
    //                 // dd($exists);
    //                 if ($exists) {
    //                     continue;
    //                 }
    //             }
    //             try {
    //                 $personType = $injuryPersonData['injury_person_type'] ?? '';

    //                 $insert_array = [
    //                     'incident_id' => $incident_id,
    //                     'injury_person_type' => $personType,
    //                     'injury_person_id' => ($personType == 3) ? 0 : $injuryPersonData['injury_person_id'] ?? 0,
    //                     'injury_person_name' => $injuryPersonData['injury_person_name'] ?? '',
    //                     'injury_person_designation' => $injuryPersonData['injury_person_designation'] ?? null,
    //                     'injury_person_department_id' => $injuryPersonData['injury_person_department_id'] ?? null,
    //                     'nature_of_injury' => isset($injuryPersonData['nature_of_injury']) ? $injuryPersonData['nature_of_injury'] : null,
    //                     'created_by' => Auth::id()
    //                 ];
    //                 $saveinjuryData = $this->create($insert_array);

    //                 // Track for updateStatusForIncident
    //                 if ($personType == 1 || $personType == 2) {
    //                     $inj_person_arr[] = $injuryPersonData['injury_person_id'] ?? 0;
    //                 } elseif ($personType == 3) {
    //                     $inj_person_arr[] = $injuryPersonData['injury_person_name'] ?? '';
    //                 }

    //                 // Update temporary records
    //                 $injdata = [
    //                     'incident_id' => $incident_id,
    //                     'injury_id' => $saveinjuryData->id,
    //                     'status' => 'Y',
    //                 ];

    //                 $updateConditions = [
    //                     'random_id' => $random_id,
    //                     'injured_person_type' => $personType,
    //                     'status' => 'T'
    //                 ];

    //                 if ($personType == 1 || $personType == 2) {
    //                     $updateConditions['injury_person_id'] = $saveinjuryData->injury_person_id;
    //                 } else {
    //                     $updateConditions['injury_person_name'] = $saveinjuryData->injury_person_name;
    //                 }
    //                 $IncidentBodyParts->where($updateConditions)->update($injdata);
    //             } catch (\Exception $e) {
    //                 dd($e);
    //                 continue; // Skip if error occurs
    //             }
    //         }
    //     }

    //     if (!empty($inj_person_arr)) {
    //         $IncidentBodyParts->updateStatusForIncident(
    //             $random_id,
    //             $incident_id,
    //             $personType ?? null,
    //             $inj_person_arr
    //         );
    //     }
    // }
    public function storeinjuryApi($incident_id, $random_id)
    {
        $request = request();
        $IncidentBodyParts = new IncidentBodyParts();

        $injuryPerson = $request->input('injuredPerson');
        $inj_person_arr = [];

        if (!empty($injuryPerson) && is_array($injuryPerson)) {
            foreach ($injuryPerson as $injuryPersonData) {
                try {
                    // Check for existing detail (avoid duplicate creation)
                    if (!empty($injuryPersonData['injury_detail_id'])) {
                        if ($this->where('id', $injuryPersonData['injury_detail_id'])->exists()) {
                            continue;
                        }
                    }

                    // Determine person type
                    $personType = $injuryPersonData['injury_person_type'] ?? null;

                    // Build data for insert
                    $insert_array = [
                        'incident_id'                 => $incident_id,
                        'injury_person_type'          => $personType,
                        'injury_person_id'            => ($personType == 3) ? 0 : ($injuryPersonData['injury_person_id'] ?? 0),
                        'injury_person_name'          => ($personType == 3) ? ($injuryPersonData['injury_person_name'] ?? '') : '',
                        'injury_person_designation'   => $injuryPersonData['injury_person_designation'] ?? null,
                        'injury_person_department_id' => $injuryPersonData['injury_person_department_id'] ?? null,
                        'nature_of_injury'            => $injuryPersonData['nature_of_injury'] ?? null,
                        'created_by'                  => Auth::id(),
                    ];

                    // Save new Injury Person
                    $saveinjuryData = $this->create($insert_array);

                    // Track IDs or Names for exclusion in status update
                    if ($personType == 1 || $personType == 2) {
                        $inj_person_arr[] = $saveinjuryData->injury_person_id;
                    } elseif ($personType == 3) {
                        $inj_person_arr[] = $saveinjuryData->injury_person_name;
                    }

                    // Update IncidentBodyParts for this person
                    $injdata = [
                        'incident_id' => $incident_id,
                        'injury_id'   => $saveinjuryData->id,
                        'status'      => 'Y',
                    ];

                    $updateConditions = [
                        'random_id'           => $random_id,
                        'injured_person_type' => $personType,
                        'status'              => 'T',
                    ];

                    if ($personType == 1 || $personType == 2) {
                        $updateConditions['injury_person_id'] = $saveinjuryData->injury_person_id;
                    } elseif ($personType == 3) {
                        $updateConditions['injury_person_name'] = $saveinjuryData->injury_person_name;
                    }
                        // dd($updateConditions,$injdata);
                    $IncidentBodyParts->where($updateConditions)->update($injdata);
                } catch (\Exception $e) {
                    dd($e);
                    continue;
                }
            }

            // If we have any saved persons, mark all other TEMP rows as 'N'
            if (!empty($inj_person_arr)) {
                $IncidentBodyParts->updateStatusForIncident(
                    $random_id,
                    $incident_id,
                    $injuryPerson[0]['injury_person_type'] ?? null,  // Use type of first
                    $inj_person_arr
                );
            }
        }
    }

    public function deleterecord($incidentId, $injuryId)
    {

        $update_data = array(
            'trash' => 'YES',
        );

        return $this->where('id', $injuryId)->where('incident_id', $incidentId)->update($update_data);
    }

    public function getBodypartsInjuryPerson($id)
    {
        return $this->select(
            'ims_injury_details.*',
            DB::raw('COALESCE(masters_employee.emp_name, masters_work.emp_name) as emp_name'),
            DB::raw('COALESCE(masters_employee.emp_id, masters_work.emp_id) as emp_id'),
            DB::raw('COALESCE(masters_employee.designation, masters_work.designation) as designation'),
            'ims_incident_body_parts.imgMapdata',
            'ims_incident_body_parts.body_part_image'
        )
            ->leftJoin('masters_employee', function ($join) {
                $join->on('ims_injury_details.injury_person_id', '=', 'masters_employee.id')
                    ->where('ims_injury_details.injury_person_type', '=', 1);
            })
            ->leftJoin('masters_work', function ($join) {
                $join->on('ims_injury_details.injury_person_id', '=', 'masters_work.id')
                    ->where('ims_injury_details.injury_person_type', '=', 2);
            })
            ->leftJoin('ims_incident_body_parts', 'ims_injury_details.id', '=', 'ims_incident_body_parts.injury_id')
            ->where('ims_injury_details.incident_id', $id)
            ->where('ims_injury_details.trash', 'NO')
            ->get();
    }

    public function find_foreignkey($id)
    {

        $data = $this->select(
            'ims_injury_details.*',
            'ims_incident_body_parts.imgMapdata',
            'ims_incident_body_parts.body_part_image',

        )
            ->leftJoin('ims_incident_body_parts', 'ims_injury_details.id', '=', 'ims_incident_body_parts.injury_id')
            ->where('ims_injury_details.incident_id', $id)->where('ims_injury_details.trash', 'NO')->get();
        return $data;
    }
}
