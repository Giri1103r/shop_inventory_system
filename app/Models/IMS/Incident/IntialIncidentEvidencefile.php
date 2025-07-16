<?php

namespace App\Models\IMS\Incident;

use Illuminate\Http\Request;

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
        'capa_id',
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
    public function evidenceStore_api($incidentId)
    {
        $request = request();
        $evidencefiles = $request->evidence;

        if ($evidencefiles != null) {
            foreach ($evidencefiles as $evidencefile) {
                $base64File = $evidencefile;

                // Clean the base64 string
                $data = preg_replace('#^data:.*?;base64,#i', '', $base64File);
                $decodedFile = base64_decode($data);

                // Detect MIME type
                $finfo = finfo_open();
                $mimeType = finfo_buffer($finfo, $decodedFile, FILEINFO_MIME_TYPE);
                finfo_close($finfo);

                // Allowed MIME types
                $validFormats = [
                    'image/jpeg',
                    'image/jpg',
                    'image/png',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'video/mp4'
                ];

                if (!in_array($mimeType, $validFormats)) {
                    // Skip invalid formats
                    continue;
                }

                // Map MIME type to file extension
                $mimetypes = [
                    'image/jpeg' => 'jpeg',
                    'image/jpg' => 'jpg',
                    'image/png' => 'png',
                    'application/pdf' => 'pdf',
                    'application/msword' => 'doc',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                    'video/mp4' => 'mp4'
                ];

                $extension = $mimetypes[$mimeType] ?? 'file';

                // Set folder and file path
                $folderPath = public_path('uploads/initial/incident/' . $incidentId);
                $relativePath = 'public/uploads/initial/incident/' . $incidentId;

                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, 0755, true);
                }

                // Generate file name
                $filenewname = time() . Str::random(10) . '.' . $extension;
                $filePath = $folderPath . '/' . $filenewname;

                // Save decoded content to file
                file_put_contents($filePath, $decodedFile);

                // File details
                $fileSize = filesize($filePath);
                $user_id = Auth::id();

                // Insert into DB
                $insert_data = [
                    'incident_id'     => $incidentId,
                    'file_name'       => $filenewname,
                    'file_orgname'    => $filenewname,
                    'file_path'       => $relativePath . '/' . $filenewname,
                    'file_size'       => $fileSize,
                    'file_extension'  => $extension,
                    'created_by'      => $user_id,
                ];

                $this->create($insert_data);
            }
        }
    }



    public function capaEvidence($initialincident, $capa_id)
    {
        $request = request();
        $initialIncidentEvidence = $request->file('evidence');
        if (!empty($initialIncidentEvidence)) {
            foreach ($initialIncidentEvidence as $groupIndex => $siteImageGroup) {
                foreach ($siteImageGroup as $index => $siteImage) {
                    if ($siteImage) {
                        $uploadPath = 'public/uploads/initial/incident/' . $capa_id;
                        $folderPath = 'public/uploads/initial/incident/' . $capa_id;

                        if (!File::exists($folderPath)) {
                            File::makeDirectory($folderPath, 0755, true);
                        }

                        $filenewname = time() . Str::random(10) . '.' . $siteImage->getClientOriginalExtension();
                        $fileName = $siteImage->getClientOriginalName();
                        // dd($siteImage,$fileName);
                        $fileSize = $siteImage->getSize();
                        $fileExt = $siteImage->getClientOriginalExtension();

                        $siteImage->move($folderPath, $filenewname);

                        $path = 'public/uploads/initial/incident/' . $capa_id . "/" . $filenewname;
                        $userId = Auth::id();

                        $insertData = [
                            'incident_id' => $initialincident->id,
                            'capa_id' => $capa_id,
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
            ->where('ims_initial_incident_evidence_upload.capa_id', null)
            ->get();
        return $data;
    }
    public function SelectcapaEvidence($id, $incident_id)
    {

        $data = $this->select(
            'ims_initial_incident_evidence_upload.*',
        )
            ->where('ims_initial_incident_evidence_upload.incident_id', $incident_id)
            ->where('ims_initial_incident_evidence_upload.capa_id', $id)
            ->get();
        return $data;
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ims_initial_incident_evidence_upload'));
    }
}
