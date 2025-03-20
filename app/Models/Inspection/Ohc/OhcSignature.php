<?php

namespace App\Models\Inspection\Ohc;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class OhcSignature extends Model
{
    protected $table = 'inspection_ohc_signatureupload';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'type',
        'emp_id',
        'ohc_id',
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

    public function signatureUpload($type)
    {
        try {

            $id = Auth::id();
            $request = Request();
            
            $file = $request->file('signature_image');
            if ($request->has('signature_image')) {
                $image = $request->file('signature_image');
                $upload_path = 'public/uploads/inspection/ohc/signatureupload';

                if (!File::exists($upload_path)) {
                    File::makeDirectory($upload_path, 0777, true, true);
                }
                $file_name = time() . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->move($upload_path, $file_name);
                $url = $upload_path . '/' . $file_name;

                $OriginalfileName = $image->getClientOriginalName();
                $fileExt = $image->getClientOriginalExtension();

                $insert_array = [
                    'checklist_id' => $id,
                    'emp_id' => Auth::id(),
                    'ohc_id' => decryptId($request->id),
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
}
