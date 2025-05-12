<?php

namespace App\Models\Inspection\RRAA;

use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RRAAFiles extends Model
{
    protected $table = 'inspection_rraa_files';

    protected $fillable = [
        'rraa_checklist_id',
        'file_path',
        'file_name',
        'file_orgname',
        'file_extension',
        'created_by',
        'updated_by',
        'status',
        'trash',
        'created_at',
        'updated_at',
    ];


    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];


    public function store($id, $index, $image)
    {
        $request = Request();
        if ($request->has('rraa_files')) {

            $upload_path = 'public/uploads/inspection/rraa';

            foreach($image as $imageIndex => $value){

                if($index == $imageIndex){
                    if (!File::exists($upload_path)) {
                        File::makeDirectory($upload_path, 0777, true, true);
                    }
                    $file_name = time() . Str::random(10) . '.' . $value->getClientOriginalExtension();
                    $value->move($upload_path, $file_name);
                    $url = $upload_path . '/' . $file_name;

                    $OriginalfileName = $value->getClientOriginalName();
                    $fileExt = $value->getClientOriginalExtension();

                    $insert_array = [
                        'rraa_checklist_id' => $id,
                        'file_path' => $url,
                        'file_name' => $file_name,
                        'file_orgname' => $OriginalfileName,
                        'file_extension' => $fileExt,
                        'created_by' => Auth::id(),
                    ];
                    $this->create($insert_array);
                }
                continue;
            }
        }
    }

    public function get_rraa_file($id){
         $data = $this->where('rraa_checklist_id',$id)->first();
         return $data;
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_rraa_files'));
    }
}
