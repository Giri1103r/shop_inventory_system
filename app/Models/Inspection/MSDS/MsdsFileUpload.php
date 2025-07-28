<?php

namespace App\Models\Inspection\MSDS;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;

class MsdsFileUpload extends Model
{
    protected $table = 'inspection_msds_files';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'msds_id',
        'msds_detail_id',
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

    public function file_upload($msds_id, $msds_detail_id, $index)
    {
        try {
            $request = request();

            if ($request->hasFile("msds_image.$index")) {
                $file = $request->file("msds_image.$index");

                $upload_path = public_path("public/uploads/inspection/msds/{$msds_id}/{$msds_detail_id}");

                if (!File::exists($upload_path)) {
                    File::makeDirectory($upload_path, 0777, true, true);
                }

                $file_name = time() . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->move($upload_path, $file_name);

                $url = "public/uploads/inspection/msds/{$msds_id}/{$msds_detail_id}/{$file_name}";

                $insert_array = [
                    'msds_id'        => $msds_id,
                    'msds_detail_id' => $msds_detail_id,
                    'file_path'      => $url,
                    'file_name'      => $file_name,
                    'file_orgname'   => $file->getClientOriginalName(),
                    'file_extension' => $file->getClientOriginalExtension(),
                    'created_by'     => Auth::id(),
                ];

                $this->create($insert_array);
            }
        } catch (\Exception $ex) {
            report($ex);
        }
    }



    // public function GetFile($id, $msds_detail_id)
    // {
    //     $data = $this->where('msds_id', $id)->where('msds_detail_id', $msds_detail_id)->where('status', 1)->where('trash', 'NO')->first();

    //     if ($data) {
    //         return $data->file_path;
    //     }

    //     return false;
    // }
    public function GetFile($id, $msds_detail_id)
    {
        return $this->where('msds_id', $id)
            ->where('msds_detail_id', $msds_detail_id)
            ->where('status', 1)
            ->where('trash', 'NO')
            ->first();
    }
}
