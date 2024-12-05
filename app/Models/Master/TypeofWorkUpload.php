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

        // dd($typeofwork_upload);

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
        $typeofwork_upload = $request->file('typeofwork_upload');
        $this->where('typeofwork_id', $id)->update(['trash' => 'YES'],['status' => 1]);
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
                'file_type' => 1,
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

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ptw_masters_typeofwork_upload'));

        // static::created(function ($model) {

        //     $uniqueId = 'CMP-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
        //     $model->update(['company_id' => $uniqueId]);
        // });
    }
}
