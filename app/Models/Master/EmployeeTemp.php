<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeTemp extends Model
{
    use  HasFactory;


    protected $table = 'masters_employee_temp';
    protected $primaryKey = 'id';

    protected $fillable = [
        'emp_id',
        'emp_name',
        'gender',
        'nationality',
        'user_role',
        'id_type',
        'id_number',
        'joining_date',
        'mobile_no',
        'company',
        'location',
        'email',
        'unit',
        'department',
        'designation',
        'status',
        'employee_status',
        'reporting_manager',
        'upload_status',
        'error_status',
        'error_remarks',

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

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

  
    public function store($data)
{
    $insertArray = [];

    foreach ($data['Result'] as $item) {
        $insertArray[] = [
            'emp_id' => isset($item['pk_Emp_Code']) ? $item['pk_Emp_Code'] : null,
            'emp_name' => isset($item['Emp_Name']) ? $item['Emp_Name'] : null,
            'gender' => isset($item['Emp_Gender']) ? $item['Emp_Gender'] : null,
            'user_role' => isset($item['Emp_Rolename']) ? $item['Emp_Rolename'] : null,
            'joining_date' => !empty($item['Emp_JoiningDate']) ? DBdatetimeformat($item['Emp_JoiningDate']) : null,
            'status' => isset($item['Emp_Active']) ? $item['Emp_Active'] : null,
            'employee_status' => isset($item['Emp_Status']) ? $item['Emp_Status'] : null,
            'email' => isset($item['Emp_OfficialMail']) ? $item['Emp_OfficialMail'] : null,
            'upload_status' => 0,
            'error_status' => 0,
            'error_remarks' => null,
        ];
    }

    $batchSize = 500;
    $chunks = array_chunk($insertArray, $batchSize);

    foreach ($chunks as $chunk) {
        $this->insert($chunk); 
    }

    return $chunks;
}

    // public function store($data)
    // {

    //     $insertArray = [];
    //     foreach ($data as $item) {

    //         // $cleanUnit = isset($item['Unit']) ? str_replace(["\r", "\n"], '', trim($item['Unit'])) : null;
    //         $insertArray[] = [
    //             'emp_id' => isset($item['pk_Emp_Code']) ? $item['pk_Emp_Code'] : null,
    //             'emp_name' => isset($item['Emp_Name']) ? $item['Emp_Name'] : null,
    //             'gender' => isset($item['Emp_Gender']) ? $item['Emp_Gender'] : null,
    //             // 'nationality' => isset($item['Nationality']) ? $item['Nationality'] : null,
    //             'user_role' => isset($item['Emp_Rolename']) ? $item['Emp_Rolename'] : null,
    //             'joining_date' => $item['Emp_JoiningDate'] != '' ? DBdatetimeformat($item['Emp_JoiningDate']) : null,
    //             // 'mobile_no' => isset($item['MobileNo']) ? $item['MobileNo'] : null,
    //             // 'company' => isset($item['Company']) ? $item['Company'] : null,
    //             // 'subdepartment' => isset($item['Subdepartment']) ? $item['Subdepartment'] : null,
    //             // 'unit' => $cleanUnit,
    //             // 'department' => isset($item['Dept']) ? $item['Dept'] : null,
    //             // 'designation' => isset($item['Designation']) ? $item['Designation'] : null,
    //             'status' => isset($item['Emp_Active']) ? $item['Emp_Active'] : null,
    //             'employee_status' => isset($item['Emp_Status']) ? $item['Emp_Status'] : null,
    //             'email' => isset($item['Emp_OfficialMail']) ? $item['Emp_OfficialMail'] : null,
    //             'upload_status' => 0,
    //             'error_status' => 0,
    //             'error_remarks' => null,
    //         ];
    //     }

    //     $batchSize = 500;
    //     $chunks = array_chunk($insertArray, $batchSize);

    //     foreach ($chunks as $chunk) {
    //         $insert_Array =  $this->insert($chunk);
    //     }

    //     return  $insert_Array;
    // }

    public function updates($empid)
    {

        // dd($workid);
        $request = request();
        $update_data = array(
            'upload_status' => 1,
            'error_status' => 0,
        );
        return $this->where('emp_id', $empid)->update($update_data);
    }

    public function updateAllErrorStatus()
    {

        $result = $this->where('error_status', '!=', 1)->update(['error_status' => 1]);
        return $result;
    }

    public function exportdata()
    {
        $request = request();
        $search = '';
        $query = $this->select('company_management.*');
        if ($request->search != null || $request->search != '') {
            $search = $request->search;

            $query =  $query->Where(function ($query) use ($search) {
                $query->orWhereRaw('company_name LIKE "%' . $search . '%"');
            });
        }
        if ($request->has('company_id') && $request->company_id) {
            $query = $query->where('company_id', 'LIKE', '%' . $request->company_id . '%');
        }
        if ($request->has('company_name') && $request->company_name) {
            $query = $query->where('company_name', 'LIKE', '%' . $request->company_name . '%');
        }
        if ($request->has('status') && $request->status) {

            $query = $query->where('company_management.status', decryptId($request->status));
        }

        return  $query->get();
    }

    public function selectOne($id)
    {

        $data = $this->select(
            'company_management.*'
        )
            ->where('company_management.id', $id)
            ->first();

        return $data;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('masters_employee_temp'));
    }
}
