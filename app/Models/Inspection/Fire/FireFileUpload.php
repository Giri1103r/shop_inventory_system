<?php

namespace App\Models\Inspection\Fire;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;

class FireFileUpload extends Model
{
    protected $table = 'inspection_fire_files';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'type',
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

    public function file_upload($type, $inspection_id)
    {
        try {
            $id = Auth::id();
            $request = Request();
            $file = $request->file('device_image');
            if ($request->has('device_image')) {
                $image = $request->file('device_image');
                $upload_path = 'public/uploads/inspection/fire/' . GetTypeName($type);

                if (!File::exists($upload_path)) {
                    File::makeDirectory($upload_path, 0777, true, true);
                }
                $file_name = time() . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->move($upload_path, $file_name);
                $url = $upload_path . '/' . $file_name;

                $OriginalfileName = $image->getClientOriginalName();
                $fileExt = $image->getClientOriginalExtension();

                $insert_array = [
                    'inspection_id' => $inspection_id,
                    'type' => $type,
                    'file_path' => $url,
                    'file_name' => $file_name,
                    'file_orgname' => $OriginalfileName,
                    'file_extension' => $fileExt,
                    'created_by' => Auth::id(),
                ];
                return $this->create($insert_array);
            }
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function file_upload_api($type, $inspection_id)
    {
        $request = request();
        $upload_path = 'public/uploads/inspection/fire/' . GetTypeName($type);

        if ($request->has('device_image')) {
            $base64File = $request->device_image;
            if (!empty($base64File)) {
                $extension = null;
                $fileType = null;

                // Match known file types by base64 header
                if (preg_match('/^data:image\/(\w+);base64,/', $base64File, $matches)) {
                    $fileType = 'image';
                    $extension = $matches[1];
                } elseif (preg_match('/^data:(application\/pdf|@file\/pdf);base64,/', $base64File)) {
                    $fileType = 'pdf';
                    $extension = 'pdf';
                } elseif (preg_match('/^data:(application\/msword|@file\/msword);base64,/', $base64File)) {
                    $fileType = 'doc';
                    $extension = 'doc';
                } elseif (preg_match('/^data:(application\/vnd\.openxmlformats-officedocument\.wordprocessingml\.document|@file\/vnd\.openxmlformats-officedocument\.wordprocessingml\.document);base64,/', $base64File)) {
                    $fileType = 'docx';
                    $extension = 'docx';
                } elseif (preg_match('/^data:(application\/vnd\.ms-excel|@file\/msexcel);base64,/', $base64File)) {
                    $fileType = 'xls';
                    $extension = 'xls';
                } elseif (preg_match('/^data:(application\/vnd\.openxmlformats-officedocument\.spreadsheetml\.sheet|@file\/vnd\.openxmlformats-officedocument\.spreadsheetml\.sheet);base64,/', $base64File)) {
                    $fileType = 'xlsx';
                    $extension = 'xlsx';
                } elseif (preg_match('/^data:application\/octet-stream;base64,/', $base64File)) {
                    $fileType = 'binary';
                    $extension = 'bin'; // default, will try to detect below
                } else {
                    return;
                }


                // Remove base64 header and decode
                $base64File = preg_replace('#^data:(.*);base64,#i', '', $base64File);
                $fileData = base64_decode($base64File);

                if ($fileData === false) {
                    Log::error("Base64 decoding failed.");
                    return;
                }

                // If unknown, detect MIME type using finfo
                if ($extension === 'bin') {
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->buffer($fileData);

                    switch ($mimeType) {
                        case 'application/pdf':
                            $extension = 'pdf';
                            break;
                        case 'application/msword':
                            $extension = 'doc';
                            break;
                        case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                            $extension = 'docx';
                            break;
                        case 'application/vnd.ms-excel':
                            $extension = 'xls';
                            break;
                        case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                            $extension = 'xlsx';
                            break;
                        case 'image/png':
                            $extension = 'png';
                            break;
                        case 'image/jpeg':
                            $extension = 'jpg';
                            break;
                        default:
                            $extension = 'bin';
                    }
                }

                // Ensure upload directory exists
                if (!File::exists($upload_path)) {
                    File::makeDirectory($upload_path, 0777, true, true);
                }

                // Generate unique filename
                $file_name = time() . Str::random(10) . '.' . $extension;
                $file_path = $upload_path . '/' . $file_name;

                // Save file to disk
                file_put_contents($file_path, $fileData);


                $insert_array = [
                    'emp_id' => Auth::id(),
                    'inspection_id' => $inspection_id,
                    'type' => $type,
                    'file_name' => $file_name,
                    'file_orgname' => $file_name,
                    'file_path' => $upload_path . '/' . $file_name,
                    'file_extension' => $extension,
                    'created_by' => Auth::id(),
                    'trash' => 'NO',
                ];
                $this->create($insert_array);
            }
        }
    }

    public function GetFile($type, $id)
    {
        $data = $this->where('type', $type)->where('inspection_id', $id)->where('status', 1)->where('trash', 'NO')->first();

        if ($data) {
            return $data->file_path;
        }

        return false;
    }

    public function GetFileApi($type, $id)
    {
        $data = $this->where('type', $type)->where('inspection_id', $id)->where('status', 1)->where('trash', 'NO')->first();

        if ($data) {
            return admin_url($data->file_path);
        }

        return false;
    }
}
