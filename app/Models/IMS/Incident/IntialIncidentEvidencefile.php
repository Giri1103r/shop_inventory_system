<?php

namespace App\Models\IMS\Incident;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Str;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


use App\Scopes\TrashScope;

class IntialIncidentEvidencefile extends Model
{
    use  HasFactory;


    protected $table = 'ims_initial_incident_evidence_upload';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'incident_id',
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

    public function store($initialincident)
    {
        $request = request();
        $initialIncidentEvidence = $request->file('evidence');
        \Log::info('Uploaded Files:', ['files' => $initialIncidentEvidence]);
        $this->where('incident_id', $initialincident->id)->update(['trash' => 'YES']);
        if (!empty($initialIncidentEvidence)) {
            foreach ($initialIncidentEvidence as $groupIndex => $siteImageGroup) {
                foreach ($siteImageGroup as $index => $siteImage) {
                    if ($siteImage) {
                        $uploadPath = 'public/uploads/initial/incident/' . $initialincident->id;
                        $folderPath = 'public/uploads/initial/incident/' . $initialincident->id;

                        if (!File::exists($folderPath)) {
                            File::makeDirectory($folderPath, 0755, true);
                        }

                        $filenewname = time() . Str::random(10) . '.' . $siteImage->getClientOriginalExtension();
                        $fileName = $siteImage->getClientOriginalName();
                        $fileSize = $siteImage->getSize();
                        $fileExt = $siteImage->getClientOriginalExtension();

                        $siteImage->move($folderPath, $filenewname);

                        $path = 'public/uploads/initial/incident/' . $initialincident->id . "/" . $filenewname;
                        $userId = Auth::id();

                        $insertData = [
                            'incident_id' => $initialincident->id,
                            'file_name' => $filenewname,
                            'file_orgname' => $fileName,
                            'file_path' => $path,
                            'file_size' => $fileSize,
                            'file_extension' => $fileExt,
                            'created_by' => $userId,
                        ];

                        $this->create($insertData);

                        \Log::info("File saved: " . $filenewname);
                    } else {
                        \Log::warning("Uploaded data is not a valid file: " . json_encode($siteImage));
                    }
                }
            }
        }
    }


    public function updates($id)
    {
        $request = request();
        $initialIncidentEvidence = $request->file('evidence');
    // dd($id);
        \Log::info('Uploaded Files:', ['files' => $initialIncidentEvidence]);
    
        if (!empty($initialIncidentEvidence)) {
            foreach ($initialIncidentEvidence as $siteImage) {
                if ($siteImage) {
                    $folderPath = 'public/uploads/initial/incident/' . $id;
    
                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, 0755, true);
                    }
    
                    $filenewname = time() . Str::random(10) . '.' . $siteImage->getClientOriginalExtension();
                    $fileName = $siteImage->getClientOriginalName();
                    $fileSize = $siteImage->getSize();
                    $fileExt = $siteImage->getClientOriginalExtension();

                    $siteImage->move($folderPath, $filenewname);

                    $filePath = 'public/uploads/initial/incident/' . $id . "/" . $filenewname;
                    $userId = Auth::id();
    
                    $existingEvidence = $this->where('incident_id', $id)
                        ->where('file_orgname', $fileName)
                        ->first();
    
                    if ($existingEvidence) {
                        $existingEvidence->update([
                            'file_name' => $filenewname,
                            'file_path' => $filePath,
                            'file_size' => $fileSize,
                            'file_extension' => $fileExt,
                            'updated_by' => $userId,
                        ]);
                        \Log::info("File updated: " . $filenewname);
                    } else {
                        $this->create([
                            'incident_id' => $id,
                            'file_name' => $filenewname,
                            'file_orgname' => $fileName,
                            'file_path' => $filePath,
                            'file_size' => $fileSize,
                            'file_extension' => $fileExt,
                            'created_by' => $userId,
                        ]);
                        \Log::info("New file added: " . $filenewname);
                    }
                } else {
                    \Log::warning("Invalid file upload: " . json_encode($siteImage));
                }
            }
        }
    }
    

    public function deleterecord($id)
    {

        $update_data = array(
            'status' => 0,
            'trash' => 'YES',
        );

        return $this->where('id', $id)->update($update_data);
    }


    public function selectOne($id)
    {

        $data = $this->select(
            'ims_initial_incident_evidence_upload.*',
        )
            ->where('ims_initial_incident_evidence_upload.incident_id', $id)
            ->get();

        return $data;
    }



    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ims_initial_incident_evidence_upload'));
    }
}
