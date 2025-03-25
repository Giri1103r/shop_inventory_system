<?php

namespace App\Models\Inspection\MSDS;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;

class MSDSSignatureUpload extends Model
{
    protected $table = 'inspection_msds_signatureupload';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'emp_id',
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



    public function signatureUpload()
    {
        try {
            $id = Auth::id();
            $request = Request();
            $file = $request->file('signature_image');
            if ($request->has('signature_image')) {
                $image = $request->file('signature_image');
                $upload_path = 'public/uploads/inspection/msds/signatureupload';

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
                    'inspection_id' => decryptId($request->id),
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

    public function signatureStrore($type,$id)
    {
        try {
           
            $request = Request();
            $file = $request->file('signature_image');
            if ($request->has('signature_image')) {
                $image = $request->file('signature_image');
                $upload_path = 'public/uploads/inspection/msds/signatureupload';

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
}
