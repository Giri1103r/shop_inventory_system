<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Str;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Facades\File;



class TypeofWorkUpload extends Model
{
    use  HasFactory;


    protected $table = 'ptw_masters_typeofwork_upload';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'typeofwork_id',
        'file_name',
        'file_orgname',
        'file_path',
        'file_size',
        'file_extension',
        'created_by',
        'status',
        'trash',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function store($id)
    {

        $request = request();

        $typeofwork_upload = $request->file('typeofwork_upload');



        if ($typeofwork_upload != null) {


            $uploadpath = 'public/uploads/ptw/typeofwork/' . $id;

            $folderPath = public_path('uploads/ptw/typeofwork/' . $id);

            if (!File::exists($folderPath)) {

                File::makeDirectory($folderPath, 0755, true);
            }

            $filenewname = time() . Str::random('10') . '.' . $typeofwork_upload->getClientOriginalExtension();

            $fileName = $typeofwork_upload->getClientOriginalName();
            $fileSize = $typeofwork_upload->getSize();

            $fileExt = $typeofwork_upload->getClientOriginalExtension();

            $typeofwork_upload->move($uploadpath, $filenewname);

            $path = $uploadpath . "/" . $filenewname;
            $user_id = Auth::id();

            $insert_data = array(
                'typeofwork_id' => $id,
                'file_name' => $filenewname,
                'file_orgname' => $fileName,
                'file_path' => $path,
                'file_size' => $fileSize,
                'file_extension' => $fileExt,
                'created_by' => $user_id,
            );

            $this->create($insert_data)->id;
        }
    }



    public function updates($id)
    {
        $request = request();
        $intendent = $request->file('typeofwork_upload');
        if ($intendent != null) {

            $uploadpath = 'public/uploads/ptw/typeofwork/' . $id;

            $folderPath = public_path('uploads/ptw/typeofwork/' . $id);
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

                'typeofwork_id' => $id,
                'file_type' => 2,
                'file_name' => $filenewname,
                'file_orgname' => $fileName,
                'file_path' => $path,
                'file_size' => $fileSize,
                'file_extension' => $fileExt,
                'updated_by' => $user_id,
                'updated_at' => now(),
            );
            $existingData = $this->where('typeofwork_id', $id)->first();

            if ($existingData) {
                $existingData->update($insert_data);
            } else {
                $this->create($insert_data);
            }
        }
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ptw_masters_typeofwork_upload'));

      
    }
}
