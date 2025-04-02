<?php

namespace App\Models\Inspection\Fire;

use Exception;
use Illuminate\Support\Str;
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

    public function file_upload($type,$inspection_id)
    {
        try {
            $id = Auth::id();
            $request = Request();
            $file = $request->file('device_image');
            if ($request->has('device_image')) {
                $image = $request->file('device_image');
                $upload_path = 'public/uploads/inspection/fire/'.GetTypeName($type);

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

    public function GetFile($type,$id)
    {
        $data = $this->where('type',$type)->where('inspection_id',$id)->where('status',1)->where('trash','NO')->first();

        if($data)
        {
            return $data->file_path;
        }

        return false;
    }

}
