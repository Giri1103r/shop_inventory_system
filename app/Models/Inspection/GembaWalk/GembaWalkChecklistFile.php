<?php

namespace App\Models\Inspection\GembaWalk;

use Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GembaWalkChecklistFile extends Model
{
    use  HasFactory;

    protected $table = 'inspection_gemba_walk_checklist_files';

    protected $fillable = [
        'gemba_walk_id',
        'emp_id',
        'gemba_walk_checklist_id',
        'file_type',
        'file_path',
        'file_name',
        'file_orgname',
        'file_extension',
        'created_by',
        'updated_by',

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

    public function storeSignature($gembaWalk_id)
    {
        $request = request();
        $intendent = $request->file('gemba_walk_prepared_by');
        if ($intendent != null) {

            $uploadpath = 'public/uploads/gembaWalkApprovedSignature/' . $gembaWalk_id;

            $folderPath = public_path('uploads/gembaWalkApprovedSignature/' . $gembaWalk_id);

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

                'gemba_walk_id' => $gembaWalk_id,
                'emp_id' => Auth::id(),
                'file_type' => 1,
                'file_name' => $filenewname,
                'file_orgname' => $fileName,
                'file_path' => $path,
                'file_size' => $fileSize,
                'file_extension' => $fileExt,
                'created_by' => $user_id,
            );
            $this->create($insert_data);
        }
    }

    public function storeVerifiedSignature($gembaWalk_id)
    {
        $request = request();

        $intendent = $request->file('gemba_walk_verified_by');
        if ($intendent != null) {

            $uploadpath = 'public/uploads/gembaWalkVerifiedSignature/' . $gembaWalk_id;

            $folderPath = public_path('uploads/gembaWalkVerifiedSignature/' . $gembaWalk_id);

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
                'gemba_walk_id' => $gembaWalk_id,
                'emp_id' => Auth::id(),
                'file_type' => 2,
                'file_name' => $filenewname,
                'file_orgname' => $fileName,
                'file_path' => $path,
                'file_size' => $fileSize,
                'file_extension' => $fileExt,
                'created_by' => $user_id,
            );
            $this->create($insert_data);
        }
    }


    public function getClosingEvidence($id){
        return $this->where('gemba_walk_id',$id)->where('file_type',4)->first();
    }


    // Api
    public function storeSignatureApi($gembaWalk_id)
    {
        $request = request();
        $base64File = $request->input('gemba_walk_prepared_by');

        if (!empty($base64File) && is_string($base64File)) {

            // Detect file extension
            $extension = null;

            if (preg_match('/^data:image\/(\w+);base64,/', $base64File, $matches)) {
                $extension = $matches[1];
            } elseif (preg_match('/^data:application\/pdf;base64,/', $base64File)) {
                $extension = 'pdf';
            } else {
                Log::error("Unsupported Base64 signature file format.");
                return response()->json(['error' => 'Unsupported file format'], 400);
            }

            // Decode the file
            $base64Data = preg_replace('#^data:.*;base64,#', '', $base64File);
            $fileData = base64_decode($base64Data);

            if ($fileData === false) {
                Log::error("Base64 signature decoding failed.");
                return response()->json(['error' => 'Invalid base64 data'], 400);
            }

            // Prepare file path
            $folderPath = public_path('uploads/gembaWalkApprovedSignature/' . $gembaWalk_id);
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            // Save file
            $filenewname = time() . Str::random(10) . '.' . $extension;
            $fullPath = $folderPath . '/' . $filenewname;
            file_put_contents($fullPath, $fileData);

            $relativePath = 'uploads/gembaWalkApprovedSignature/' . $gembaWalk_id . '/' . $filenewname;

            // Save metadata to DB
            $this->create([
                'gemba_walk_id' => $gembaWalk_id,
                'emp_id' => Auth::id(),
                'file_type' => 1,
                'file_name' => $filenewname,
                'file_orgname' => $filenewname,
                'file_path' => $relativePath,
                'file_size' => strlen($fileData),
                'file_extension' => $extension,
                'created_by' => Auth::id(),
            ]);

            return response()->json(['message' => 'Signature uploaded successfully'], 201);
        }

        return response()->json(['error' => 'No signature provided'], 400);
    }

    public function storeVerifiedSignatureApi($gembaWalk_id)
    {
        $request = request();
        $base64File = $request->input('gemba_walk_verified_by');

        if (!empty($base64File) && is_string($base64File)) {
            $extension = null;

            if (preg_match('/^data:image\/(\w+);base64,/', $base64File, $matches)) {
                $extension = $matches[1];
            } elseif (preg_match('/^data:application\/pdf;base64,/', $base64File)) {
                $extension = 'pdf';
            } else {
                Log::error("Unsupported Base64 signature file format.");
                return response()->json(['error' => 'Unsupported file format'], 400);
            }

            // Decode the file
            $base64Data = preg_replace('#^data:.*;base64,#', '', $base64File);
            $fileData = base64_decode($base64Data);

            if ($fileData === false) {
                Log::error("Base64 decoding failed.");
                return response()->json(['error' => 'Invalid base64 data'], 400);
            }

            // Prepare directory
            $folderPath = public_path('uploads/gembaWalkVerifiedSignature/' . $gembaWalk_id);
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            // Generate file name
            $filenewname = time() . Str::random(10) . '.' . $extension;
            $fullPath = $folderPath . '/' . $filenewname;

            // Save file
            file_put_contents($fullPath, $fileData);

            $relativePath = 'uploads/gembaWalkVerifiedSignature/' . $gembaWalk_id . '/' . $filenewname;

            // Save metadata to DB
            $this->create([
                'gemba_walk_id' => $gembaWalk_id,
                'emp_id' => Auth::id(),
                'file_type' => 2, // Verified signature
                'file_name' => $filenewname,
                'file_orgname' => $filenewname,
                'file_path' => $relativePath,
                'file_size' => strlen($fileData),
                'file_extension' => $extension,
                'created_by' => Auth::id(),
            ]);

            return response()->json(['message' => 'Verified signature uploaded successfully'], 201);
        }

        return response()->json(['error' => 'No signature provided'], 400);
    }
}
