<?php

namespace App\Models\Inspection\Safety;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Master\Employee;
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
            dd($ex);
            report($ex);
        }
    }
}
