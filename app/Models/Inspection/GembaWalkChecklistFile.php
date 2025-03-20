<?php

namespace App\Models\Inspection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\File;
use Str;

use Illuminate\Support\Facades\Auth;
class GembaWalkChecklistFile extends Model
{
    use  HasFactory;

    protected $table = 'inspection_gemba_walk_checklist_files';

    protected $fillable = [
        'gemba_walk_id',
        'gemba_walk_checklist_id',
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

    public function storeSignature($gembaWalk_id){
        $request = request();
        $intendent = $request->file('gemba_walk_prepared_by');
        if ($intendent != null) {

            $uploadpath = 'public/uploads/gembaWalkApprovedSignature/' . $gembaWalk_id;

            $folderPath = public_path('uploads/gembaWalkApprovedSignature/' . $gembaWalk_id);

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

                'gemba_walk_id' => $gembaWalk_id,
                'file_type'=>1,
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

    public function storeVerifiedSignature($gembaWalk_id){
        $request = request();

        $intendent = $request->file('gemba_walk_verified_by');
        if ($intendent != null) {

            $uploadpath = 'public/uploads/gembaWalkVerifiedSignature/' . $gembaWalk_id;

            $folderPath = public_path('uploads/gembaWalkVerifiedSignature/' . $gembaWalk_id);

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
                'gemba_walk_id' => $gembaWalk_id,
                'file_type'=>2,
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
