<?php

namespace App\Models\Inspection\GembaWalk;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class GembaWalkChecklist extends Model
{
    use  HasFactory;
    protected $table = 'inspection_gemba_walk_checklist';

    protected $primaryKey = 'id';

    protected $fillable = [
        'gemba_walk_id',
        'location_id',
        'unit_id',
        'department_id',
        'exact_location',
        'date_of_observation',
        'observation_type_id',
        'time',
        'risk_category',
        'description',
        'hazard',
        'capa',
        // 'date_of_compliance',
        'responsibility_id',
        'gemba_walk_checklist_status',
        'remark',
        // 'observation',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];


    public function store($gembaWalk_id)
    {
        $request = request();
        $gembaWalkData = $request->input('gemba_walk');

        if (!empty($gembaWalkData) && is_array($gembaWalkData)) {
            foreach ($gembaWalkData as $index => $walk) {


                // for Hazard
                $decryptedHazards = array_map(function ($id) {
                    return decryptId($id);
                }, $walk['hazard']);
                $hazardIds = implode(',', $decryptedHazards);

                // for responsible person

                if (!empty($walk['responsible_person_id'])) {
                    $decryptedObserverPerson = array_map(function ($id) {
                        return ($id); // You can add decryption here if needed
                    }, $walk['responsible_person_id']);

                    $ObserversIds = implode(',', $decryptedObserverPerson);
                } else {
                    $ObserversIds = '';
                }

                $data = [
                    'gemba_walk_id' => $gembaWalk_id,
                    'location_id' => decryptId($walk['location_id']),
                    'unit_id' => decryptId($walk['unit_id']),
                    'department_id' => decryptId($walk['department_id']),
                    'exact_location' => $walk['exact_location'],
                    'date_of_observation' => DBdateformat($walk['date_of_observation']),
                    'risk_category' => decryptId($walk['risk_category']),
                    'observation_type_id' => $walk['observation_type'],
                    'description' => $walk['checklist_description'],
                    'hazard' =>  $hazardIds,
                    'capa' => $walk['checklist_capa'],
                    'time' => $walk['time'],
                    // 'date_of_compliance' => DBdateformat($walk['date_of_compliance']),
                    'responsibility_id' =>  $ObserversIds,
                    'gemba_walk_checklist_status' => $walk['current_status'],
                    'remark' => $walk['checklist_remark'],
                    // 'observation' => json_encode($walk['checklist_observation']),
                    'created_by' => Auth::id()
                ];

                $gembaWalkChecklist = $this->create($data);

                if ($request->hasFile("gemba_walk.$index.evidence")) {
                    $intendent = $request->file("gemba_walk.$index.evidence");

                    $uploadpath = 'uploads/gembaWalk/' . $gembaWalkChecklist->id;
                    $folderPath = public_path($uploadpath);

                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, 0755, true);
                    }

                    $filenewname = time() . Str::random(10) . '.' . $intendent->getClientOriginalExtension();
                    $fileName = $intendent->getClientOriginalName();
                    $fileSize = $intendent->getSize();
                    $fileExt = $intendent->getClientOriginalExtension();

                    $intendent->move($folderPath, $filenewname);
                    $path = "public/" . $uploadpath . "/" . $filenewname;
                    $user_id = Auth::id();

                    GembaWalkChecklistFile::create([
                        'gemba_walk_id' => $gembaWalk_id,
                        'gemba_walk_checklist_id' => $gembaWalkChecklist->id,
                        'file_type' => 3,
                        'file_name' => $filenewname,
                        'file_orgname' => $fileName,
                        'file_path' => $path,
                        'file_size' => $fileSize,
                        'file_extension' => $fileExt,
                        'created_by' => $user_id,
                    ]);
                }
                if ($request->hasFile("gemba_walk.$index.closing_evidence")) {
                    $intendent = $request->file("gemba_walk.$index.closing_evidence");

                    $uploadpath = 'uploads/gembaWalk/' . $gembaWalkChecklist->id;
                    $folderPath = public_path($uploadpath);

                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, 0755, true);
                    }

                    $filenewname = time() . Str::random(10) . '.' . $intendent->getClientOriginalExtension();
                    $fileName = $intendent->getClientOriginalName();
                    $fileSize = $intendent->getSize();
                    $fileExt = $intendent->getClientOriginalExtension();

                    $intendent->move($folderPath, $filenewname);
                    $path = "public/" . $uploadpath . "/" . $filenewname;
                    $user_id = Auth::id();

                    GembaWalkChecklistFile::create([
                        'gemba_walk_id' => $gembaWalk_id,
                        'gemba_walk_checklist_id' => $gembaWalkChecklist->id,
                        'file_type' => 4,
                        'file_name' => $filenewname,
                        'file_orgname' => $fileName,
                        'file_path' => $path,
                        'file_size' => $fileSize,
                        'file_extension' => $fileExt,
                        'created_by' => $user_id,
                    ]);
                }
            }

            return response()->json(['message' => 'Data stored successfully'], 201);
        }

        return response()->json(['error' => 'Invalid data'], 400);
    }
    public function selectMail($id)
    {
        return $this->where('gemba_walk_id', $id)->where('status', 1)->first();
    }
 public function gembaWalkPotentialCount()
{
    $request = request();

    $query = $this
        ->select(
            'masters_unit.unit_name',
            'inspection_gemba_walk_checklist.unit_id',
            'inspection_gemba_walk_checklist.observation_type_id',
            DB::raw('COUNT(*) as total')
        )
        ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_gemba_walk_checklist.unit_id')
        ->where('inspection_gemba_walk_checklist.status',1);

    if ($request->Fromdate && $request->Todate) {
        $query->whereBetween('inspection_gemba_walk_checklist.created_at', [
            DBdateformat($request->Fromdate),
            DBdateformat($request->Todate)
        ]);
    } elseif ($request->Fromdate) {
        $query->where('inspection_gemba_walk_checklist.created_at', '>=', DBdateformat($request->Fromdate));
    } elseif ($request->Todate) {
        $query->where('inspection_gemba_walk_checklist.created_at', '<=', DBdateformat($request->Todate));
    }

    $results = $query
        ->groupBy('masters_unit.unit_name', 'inspection_gemba_walk_checklist.observation_type_id', 'inspection_gemba_walk_checklist.unit_id')
        ->get();

    $finalData = [];
    $observationLabels = [];

    foreach ($results as $row) {
        $unit = $row->unit_name;
        $unit_id = $row->unit_id;
        $typeId = $row->observation_type_id;
        $label = getObservationType($typeId); // returns "Unsafe Act" or "Unsafe Condition"
        $count = $row->total;

        $observationLabels[$typeId] = $label;

        if (!isset($finalData[$unit])) {
            $finalData[$unit] = [
                'unit_name' => $unit,
                'unit_id' => $unit_id,
                'counts' => [],
                'observation_labels' => $observationLabels
            ];
        }

        $finalData[$unit]['counts'][$typeId] = $count;
    }

    // Ensure missing observation types are set to 0
    foreach ($finalData as &$unitData) {
        foreach ($observationLabels as $typeId => $label) {
            if (!isset($unitData['counts'][$typeId])) {
                $unitData['counts'][$typeId] = 0;
            }
        }
        $unitData['observation_labels'] = $observationLabels;
    }

    return array_values($finalData);
}

    public function DailyObservationMonthCount()
    {
        $request = request();

        $query = $this
            ->select(
                'masters_unit.unit_name',
                DB::raw("SUM(CASE WHEN inspection_gemba_walk_checklist.checklist_observation_status = 1 THEN 1 ELSE 0 END) as open"),
                DB::raw("SUM(CASE WHEN inspection_gemba_walk_checklist.checklist_observation_status = 5 THEN 1 ELSE 0 END) as closed"),
                DB::raw("COUNT(*) as total")
            )
            ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_gemba_walk_checklist.unit_id');

        if ($request->Fromdate && $request->Todate) {
            $query->whereBetween('inspection_gemba_walk_checklist.created_at', [
                DBdateformat($request->Fromdate),
                DBdateformat($request->Todate)
            ]);
        } elseif ($request->Fromdate) {
            $query->where('inspection_gemba_walk_checklist.created_at', '>=', DBdateformat($request->Fromdate));
        } elseif ($request->Todate) {
            $query->where('inspection_gemba_walk_checklist.created_at', '<=', DBdateformat($request->Todate));
        }

        if (!CheckUserRole(ROLE_SUPERADMIN) && !CheckUserRole(ROLE_ADMIN) && !CheckUserRole(ROLE_EHS_HEAD)) {
            if (Auth::user()->role == ROLE_USER) {
                $query->where('inspection_gemba_walk_checklist.created_by', Auth::id());
            }
        }
        $results = $query->groupBy('masters_unit.unit_name')->get();

        return $results;
    }


    public function storeApi($gembaWalk_id)
    {
        $request = request();
        $gembaWalkData = $request->input('gemba_walk');

        if (!empty($gembaWalkData) && is_array($gembaWalkData)) {
            $insertedChecklists = [];

            foreach ($gembaWalkData as $index => $walk) {

                $decryptedHazards = array_map(function ($id) {
                    return ($id);
                }, $walk['hazard']);
                $hazardIds = implode(',', $decryptedHazards);

                // for responsible person

                if (!empty($walk['responsible_person_id'])) {
                    $decryptedObserverPerson = array_map(function ($id) {
                        return ($id); // You can add decryption here if needed
                    }, $walk['responsible_person_id']);

                    $ObserversIds = implode(',', $decryptedObserverPerson);
                } else {
                    $ObserversIds = '';
                }
                $data = [
                    'gemba_walk_id' => $gembaWalk_id,
                    'location_id' => $walk['location_id'],
                    'unit_id' => $walk['unit_id'],
                    'department_id' => $walk['department_id'],
                    'exact_location' => $walk['exact_location'],
                    'date_of_observation' => DBdateformat($walk['date_of_observation']),
                    'risk_category' => $walk['risk_category'],
                    'observation_type_id' => $walk['observation_type'],
                    'description' => $walk['checklist_description'],
                    'hazard' =>  $hazardIds,
                    'capa' => $walk['checklist_capa'],
                    'time' => $walk['time'],
                    'responsibility_id' =>  $ObserversIds,
                    'gemba_walk_checklist_status' => $walk['current_status'],
                    'remark' => $walk['checklist_remark'],
                    'created_by' => Auth::id()
                ];

                $gembaWalkChecklist = $this->create($data);

                // Handle Base64 file upload (evidence)
                if (!empty($walk['evidence']) && is_string($walk['evidence'])) {
                    $base64File = $walk['evidence'];
                    $extension = null;

                    // Detect file extension
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64File, $matches)) {
                        $extension = $matches[1];
                    } elseif (preg_match('/^data:application\/pdf;base64,/', $base64File)) {
                        $extension = 'pdf';
                    } elseif (preg_match('/^data:application\/msword;base64,/', $base64File)) {
                        $extension = 'doc';
                    } elseif (preg_match('/^data:application\/vnd\.openxmlformats-officedocument\.wordprocessingml\.document;base64,/', $base64File)) {
                        $extension = 'docx';
                    } elseif (preg_match('/^data:application\/vnd\.ms-excel;base64,/', $base64File)) {
                        $extension = 'xls';
                    } elseif (preg_match('/^data:application\/vnd\.openxmlformats-officedocument\.spreadsheetml\.sheet;base64,/', $base64File)) {
                        $extension = 'xlsx';
                    } else {
                        Log::error("Unsupported Base64 file format.");
                        continue;
                    }

                    // Decode file
                    $base64Data = preg_replace('#^data:.*;base64,#', '', $base64File);
                    $fileData = base64_decode($base64Data);

                    if ($fileData === false) {
                        Log::error("Base64 decoding failed.");
                        continue;
                    }

                    // Create directory
                    $uploadpath = 'uploads/gembaWalk/' . $gembaWalkChecklist->id;
                    $folderPath = public_path($uploadpath);
                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, 0755, true);
                    }

                    // Save file
                    $filenewname = time() . Str::random(10) . '.' . $extension;
                    $fullFilePath = $folderPath . '/' . $filenewname;
                    $path = "public/" . $uploadpath . "/" . $filenewname;
                    file_put_contents($fullFilePath, $fileData);

                    // Store file info in DB
                    GembaWalkChecklistFile::create([
                        'gemba_walk_id' => $gembaWalk_id,
                        'gemba_walk_checklist_id' => $gembaWalkChecklist->id,
                        'file_type' => 3,
                        'file_name' => $filenewname,
                        'file_orgname' => $filenewname,
                        'file_path' => $path,
                        'file_size' => strlen($fileData),
                        'file_extension' => $extension,
                        'created_by' => Auth::id(),
                    ]);
                }


                // Handle Base64 file upload (closing_evidence)
                if (!empty($walk['closing_evidence']) && is_string($walk['closing_evidence'])) {
                    $base64File = $walk['closing_evidence'];
                    $extension = null;

                    // Detect file extension
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64File, $matches)) {
                        $extension = $matches[1];
                    } elseif (preg_match('/^data:application\/pdf;base64,/', $base64File)) {
                        $extension = 'pdf';
                    } elseif (preg_match('/^data:application\/msword;base64,/', $base64File)) {
                        $extension = 'doc';
                    } elseif (preg_match('/^data:application\/vnd\.openxmlformats-officedocument\.wordprocessingml\.document;base64,/', $base64File)) {
                        $extension = 'docx';
                    } elseif (preg_match('/^data:application\/vnd\.ms-excel;base64,/', $base64File)) {
                        $extension = 'xls';
                    } elseif (preg_match('/^data:application\/vnd\.openxmlformats-officedocument\.spreadsheetml\.sheet;base64,/', $base64File)) {
                        $extension = 'xlsx';
                    } else {
                        Log::error("Unsupported Base64 file format.");
                        continue;
                    }

                    // Decode file
                    $base64Data = preg_replace('#^data:.*;base64,#', '', $base64File);
                    $fileData = base64_decode($base64Data);

                    if ($fileData === false) {
                        Log::error("Base64 decoding failed.");
                        continue;
                    }

                    // Create directory
                    $uploadpath = 'uploads/gembaWalk/' . $gembaWalkChecklist->id;
                    $folderPath = public_path($uploadpath);
                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, 0755, true);
                    }

                    // Save file
                    $filenewname = time() . Str::random(10) . '.' . $extension;
                    $fullFilePath = $folderPath . '/' . $filenewname;
                    $path = "public/" . $uploadpath . "/" . $filenewname;
                    file_put_contents($fullFilePath, $fileData);

                    // Store file info in DB
                    GembaWalkChecklistFile::create([
                        'gemba_walk_id' => $gembaWalk_id,
                        'gemba_walk_checklist_id' => $gembaWalkChecklist->id,
                        'file_type' => 4,
                        'file_name' => $filenewname,
                        'file_orgname' => $filenewname,
                        'file_path' => $path,
                        'file_size' => strlen($fileData),
                        'file_extension' => $extension,
                        'created_by' => Auth::id(),
                    ]);
                }

                $insertedChecklists[] = $gembaWalkChecklist;
            }

            return response()->json([
                'message' => 'Data stored successfully',
                'checklists' => $insertedChecklists
            ], 201);
        }

        return response()->json(['error' => 'Invalid data'], 400);
    }

    public function selectOne($id)
    {
        return $this->where('gemba_walk_id', $id)->first();
    }
}
