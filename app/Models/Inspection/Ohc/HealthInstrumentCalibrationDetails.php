<?php

namespace App\Models\Inspection\Ohc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class HealthInstrumentCalibrationDetails extends Model
{
    use  HasFactory;

    protected $table = 'inspection_ohc_health_instrument_calibration_track_sheet_details';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'sr_no',
        'health_instrument_id',
        'instrument_name',
        'resource_code',
        'exact_location',
        'unit_id',
        'instrument_serial_no',
        'make',
        'model',
        'instrument_range',
        'calibration_frequency',
        'date_of_calibration',
        'due_date_of_calibration',
        'remarks',
        'created_by',
        'updated_by',
        'updated_at',
        'created_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

 

    public function store($id){
        $request = request();
        $health_instrument_details = $request->input('health_instrument');
        
        if (!empty($health_instrument_details) && is_array($health_instrument_details)) {
            $insertedData = []; 
            foreach ($health_instrument_details as $health) {
                $data = [
                    'health_instrument_id' => $id, 
                    'sr_no'=>$health['sr_no'],
                    'instrument_name' => $health['instrument_name'],
                    'resource_code' => $health['resource_code'],
                    'exact_location' => $health['exact_location'],
                    'unit_id' => decryptId($health['unit_id']),
                    'instrument_serial_no' => $health['instrument_serial_no'],
                    'make' => $health['make'],
                    'model' => $health['model'],
                    'instrument_range' => $health['instrument_range'],
                    'calibration_frequency' => $health['calibration_frequency'],
                    'date_of_calibration' => DBdateformat($health['date_of_calibration']),
                    'due_date_of_calibration' => DBdateformat($health['due_date_of_calibration']),
                    'remarks' => $health['instrument_remarks'],
                    'created_by' => Auth::id(),
                ];

                
    
                $insertedData[] = $this->create($data); 
            }
            return $insertedData; 
        }
    }
    
}

    


