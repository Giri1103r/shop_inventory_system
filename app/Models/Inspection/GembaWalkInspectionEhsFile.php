<?php

namespace App\Models\Inspection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Str;

class GembaWalkInspectionEhsFile extends Model
{
    use HasFactory;

    protected $table = 'inspection_gemba_walk_ehs_inspection_files';

    protected $fillable = [
        'ehs_id',
        'file_type',
        'file_path',
        'file_name',
        'file_orgname',
        'file_extension',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];


    public function capaFileSubmit($ehs_id,$upload_status){
        $request = request();

        $intendent = $request->file('capa_image');
        if ($intendent != null) {

            $uploadpath = 'public/uploads/gembaWalkEhs/' . $ehs_id;

            $folderPath = public_path('uploads/gembaWalkEhs/' . $ehs_id);

            if (!File::exists($folderPath)) {

                File::makeDirectory($folderPath, 0755, true);
            }
            $filenewname = time() . Str::random('10') . '.' . $intendent->getClientOriginalExtension();

            $fileName = $intendent->getClientOriginalName();
            $fileSize = $intendent->getSize();

            $fileExt = $intendent->getClientOriginalExtension();

            $intendent->move($uploadpath, $filenewname);

            $path = $uploadpath . "/" . $filenewname;
            $user_id = Auth::id();

            $insert_data = array(

                'ehs_id' => $ehs_id,
                'file_type' => $upload_status,
                'file_name' => $filenewname,
                'file_orgname' => $fileName,
                'file_path' => $path,
                'file_size' => $fileSize,
                'file_extension' => $fileExt,
                'created_by' => $user_id,
            );
            $this->create($insert_data);
        }
    }
}
