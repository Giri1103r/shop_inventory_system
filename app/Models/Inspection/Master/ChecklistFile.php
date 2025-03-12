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
            $upload_path = 'uploads/inspection/master/checklist';

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

    public function selectChecklistTypeImage($id){
        return $this->where('checklist_id', $id)->get();
    }
}
