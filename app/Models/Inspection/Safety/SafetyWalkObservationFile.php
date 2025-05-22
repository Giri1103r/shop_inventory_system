<?php

namespace App\Models\Inspection\Safety;

use App\Scopes\TrashScope;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
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

            foreach ($image as $imageIndex => $value) {

                if ($index == $imageIndex) {
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

    public function store_api($id, $checklistFiles)
    {
        $request = request();

        $uploadDir = public_path('public/uploads/inspection/safetywalkobservation');

        if (!File::exists($uploadDir)) {
            File::makeDirectory($uploadDir, 0777, true, true);
        }


        $base64File = $checklistFiles;
        $extension = null;

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
        } elseif (preg_match('/^data:application\/octet-stream;base64,/', $base64File)) {
            $extension = 'bin';
        } else {
            Log::error("Unsupported Base64 file format");
            return;
        }


        $cleanBase64 = preg_replace('#^data:(.*);base64,#i', '', $base64File);
        $fileData = base64_decode($cleanBase64);



        $fileName = time() . Str::random(10) . '.' . $extension;
        $filePath = $uploadDir . '/' . $fileName;

        file_put_contents($filePath, $fileData);

        $this->create([
            'safety_walk_observation_id' => $id,
            'file_name'      => $fileName,
            'file_orgname'   => $fileName,
            'file_extension' => $extension,
            'file_path'      => 'public/public/uploads/inspection/safetywalkobservation/' . $fileName,
            'created_by'     => Auth::id(),
            'trash'          => 'NO',
        ]);
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_safety_walk_observation_files'));
    }
}
