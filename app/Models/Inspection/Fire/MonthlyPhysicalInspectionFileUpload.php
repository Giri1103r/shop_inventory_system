<?php

namespace App\Models\Inspection\Fire;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;

class MonthlyPhysicalInspectionFileUpload extends Model
{
    protected $table = 'inspeciton_fire_monthly_physical_inspection_upload';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'equipment_id',
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

    public function store($id)
    {
        try {
            $request = request();
            $equipments = $request->equipment;
            foreach ($equipments as $index => $equipment) {
                foreach ($equipment as $equip) {
                    $image = $equip;
                    $upload_path = 'public/uploads/inspection/fire/monthlyphysicalinspection';

                    if (!File::exists($upload_path)) {
                        File::makeDirectory($upload_path, 0777, true, true);
                    }
                    $file_name = time() . Str::random(10) . '.' . $image->getClientOriginalExtension();
                    $image->move($upload_path, $file_name);
                    $url = $upload_path . '/' . $file_name;

                    $OriginalfileName = $image->getClientOriginalName();
                    $fileExt = $image->getClientOriginalExtension();

                    $insert_array = [
                        'inspection_id' => $id,
                        'equipment_id' => $index,
                        'file_path' => $url,
                        'file_name' => $file_name,
                        'file_orgname' => $OriginalfileName,
                        'file_extension' => $fileExt,
                        'created_by' => Auth::id(),
                    ];
                    $this->create($insert_array);
                }
            }
        } catch (Exception $ex) {
            dd($ex);
            report($ex);
        }
    }


    public function GetFile($id)
    {
        $data = $this->where('inspection_id', $id)
            ->where('status', 1)
            ->where('trash', 'NO')
            ->get();

        if ($data) {
            $groupedData = $data->groupBy('equipment_id');
            return $groupedData;
        }

        return false;
    }
}
