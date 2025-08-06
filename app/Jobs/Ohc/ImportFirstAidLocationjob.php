<?php

namespace App\Jobs\Ohc;

use Shuchkin\SimpleXLSX;
use App\Models\UploadLog;
use Illuminate\Bus\Queueable;
use App\Models\Master\Factory;
use App\Models\UploadLogError;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Master\Company;
use App\Models\Master\Department;
use App\Models\Master\Unit;
use App\Models\OhcManagement\Master\FirstAidLocation;
use App\Models\OhcManagement\Master\HospitalDetails;
use App\Models\OhcManagement\Master\Vendor;
use Illuminate\Support\Facades\Session;

// class ImportFirstAidLocationjob implements ShouldQueue
class ImportFirstAidLocationjob
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $details;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {


        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $i = 1;
        UploadLog::where('id', $this->details['log_id'])
            ->update(['upload_status' => 1]);

        $xlsx = SimpleXLSX::parse($this->details['path']);
        $cond_error_datas = [];

        // Helper function to normalize unit name
        $normalize = function ($value) {
            $value = strtolower(trim($value));
            // Replace multiple spaces with a single space
            $value = preg_replace('/\s+/', ' ', $value);
            // Replace roman numerals with numbers
            $romanMap = [
                ' i' => ' 1',
                ' ii' => ' 2',
                ' iii' => ' 3',
                ' iv' => ' 4',
                ' v' => ' 5',
            ];
            $value = str_replace(array_keys($romanMap), array_values($romanMap), $value);
            // Remove spaces for final comparison
            return str_replace(' ', '', $value);
        };

        foreach ($xlsx->rows() as $row) {
            // HEADER CHECK
            if ($i == 1) {
                if (count($row) != 7) {
                    $cond_error_datas[] = [
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Header column count mismatch',
                    ];
                    break;
                }
                if (
                    trim($row[0]) != 'SNo' ||
                    trim($row[1]) != 'Unit Name' ||
                    trim($row[2]) != 'Department Name' ||
                    trim($row[3]) != 'Exact Location' ||
                    trim($row[4]) != 'Station Master' ||
                    trim($row[5]) != 'Station Number' ||
                    trim($row[6]) != 'First Aid Box Number'
                ) {
                    $cond_error_datas[] = [
                        'upload_id' => $this->details['log_id'],
                        'line_no' => $i,
                        'error' => 'Header column names mismatch',
                    ];
                }
                $i++;
                continue;
            }

            // DATA EXTRACTION
            $sno = trim($row[0]);
            $unitName = trim($row[1]);
            $departmentName = trim($row[2]);
            $excatLocation = trim($row[3]);
            $stationMaster = trim($row[4]);
            $stationNumber = trim($row[5]);
            $firstAidLocation = trim($row[6]);

            // VALIDATION
            if ($unitName == '') {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Unit name is missing'
                ];
                $i++;
                continue;
            }

            // Find unit using normalized comparison
            $unit = Unit::get()->first(function ($u) use ($unitName, $normalize) {
                return $normalize($u->unit_name) === $normalize($unitName);
            });

            if (!$unit) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Unit not found'
                ];
                $i++;
                continue;
            }

            if ($departmentName == '') {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Department name is missing'
                ];
                $i++;
                continue;
            }

            // Find department in the matched unit using normalized comparison
            $department = Department::where('unit_id', $unit->id)->get()->first(function ($d) use ($departmentName, $normalize) {
                return $normalize($d->department_name) === $normalize($departmentName);
            });

            if (!$department) {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Department not found'
                ];
                $i++;
                continue;
            }

            if ($excatLocation == '') {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Location is missing'
                ];
                $i++;
                continue;
            }

            if ($stationMaster == '') {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Station Master is missing'
                ];
                $i++;
                continue;
            }

            if ($firstAidLocation == '') {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'First Aid Box Number is missing'
                ];
                $i++;
                continue;
            }

            if ($stationNumber == '') {
                $cond_error_datas[] = [
                    'upload_id' => $this->details['log_id'],
                    'line_no' => $i,
                    'error' => 'Station Number is missing'
                ];
                $i++;
                continue;
            }

            // SAVE DATA
            FirstAidLocation::create([
                'unit_id' => $unit->id,
                'department_id' => $department->id,
                'station_master' => $stationMaster,
                'location_id' => $excatLocation, // replace with location ID if needed
                'station_number' => $stationNumber,
                'first_aid_box_no' => $firstAidLocation,
                'created_by' => $this->details['user_id']
            ]);

            $i++;
        }

        // FINAL STATUS UPDATE
        if (count($cond_error_datas) > 0) {
            UploadLogError::insert($cond_error_datas);
            $status = 3;
            Session::flash('error', 'Failed to upload. Please check the upload log.');
        } else {
            $status = 2;
            Session::flash('success', 'Upload completed successfully.');
        }

        UploadLog::where('id', $this->details['log_id'])->update(['upload_status' => $status]);
    }
}
