<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Master\Location;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Log;

use App\Models\User;

class Work extends Model
{
    use  HasFactory;


    protected $table = 'masters_work';
    protected $primaryKey = 'id';

    protected $fillable = [
        'emp_id',
        'emp_name',
        'gender',
        'nationality',
        'biometric_code',
        'doi',
        'exit_date',
        'mobile_no',
        'company',
        'location',
        'subdepartment',
        'unit',
        'department',
        'designation',
        'wfemptype',
        'skill',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list()
    {
        $request = request();
        $search = '';
        $query = $this->select('masters_work.*', 'company_management.company_name', 'masters_location.location_name', 'masters_department.department_name', 'masters_unit.unit_name');
        $query = $query->leftJoin('company_management', 'masters_work.company', '=', 'company_management.id');
        $query = $query->leftJoin('masters_location', 'masters_work.location', '=', 'masters_location.id');
        $query = $query->leftJoin('masters_unit', 'masters_work.unit', '=', 'masters_unit.id');
        $query = $query->leftJoin('masters_department', 'masters_work.department', '=', 'masters_department.id');

        $org_total =  $query;
        $org_total_counts = $org_total->count();
        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_work.emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_work.emp_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('company_management.company_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_location.location_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('emp_id') && $request->emp_id) {
            $query = $query->where('masters_work.emp_id', 'LIKE', '%' . $request->emp_id . '%');
        }

        if ($request->has('emp_name') && $request->emp_name) {
            $query = $query->where('masters_work.emp_name', 'LIKE', '%' . $request->emp_name . '%');
        }
        if ($request->has('company_id') && $request->company_id) {
            $query = $query->where('masters_work.company', decryptId($request->company_id));
        }
        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('masters_work.location', decryptId($request->location_id));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('masters_work.unit', decryptId($request->unit_id));
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('masters_work.department', decryptId($request->department_id));
        }
        if ($request->has('wfemptype') && $request->wfemptype) {
            $query = $query->where('masters_work.wfemptype', 'LIKE', '%' . $request->wfemptype . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_work.status', decryptId($request->status));
        }


        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('id', 'DESC');

        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }

        $data = $query->get();

        $datas = array(
            'data' => $data,
            'total_records' => $org_total_counts,
            'filter_records' => $total_records,
        );
        return $datas;
    }

    public function UniqueCheck($data)
    {

        return $this->where($data['param'],  $data['value'])->get();
    }

    public function ExistuniqueCheck($data)
    {
        return $this->where($data['param'],  $data['value'])
            ->where('id', '!=', decryptId($data['id']))
            ->get();
    }


    // public function store($worktemp)
    // {
    //     $insertArray = [];
    //     $batchSize = 500;

    //     $worktemp = $worktemp->toArray();

    //     $chunks = array_chunk($worktemp, $batchSize);
    //     $insertedRecords = [];

    //     foreach ($chunks as $chunk) {
    //         foreach ($chunk as $item) {

    //             // $companyExists = DB::table('company_management')->where('company_name', $item['company'])->first();
    //             // $unitExists = DB::table('masters_unit')->where('unit_name', $item['unit'])->first();
    //             // $departmentExists = DB::table('masters_department')->where('department_name', $item['department'])->first();

    //             $companyExists =DB::table('company_management')->where('company_name', $item['company'])->exists();
    //             $unitExists =DB::table('masters_unit')->where('unit_name', $item['unit'])->exists();
    //             $departmentExists =DB::table('masters_department')->where('department_name', $item['department'])->exists();

    //             if ($companyExists) {
    //                 $errorMessage = "Company does not exist.";
    //                 $this->updateErrorStatus($item['emp_id'], $errorMessage);
    //                 continue;
    //             } elseif ($unitExists) {
    //                 $errorMessage = "Unit does not exist.";
    //                 $this->updateErrorStatus($item['emp_id'], $errorMessage);
    //                  continue;
    //             } elseif ($departmentExists) {
    //                 $errorMessage = "Department does not exist.";
    //                 $this->updateErrorStatus($item['emp_id'], $errorMessage);
    //                 continue;
    //             } else {
    //                 $valuesToInsertOrUpdate = [

    //                     'emp_name' => $item['emp_name'] ?? null,
    //                     'gender' => $item['gender'] ?? null,
    //                     'nationality' => $item['nationality'] ?? null,
    //                     'biometric_code' => $item['biometric_code'] ?? null,
    //                     'doi' => DBdatetimeformat($item['doi']) ?? null,
    //                     'exit_date' => DBdatetimeformat($item['exit_date']) ?? null,
    //                     'mobile_no' => $item['mobile_no'] ?? null,
    //                     'company' => $companyExists->id ?? null,
    //                     'subdepartment' => $item['subdepartment'] ?? null,
    //                     'unit' => $unitExists->id ?? null,
    //                     'department' => $departmentExists->id ?? null,
    //                     'designation' => $item['designation'] ?? null,
    //                     'wfemptype' => $item['wfemptype'] ?? null,
    //                     'skill' => $item['skill'] ?? null,
    //                     'status' => 1,
    //                     'created_by' => Auth::id(),
    //                 ];

    //                 $exists = $this->where('emp_id', $item['emp_id'])->exists();
    //                 if ($exists) {
    //                     $valuesToInsertOrUpdate['updated_at'] = now();
    //                 } else {
    //                     $valuesToInsertOrUpdate['created_at'] = now();
    //                 }

    //                 $insertedRecords = $this->updateOrInsert(
    //                     ['emp_id' => $item['emp_id']],
    //                     $valuesToInsertOrUpdate
    //                 );
    //                 $insertedRecords[] = array_merge(['emp_id' => $item['emp_id']], $valuesToInsertOrUpdate);

    //             }
    //         }
    //     }
    //     return $insertedRecords;
    // }
    public function store($worktemp)
    {
        $insertedRecords = [];
        $batchSize = 500;

        // Convert to array
        $worktemp = $worktemp->toArray();

        // Process in chunks
        foreach (array_chunk($worktemp, $batchSize) as $chunk) {
            foreach ($chunk as $item) {
                // Check existence of related entities
                $companyExists = DB::table('company_management')->where('company_name', $item['company'])->first();
                $unitExists = DB::table('masters_unit')->where('unit_name', $item['unit'])->first();
                $departmentExists = DB::table('masters_department')->where('department_name', $item['department'])->first();
               // dd($companyExists,$unitExists,$departmentExists,$item);
                // Validate existence
                // if (!$companyExists) {
                //     $this->updateErrorStatus($item['emp_id'], "Company does not exist.");
                //     continue;
                // } elseif (!$unitExists) {
                //     $this->updateErrorStatus($item['emp_id'], "Unit does not exist.");
                //     continue;
                // } elseif (!$departmentExists) {
                //     $this->updateErrorStatus($item['emp_id'], "Department does not exist.");
                //     continue;
                // }

                // Prepare data for insertion or update
                $valuesToInsertOrUpdate = [
                    'emp_name' => $item['emp_name'] ?? null,
                    'gender' => $item['gender'] ?? null,
                    'nationality' => $item['nationality'] ?? null,
                    'biometric_code' => $item['biometric_code'] ?? null,
                    'doi' => isset($item['doi']) ? DBdatetimeformat($item['doi']) : null,
                    'exit_date' => isset($item['exit_date']) ? DBdatetimeformat($item['exit_date']) : null,
                    'mobile_no' => $item['mobile_no'] ?? null,
                    'company' => $companyExists->id ?? null,
                    'subdepartment' => $item['subdepartment'] ?? null,
                    'unit' => $unitExists->id ?? null,
                    'department' => $departmentExists->id ?? null,
                    'designation' => $item['designation'] ?? null,
                    'wfemptype' => $item['wfemptype'] ?? null,
                    'skill' => $item['skill'] ?? null,
                    'status' => 1,
                    'created_by' => Auth::id(),
                ];

                // Check if record exists
                if ($this->where('emp_id', $item['emp_id'])->exists()) {
                    $valuesToInsertOrUpdate['updated_at'] = now();
                } else {
                    $valuesToInsertOrUpdate['created_at'] = now();
                }

                // Perform update or insert
                $this->updateOrInsert(['emp_id' => $item['emp_id']], $valuesToInsertOrUpdate);

                // Add to insertedRecords for tracking
                $insertedRecords[] = array_merge(['emp_id' => $item['emp_id']], $valuesToInsertOrUpdate);
            }
        }

        // Return inserted or updated records
        return $insertedRecords;
    }



    public function updates($id)
    {

        $request = request();

        $update_array = array(
            'emp_id' => $request->emp_id ?? null,
            'emp_name' => $request->emp_name ?? null,
            'gender' => $request->gender ?? null,
            'nationality' => $request->nationality ?? null,
            'biometric_code' => $request->biometric_code ?? null,
            'doi' => DBdatetimeformat($request->doi),
            'exit_date' => DBdatetimeformat($request->exit_date),
            'mobile_no' => $request->mobile_no ?? null,
            'company' => decryptId($request->company),
            'location' => decryptId($request->location),
            'subdepartment' => $request->subdepartment ?? null,
            'unit' => decryptId($request->unit),
            'department' => decryptId($request->department),
            'designation' => $request->designation ?? null,
            'wfemptype' => $request->wfemptype ?? null,
            'skill' => $request->skill ?? null,
            'status' => 1,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        );
        return $this->where('id', $id)->update($update_array);
    }
    public function updateErrorStatus($emp_id, $errorMessage)
    {
        $update_data = [
            'error_status' => 1,
            'error_remarks' => $errorMessage,
        ];

        return Worktemp::where('emp_id', $emp_id)->update($update_data);
    }
    public function getEmployeeID(){
        return $this->where('status',1)->where('trash','NO')->get();
    }

    public function statuschange($id)
    {
        $request = request();

        $type = $request->types;
        if ($type == 1) {
            $update_data = array(
                'status' => 0,
            );
        } else {
            $update_data = array(
                'status' => 1,
            );
        }

        return $this->where('id', $id)->update($update_data);
    }

    public function deleterecord($id)
    {

        $update_data = array(
            'status' => 0,
            'trash' => 'YES',
        );

        return $this->where('id', $id)->update($update_data);
    }

    public function reporting_manager()
    {
        return $this->belongsTo(User::class, 'reporting_manager_id', 'id');
    }
    public function exportdata()
    {
        $request = request();

        $search = '';
        $query = $this->select('masters_work.*', 'company_management.company_name', 'masters_location.location_name', 'masters_department.department_name', 'masters_unit.unit_name');
        $query = $query->leftJoin('company_management', 'masters_work.company', '=', 'company_management.id');
        $query = $query->leftJoin('masters_unit', 'masters_work.unit', '=', 'masters_unit.id');
        $query = $query->leftJoin('masters_location', 'masters_work.location', '=', 'masters_location.id');
        $query = $query->leftJoin('masters_department', 'masters_work.department', '=', 'masters_department.id');


        if (!empty($request->search)) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('masters_work.emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_work.emp_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('company_management.company_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_location.location_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_unit.unit_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('masters_department.department_name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->has('emp_id') && $request->emp_id) {
            $query = $query->where('masters_work.emp_id', 'LIKE', '%' . $request->emp_id . '%');
        }

        if ($request->has('emp_name') && $request->emp_name) {
            $query = $query->where('masters_work.emp_name', 'LIKE', '%' . $request->emp_name . '%');
        }
        if ($request->has('company_id') && $request->company_id) {
            $query = $query->where('masters_work.company', decryptId($request->company_id));
        }
        if ($request->has('location_id') && $request->location_id) {
            $query = $query->where('masters_work.location', decryptId($request->location_id));
        }
        if ($request->has('unit_id') && $request->unit_id) {
            $query = $query->where('masters_work.unit', decryptId($request->unit_id));
        }
        if ($request->has('department_id') && $request->department_id) {
            $query = $query->where('masters_work.department', decryptId($request->department_id));
        }
        if ($request->has('wfemptype') && $request->wfemptype) {
            $query = $query->where('masters_work.wfemptype', 'LIKE', '%' . $request->wfemptype . '%');
        }

        if ($request->has('status') && $request->status) {

            $query = $query->where('masters_work.status', decryptId($request->status));
        }

        $query->orderBy('id', 'DESC');

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select('masters_work.*', 'company_management.company_name', 'masters_location.location_name', 'masters_department.department_name', 'masters_unit.unit_name')
            ->leftJoin('company_management', 'masters_work.company', '=', 'company_management.id')
            ->leftJoin('masters_location', 'masters_work.location', '=', 'masters_location.id')
            ->leftJoin('masters_unit', 'masters_work.unit', '=', 'masters_unit.id')
            ->leftJoin('masters_department', 'masters_work.department', '=', 'masters_department.id')
            ->where('masters_work.id', $id)
            ->first();
        return $data;
    }

    public function ajaxList($where, $whereIn = [])
    {

        $query = $this->select('login_id', 'emp_name')->where('status', 1);

        if (count($where) > 0) {
            $query = $query->where($where);
        }

        if (count($whereIn) > 0) {
            $query = $query->whereRaw('FIND_IN_SET(?, role_id)', [$whereIn]);
        }


        $datas = $query->get();

        $list = [];
        foreach ($datas as $data) {
            $listvalue = [];
            $listvalue['id'] = encryptId($data->login_id);
            $listvalue['name'] = $data->emp_name;

            $list[] = $listvalue;
        }

        return $list;
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('masters_work'));
    }
}
