<?php

namespace App\Models\Ppemanagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PpeFiles extends Model
{
    use  HasFactory;


    protected $table = 'ppe_management_files';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'reference_id',
        'file_type',
        'file_name',
        'file_orgname',
        'file_path',
        'file_size',
        'file_extension',
        'created_by',
        'status',
        'trash',
        'updated_by',
        'created_at',
        'updated_at'

    ];
    public function store($ppeexemption)
    {
        $request = request();
        $documentFiles = $request->file('ppe_file');

        if (!empty($documentFiles)) {
            foreach ($documentFiles as $groupIndex => $ExemptionFiles) {
                foreach ($ExemptionFiles as $index => $Files) {
                    if ($Files) {
                        $uploadPath = 'public/uploads/ppe_ExemptionFiles/' . $ppeexemption->id;
                        $folderPath = 'public/uploads/ppe_ExemptionFiles/' . $ppeexemption->id;

                        if (!File::exists($folderPath)) {
                            File::makeDirectory($folderPath, 0755, true);
                        }

                        $filenewname = time() . Str::random(10) . '.' . $Files->getClientOriginalExtension();
                        $fileName = $Files->getClientOriginalName();
                        $fileSize = $Files->getSize();
                        $fileExt = $Files->getClientOriginalExtension();

                        $Files->move($folderPath, $filenewname);

                        $path = 'public/uploads/ppe_ExemptionFiles/' . $ppeexemption->id . "/" . $filenewname;
                        $userId = Auth::id();

                        $insertData = [
                            'reference_id' => $ppeexemption->id,
                            'file_type' => 1,
                            'file_name' => $filenewname,
                            'file_orgname' => $fileName,
                            'file_path' => $path,
                            'file_size' => $fileSize,
                            'file_extension' => $fileExt,
                            'created_by' => $userId,
                            'trash' => 'NO',
                        ];

                        $this->create($insertData);

                        Log::info("File saved: " . $filenewname);
                    } else {
                        Log::warning("Uploaded data is not a valid file: " . json_encode($Files));
                    }
                }
            }
        }
    }

    public function getExemptionFile($id){

         return $this->where('reference_id',$id)->where('file_type',1)->where('trash','NO')->get();
    }



    public function store_api($ppeexemption)
    {
        $request = request();
        $upload_path = 'public/uploads/ppe_ExemptionFiles/' . $ppeexemption->id;

        if ($request->has('ppe_file') && is_array($request->ppe_file)) {
            foreach ($request->ppe_file as $base64File) {
                if (!empty($base64File)) {
                    $extension = null;
                    $fileType = null;

                    // Match images
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64File, $matches)) {
                        $fileType = 'image';
                        $extension = $matches[1];
                    }
                    // Match PDFs
                    elseif (preg_match('/^data:(application\/pdf|@file\/pdf);base64,/', $base64File)) {
                        $fileType = 'pdf';
                        $extension = 'pdf';
                    }
                    // Match Word DOC
                    elseif (preg_match('/^data:(application\/msword|@file\/msword);base64,/', $base64File)) {
                        $fileType = 'doc';
                        $extension = 'doc';
                    }
                    // Match Word DOCX
                    elseif (preg_match('/^data:(application\/vnd\.openxmlformats-officedocument\.wordprocessingml\.document|@file\/vnd\.openxmlformats-officedocument\.wordprocessingml\.document);base64,/', $base64File)) {
                        $fileType = 'docx';
                        $extension = 'docx';
                    }
                    // Match Excel XLS
                    elseif (preg_match('/^data:(application\/vnd\.ms-excel|@file\/msexcel);base64,/', $base64File)) {
                        $fileType = 'xls';
                        $extension = 'xls';
                    }
                    // Match Excel XLSX
                    elseif (preg_match('/^data:(application\/vnd\.openxmlformats-officedocument\.spreadsheetml\.sheet|@file\/vnd\.openxmlformats-officedocument\.spreadsheetml\.sheet);base64,/', $base64File)) {
                        $fileType = 'xlsx';
                        $extension = 'xlsx';
                    }
                    // Fallback for generic octet-stream (e.g. unknown file types)
                    elseif (preg_match('/^data:application\/octet-stream;base64,/', $base64File)) {
                        $fileType = 'binary';
                        $extension = 'bin'; // or 'dat', depending on context
                    } else {
                        Log::error("Unsupported Base64 file format.");
                        continue;
                    }

                    // Remove base64 header
                    $base64File = preg_replace('#^data:(.*);base64,#i', '', $base64File);
                    $fileData = base64_decode($base64File);

                    if ($fileData === false) {
                        Log::error("Base64 decoding failed.");
                        continue;
                    }

                    // Ensure directory exists
                    if (!File::exists($upload_path)) {
                        File::makeDirectory($upload_path, 0777, true, true);
                    }

                    // Unique filename
                    $file_name = time() . Str::random(10) . '.' . $extension;
                    $file_path = $upload_path . '/' . $file_name;

                    // Save file
                    file_put_contents($file_path, $fileData);

                    // Save to DB
                    $insert_array = [
                        'reference_id' => $ppeexemption->id,
                        'file_type' => 1,
                        'file_name' => $file_name,
                        'file_orgname' => $file_name,
                        'file_path' => 'public/uploads/ppe_ExemptionFiles/' . $ppeexemption->id . '/' . $file_name,
                        'file_extension' => $extension,
                        'created_by' => Auth::id(),
                        'trash' => 'NO',
                    ];

                    $this->create($insert_array);
                }
            }
        }
    }






}
