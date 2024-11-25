<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeofWorkUpload extends Model
{
    use  HasFactory;


    protected $table = 'masters_ptw_typeofwork_upload';
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

    public function store($ptw_hot_cold)
    {

        $request = request();

        $typeofwork_upload = $request->file('typeofwork_upload');

        if ($typeofwork_upload != null) {


            $uploadpath = 'public/uploads/ptw/typeofwork/' . $ptw_hot_cold->id;

            $folderPath = public_path('uploads/ptw/typeofwork/' . $ptw_hot_cold->id);

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
                'ptw_hot_cold_id' => $ptw_hot_cold->id,
                'permit_id' => $request->id,
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


    public function updates($ptw_hot_cold)
    {

        $request = request();
        $typeofwork_upload = $request->file('typeofwork_upload');
        // $this->where('ptw_hot_cold_id', $ptw_hot_cold)->update(['trash' => 'YES']);
        if ($typeofwork_upload != null) {


            $uploadpath = 'public/uploads/ptw/typeofwork/' . $ptw_hot_cold;

            $folderPath = public_path('uploads/ptw/typeofwork/' . $ptw_hot_cold);

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
                'permit_id' => $ptw_hot_cold,
                'ptw_hot_cold_id' => $ptw_hot_cold,
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
        static::addGlobalScope(new TrashScope('masters_ptw_typeofwork'));

        // static::created(function ($model) {

        //     $uniqueId = 'CMP-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
        //     $model->update(['company_id' => $uniqueId]);
        // });
    }
}
