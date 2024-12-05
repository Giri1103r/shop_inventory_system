<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;
use Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingMatrixFile extends Model
{
    use  HasFactory;


    protected $table = 'training_matrix_files';
    protected $primaryKey = 'id';

    protected $fillable = [
        'training_matrix_id',
        'training_evaluation_id',
        'topic_id',
        'file_type',
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

    public function store($training_matrix)
    {
        $request = request();
        $intendent = $request->file('target_content');
        if ($intendent != null) {


            $uploadpath = 'public/uploads/training_matrix/' . $training_matrix->id;

            $folderPath = public_path('uploads/training_matrix/' . $training_matrix->id);

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

                'training_matrix_id' => $training_matrix->id,
                'training_evaluation_id' => $training_matrix->training_evaluation,
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
    public function store1($training_matrix)
    {
        $request = request();
        $intendent = $request->file('questionnaire');
        if ($intendent != null) {


            $uploadpath = 'public/uploads/training_matrix/' . $training_matrix->id;

            $folderPath = public_path('uploads/training_matrix/' . $training_matrix->id);

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

                'training_matrix_id' => $training_matrix->id,
                'training_evaluation_id' => $training_matrix->training_evaluation,
                'file_type' => 2,
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
    public function store2($topic)
    {
        $request = request();
        $intendent = $request->file('questionnaire');
        if ($intendent != null) {


            $uploadpath = 'public/uploads/topic/' . $topic->id;

            $folderPath = public_path('uploads/topic/' . $topic->id);

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

                'topic_id' => $topic->id,
                'file_type' => 3,
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
        $intendent = $request->file('target_content');
        if ($intendent != null) {


            $uploadpath = 'public/uploads/training_matrix/' . $id;

            $folderPath = public_path('uploads/training_matrix/' . $id);

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

                'training_matrix_id' => $id,
                'training_evaluation_id' => decryptId($request->training_evaluation),
                'file_type' => 1,
                'file_name' => $filenewname,
                'file_orgname' => $fileName,
                'file_path' => $path,
                'file_size' => $fileSize,
                'file_extension' => $fileExt,
                'updated_by' => $user_id,
                'updated_at' => now(),
            );


            $this->updateOrCreate(
                ['training_matrix_id' => $id, 'file_type' => 1],
                $insert_data
            );
        }
    }
    public function updates1($id)
    {
        $request = request();
        $intendent = $request->file('questionnaire');
        if ($intendent != null) {


            $uploadpath = 'public/uploads/training_matrix/' . $id;

            $folderPath = public_path('uploads/training_matrix/' . $id);

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

                'training_matrix_id' => $id,
                'training_evaluation_id' => decryptId($request->training_evaluation),
                'file_type' => 2,
                'file_name' => $filenewname,
                'file_orgname' => $fileName,
                'file_path' => $path,
                'file_size' => $fileSize,
                'file_extension' => $fileExt,
                'updated_by' => $user_id,
                'updated_at' => now(),
            );
            $existingData = $this->where('training_matrix_id', $id)->where('file_type', 2)->first();

            if ($existingData) {
                $existingData->update($insert_data);
            } else {
                $this->create($insert_data);
            }
        }
    }
    public function updates2($id)
    {
        $request = request();
        $intendent = $request->file('questionnaire');
        if ($intendent != null) {


            $uploadpath = 'public/uploads/topic/' . $id;

            $folderPath = public_path('uploads/topic/' . $id);

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

                'topic_id' => $id,
                'file_type' => 2,
                'file_name' => $filenewname,
                'file_orgname' => $fileName,
                'file_path' => $path,
                'file_size' => $fileSize,
                'file_extension' => $fileExt,
                'updated_by' => $user_id,
                'updated_at' => now(),
            );
            $existingData = $this->where('topic_id', $id)->where('file_type', 3)->first();

            if ($existingData) {
                $existingData->update($insert_data);
            } else {
                $this->create($insert_data);
            }
        }
    }

    public function deleterecord($id)
    {
        $update_data = array(
            'status' => 0,
            'trash' => 'YES',
        );

        return $this->where('training_matrix_id', $id)->update($update_data);
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('training_matrix_files'));
    }
}
