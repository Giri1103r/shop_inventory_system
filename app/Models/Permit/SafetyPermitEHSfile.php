<?php

namespace App\Models\Permit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Str;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


use App\Scopes\TrashScope;

class SafetyPermitEHSfile extends Model
{
    use  HasFactory;


    protected $table = 'ptw_aprove_reject_ehs_file';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'permit_id',
        'approve_type',
        'file_type',
        'file_name',
        'file_orgname',
        'file_path',
        'file_size',
        'file_extension',
        'permit_status',
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

    public function store($approve, $permit_status)
    {
        $request = request();
        $siteImagesData = $request->file('site_images');
    
        \Log::info('Uploaded Files:', ['files' => $siteImagesData]);
        $this->where('permit_id', $approve->permit_id)->update(['trash' => 'YES']);
        if (!empty($siteImagesData)) {
            foreach ($siteImagesData as $groupIndex => $siteImageGroup) {
                foreach ($siteImageGroup as $index => $siteImage) {
                    if ($siteImage) {
                        $uploadPath = 'public/uploads/permit/approve/' . $approve->id;
                        $folderPath = 'public/uploads/permit/approve/' . $approve->id;
    
                        if (!File::exists($folderPath)) {
                            File::makeDirectory($folderPath, 0755, true);
                        }
    
                        $filenewname = time() . Str::random(10) . '.' . $siteImage->getClientOriginalExtension();
                        $fileName = $siteImage->getClientOriginalName();
                        $fileSize = $siteImage->getSize();
                        $fileExt = $siteImage->getClientOriginalExtension();

                        $siteImage->move($folderPath, $filenewname);
    
                        $path = 'public/uploads/permit/approve/' . $approve->id . "/" . $filenewname;
                        $userId = Auth::id();

                        $insertData = [
                            'permit_id' => $approve->permit_id,
                            'approve_type' => 1,
                            'file_type' => 1,
                            'file_name' => $filenewname,
                            'file_orgname' => $fileName,
                            'file_path' => $path,
                            'file_size' => $fileSize,
                            'file_extension' => $fileExt,
                            'permit_status' => $permit_status,
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
    
    
    

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('ptw_aprove_reject_ehs_file'));
    }
}
