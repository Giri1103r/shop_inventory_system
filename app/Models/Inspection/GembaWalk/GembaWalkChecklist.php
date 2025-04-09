<?php

namespace App\Models\Inspection\GembaWalk;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;


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
                        'file_type'=>3,
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


}
