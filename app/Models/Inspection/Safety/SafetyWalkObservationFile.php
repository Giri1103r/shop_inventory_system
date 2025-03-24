<?php

namespace App\Models\Inspection\Safety;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;

class SafetyWalkObservationFile extends Model
{
    protected $table = 'inspection_safety_walk_observation_files';

    protected $fillable = [
        'id',
        'safety_walk_observation_id',
        'file_type',
        'file_path',
        'file_name',
        'file_orgname',
        'file_extension',
        'created_by',
        'updated_by',

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];


    public function store($id, $index, $image)
    {
        $request = Request();
        if ($request->has('checklist_file')) {

            $upload_path = 'public/uploads/inspection/safetywalkobservation';

            foreach($image as $imageIndex => $value){

                if($index == $imageIndex){
                    if (!File::exists($upload_path)) {
                        File::makeDirectory($upload_path, 0777, true, true);
                    }
                    $file_name = time() . Str::random(10) . '.' . $value->getClientOriginalExtension();
                    $value->move($upload_path, $file_name);
                    $url = $upload_path . '/' . $file_name;

                    $OriginalfileName = $value->getClientOriginalName();
                    $fileExt = $value->getClientOriginalExtension();

                    $insert_array = [
                        'safety_walk_observation_id' => $id,
                        'file_path' => $url,
                        'file_name' => $file_name,
                        'file_orgname' => $OriginalfileName,
                        'file_extension' => $fileExt,
                        'created_by' => Auth::id(),
                    ];
                    $this->create($insert_array);
                }
                continue;
            }
        }
    }
}
