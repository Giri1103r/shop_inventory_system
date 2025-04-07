<?php

namespace App\Models\Inspection\Ohc;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OhcSignature extends Model
{
    protected $table = 'inspection_ohc_signatureupload';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'type',
        'sub_type',
        'emp_id',
        'ohc_id',
        'file_path',
        'file_name',
        'file_orgname',
        'file_extension',
        'requestor_file_path',
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

                $data =    $this->create($insert_array);
            }
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function requestorsignatureUpload($type, $id)
    {
        try {

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
                    'emp_id' => Auth::id(),
                    'ohc_id' => $id,
                    'type' => $type,
                    'requestor_file_path' => $url,
                    'file_path' => $url,
                    'file_name' => $file_name,
                    'file_orgname' => $OriginalfileName,
                    'file_extension' => $fileExt,
                    'created_by' => Auth::id(),
                ];

                return $this->create($insert_array);
            }
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function signatureLogUpload($empId, $sfty_petty_id, $type, $fileInputName)
    {
        try {
            $signatures = request()->file($fileInputName);

            if (is_array($signatures)) {
                foreach ($signatures as $signature) {
                    if ($signature) {
                        $upload_path = 'public/uploads/inspection/ohc/signatureupload';

                        if (!File::exists($upload_path)) {
                            File::makeDirectory($upload_path, 0777, true, true);
                        }

                        $file_name = time() . Str::random(10) . '.' . $signature->getClientOriginalExtension();

                        $signature->move($upload_path, $file_name);

                        $file_path = $upload_path . '/' . $file_name;
                        $file_extension = $signature->getClientOriginalExtension();

                        DB::table('inspection_ohc_signatureupload')->insert([
                            'emp_id' => $empId,
                            'ohc_id' => $sfty_petty_id,
                            'type' => OHC_SAFETY_PETTY_LOGBOOK_INSPECTION,
                            'sub_type' => $type,
                            'file_path' => $file_path,
                            'file_name' => $file_name,
                            'file_orgname' => $signature->getClientOriginalName(),
                            'file_extension' => $file_extension,
                            'created_by' => Auth::id(),
                        ]);
                    }
                }
            } else {
                if ($signatures) {
                    $upload_path = 'public/uploads/inspection/ohc/signatureupload';
                    if (!File::exists($upload_path)) {
                        File::makeDirectory($upload_path, 0777, true, true);
                    }

                    $file_name = time() . Str::random(10) . '.' . $signatures->getClientOriginalExtension();

                    $signatures->move($upload_path, $file_name);

                    $file_path = $upload_path . '/' . $file_name;
                    $file_extension = $signatures->getClientOriginalExtension();

                    DB::table('inspection_ohc_signatureupload')->insert([
                        'emp_id' => $empId,
                        'ohc_id' => $sfty_petty_id,
                        'type' => OHC_SAFETY_PETTY_LOGBOOK_INSPECTION,
                        'sub_type' => $type,
                        'file_path' => $file_path,
                        'file_name' => $file_name,
                        'file_orgname' => $signatures->getClientOriginalName(),
                        'file_extension' => $file_extension,
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            return true;
        } catch (Exception $ex) {
            report($ex);
        }
    }

    public function safetyofficersignature($id, $safetyofficer, $type)
    {

        return $this->where('ohc_id', $id)->where('emp_id', $safetyofficer->approved_by)->where('type', $type)->first();
    }

    public function approversignature($id, $approver, $type){

        return $this->where('ohc_id',$id)->where('emp_id',$approver)->where('type',$type)->first();
    }

    public function floormanagersignature($id, $floormanger, $type){
        return $this->where('ohc_id',$id)->where('emp_id',$floormanger->approved_by)->where('type',$type)->first();
    }

    public function requestorSignature($id, $requestorsignature, $type)
    {

        return $this->where('ohc_id', $id)->where('emp_id', $requestorsignature)->where('type', $type)->first();
    }

    public function getGivenBy($type, $sub_type, $id)
    {
        $data =  $this->where('ohc_id', $id)
            ->where('type', $type)
            ->where('sub_type', $sub_type)
            ->first();

        return $data;
    }
    public function getReceivedBy($type, $sub_type, $id)
    {
        return $this->where('ohc_id', $id)
            ->where('type', $type)
            ->where('sub_type', $sub_type)
            ->first();
    }

    public function getFiles($id, $type)
    {
        return $this->where('ohc_id', $id)->where('type', $type)->where('status', 1)->where('trash', 'NO')->first();
    }
    public function getFilesByEmpId($id, $type)
    {
        return $this->where('emp_id', $id)->where('type', $type)->where('status', 1)->where('trash', 'NO')->first();
    }
}
