<?php

namespace App\Models\Inspection\Master;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;

class ChecklistFile extends Model
{

    protected $table = 'inspection_checklist_files';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'type',
        'checklist_id',
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
    public function store($id, $type)
    {
        $request = Request();
        if ($request->has('checklist_file')) {
            $image = $request->file('checklist_file');
            $upload_path = 'public/uploads/inspection/master/checklist';

            if (!File::exists(public_path($upload_path))) {
                File::makeDirectory(public_path($upload_path), 0777, true, true);
            }
            $file_name = time() . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path($upload_path), $file_name);
            $url = $upload_path . '/' . $file_name;

            $OriginalfileName = $image->getClientOriginalName();
            $fileExt = $image->getClientOriginalExtension();

            $insert_array = [
                'checklist_id' => $id,
                'type' => $type,
                'file_path' => $url,
                'file_name' => $file_name,
                'file_orgname' => $OriginalfileName,
                'file_extension' => $fileExt,
                'created_by' => Auth::id(),
            ];
            $this->create($insert_array);
        }
    }



    public function updates($id,$type)
    {
        $request = request();
        $checklist_fileImage = $request->file('checklist_file');
    
        if ($checklist_fileImage) {
            $folderPath = 'public/uploads/inspection/master/checklist/' . $id;
    

            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }
    

            $filenewname = time() . Str::random(10) . '.' . $checklist_fileImage->getClientOriginalExtension();
            $fileName = $checklist_fileImage->getClientOriginalName();
            $fileSize = $checklist_fileImage->getSize();
            $fileExt = $checklist_fileImage->getClientOriginalExtension();
    

            $checklist_fileImage->move($folderPath, $filenewname);
    
            $filePath = $folderPath . "/" . $filenewname;
            $userId = Auth::id();
    

            $existingFile = $this->where('checklist_id', $id)->where('type', $type)->first();

            if ($existingFile) {
                $oldFilePath = $existingFile->file_path;
                if (File::exists($oldFilePath)) {
                    File::delete($oldFilePath);
                }
    
                $existingFile->update([
                    'file_name' => $filenewname,
                    'file_orgname' => $fileName,
                    'file_path' => $filePath,
                    'file_size' => $fileSize,
                    'file_extension' => $fileExt,
                    'updated_by' => $userId,
                ]);
            } else {
                $this->create([
                    'checklist_id' => $id,
                    'file_name' => $filenewname,
                    'file_orgname' => $fileName,
                    'file_path' => $filePath,
                    'file_size' => $fileSize,
                    'file_extension' => $fileExt,
                    'created_by' => $userId,
                ]);
            }
        } 
    }
    


    public function selectChecklistTypeImage($id, $type)
    {
        return  $this->where('checklist_id', $id)->where('type', $type)->first();
    }
}
