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
        $documentFiles = $request->input('ppe_file');

        if ($documentFiles != null) {
            foreach ($documentFiles as $file) {
                $base64File = $file;
                $data = preg_replace('#^data:[\w/]+;base64,#i', '', $base64File);
                $decodedFile = base64_decode($data);

                // Determine file MIME type
                $finfo = finfo_open();
                $mimeType = finfo_buffer($finfo, $decodedFile, FILEINFO_MIME_TYPE);
                finfo_close($finfo);

                // Define valid MIME types and corresponding extensions
                $validFormats = [
                    'image/jpeg' => 'jpeg',
                    'image/jpg' => 'jpg',
                    'image/png' => 'png',
                    'application/pdf' => 'pdf',
                    'application/msword' => 'doc',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'
                ];

                // Check if the MIME type is valid
                if (!array_key_exists($mimeType, $validFormats)) {
                    continue;
                }

                $extension = $validFormats[$mimeType];

                // Define upload folder
                $folderPath = public_path('uploads/ppe_ExemptionFiles/' . $ppeexemption->id);

                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, 0755, true);
                }

                // Generate a unique file name
                $filenewname = time() . Str::random(10) . '.' . $extension;
                $uploadpath = $folderPath . "/" . $filenewname;

                // Save the file to the server
                file_put_contents($uploadpath, $decodedFile);

                // Get file size
                $fileSize = filesize($uploadpath);

                // Save file metadata in the database
                $insert_data = [
                    'reference_id' => $ppeexemption->id,
                    'uauc_ca_id' => NULL,
                    'file_type' => 1,
                    'file_name' => $filenewname,
                    'file_orgname' => $filenewname,
                    'file_path' => 'uploads/ppe_ExemptionFiles/' . $ppeexemption->id . "/" . $filenewname,
                    'file_size' => $fileSize,
                    'file_extension' => $extension,
                    'created_by' => Auth::id(),
                    'trash' => 'NO'
                ];

                $this->create($insert_data)->id;
            }
        }
    }



}
