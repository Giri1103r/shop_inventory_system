<?php

namespace App\Models\Inspection\GembaWalk;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
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
        'date_of_observation',
        'observation_type_id',
        'description',
        'hazard',
        'capa',
        'date_of_compliance',
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
                $data = [
                    'gemba_walk_id' => $gembaWalk_id,
                    'location_id' => decryptId($walk['location_id']),
                    'unit_id' => decryptId($walk['unit_id']),
                    'date_of_observation' => DBdateformat($walk['date_of_observation']),
                    'observation_type_id' => $walk['observation_type'],
                    'description' => $walk['checklist_description'],
                    'hazard' => $walk['hazard'],
                    'capa' => $walk['checklist_capa'],
                    'date_of_compliance' => DBdateformat($walk['date_of_compliance']),
                    'responsibility_id' => decryptId($walk['responsibility_id']),
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
                    $path = $uploadpath . "/" . $filenewname;
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
            }

            return response()->json(['message' => 'Data stored successfully'], 201);
        }

        return response()->json(['error' => 'Invalid data'], 400);
    }

    public function gembaWalkPotentialCount()
    {
        $request = request();

        // Step 1: Base query
        $query = $this
            ->select(
                'masters_unit.unit_name',
                'inspection_gemba_walk_checklist.observation_type_id as observation_type_name',
                DB::raw('COUNT(*) as total')
            )
            ->leftJoin('masters_unit', 'masters_unit.id', '=', 'inspection_gemba_walk_checklist.unit_id');

        // Step 2: Date filter
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

        // Step 3: Role-based filtering
        if (!CheckUserRole(ROLE_SUPERADMIN) && !CheckUserRole(ROLE_ADMIN) && !CheckUserRole(ROLE_EHS_HEAD)) {
            if (Auth::user()->role == ROLE_USER) {
                $query->where('inspection_gemba_walk_checklist.created_by', Auth::id());
            }
        }

        $results = $query
            ->groupBy('masters_unit.unit_name', 'inspection_gemba_walk_checklist.observation_type_id')
            ->get();

        $finalData = [];
        $allObservationTypes = [];

        foreach ($results as $row) {
            $unit = $row->unit_name;
            $type = getObservationType($row->observation_type_name);
            $count = $row->total;

            $allObservationTypes[$type] = true;

            if (!isset($finalData[$unit])) {
                $finalData[$unit] = ['unit_name' => $unit];
            }

            $finalData[$unit][$type] = $count;
        }

        $allTypes = array_keys($allObservationTypes);
        foreach ($finalData as &$unitData) {
            foreach ($allTypes as $type) {
                if (!isset($unitData[$type])) {
                    $unitData[$type] = 0;
                }
            }
        }

        $finalData = array_values($finalData);
        return $finalData;
    }
}
