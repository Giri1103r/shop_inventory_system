<?php

namespace App\Models\Inspection\Safety;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Master\Employee;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Google\Rpc\Context\AttributeContext\Request;

class SignatureUpload extends Model
{
    protected $table = 'inspection_safety_signatureupload';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'type',
        'emp_id',
        'inspection_id',
        'file_path',
        'file_name',
        'file_orgname',
        'file_extension',
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

    public function signatureUpload($type, $id)
    {
        try {
            $request = Request();
            $file = $request->file('signature_image');
            if ($request->has('signature_image')) {
                $image = $request->file('signature_image');
                $upload_path = 'public/uploads/inspection/safety/signatureupload';

                if (!File::exists($upload_path)) {
                    File::makeDirectory($upload_path, 0777, true, true);
                }
                $file_name = time() . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->move($upload_path, $file_name);
                $url = $upload_path . '/' . $file_name;

                $OriginalfileName = $image->getClientOriginalName();
                $fileExt = $image->getClientOriginalExtension();

                $insert_array = [
                    'emp_id' => Auth::id(),
                    'inspection_id' => $id,
                    'type' => $type,
                    'file_path' => $url,
                    'file_name' => $file_name,
                    'file_orgname' => $OriginalfileName,
                    'file_extension' => $fileExt,
                    'created_by' => Auth::id(),
                ];
                $this->create($insert_array);
            }
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function signatureUpload_api($ppeexemption)
    {
        $request = request();
        if ($request->has('signature_image')) {
            $base64File = $request->input('signature_image');
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
                Log::error("Unsupported Base64 file format.");
                return;
            }

            $base64File = preg_replace('#^data:(.*);base64,#i', '', $base64File);
            $fileData = base64_decode($base64File);

            if ($fileData === false) {
                Log::error("Base64 decoding failed.");
                return;
            }

            $uploadDir = public_path('uploads/inspection/safety/signatureupload');

            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0777, true, true);
            }

            $fileName = time() . Str::random(10) . '.' . $extension;
            $fileFullPath = $uploadDir . '/' . $fileName;

            file_put_contents($fileFullPath, $fileData);

            $this->create([
                'reference_id'   => $ppeexemption->id,
                'file_type'      => 1,
                'file_name'      => $fileName,
                'file_orgname'   => $fileName,
                'file_path'      => 'public/uploads/inspection/safety/signatureupload/' . $fileName,
                'file_extension' => $extension,
                'created_by'     => Auth::id(),
                'trash'          => 'NO',
            ]);
        }else{
            dd(2);
        }
    }
}
