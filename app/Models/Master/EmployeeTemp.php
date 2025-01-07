<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

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
        'updated_at',
        'created_at',

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
        $batchSize = 500;
        $insertArray = [];

        $chunks = array_chunk($data['Result'], $batchSize);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $item) {
                   
                if (empty($item['Emp_OfficialMail'])) {
                    Log::info("Skipped record due to empty email", [
                        'emp_id' => $item['pk_Emp_Code'],
                        'email' => $item['Emp_OfficialMail'],
                    ]);
                    continue; 
                }
                $emailExists = $this->where('email', $item['Emp_OfficialMail'])->where('emp_id', '!=', $item['pk_Emp_Code'])->exists();

                if ($emailExists) {
                    $errorMessage = "Email already exists.";
                    $this->updateErrorStatus($item['pk_Emp_Code'], $errorMessage);
                    Log::info("Skipped record due to already exists", [
                        'emp_id' => $item['pk_Emp_Code'],
                        'email' => $item['Emp_OfficialMail'],
                    ]);
                    continue; 
                }
                $valuesToInsertOrUpdate = [
                    'emp_id' => isset($item['pk_Emp_Code']) ? $item['pk_Emp_Code'] : null,
                    'emp_name' => isset($item['Emp_Name']) ? $item['Emp_Name'] : null,
                    'gender' => isset($item['Emp_Gender']) ? $item['Emp_Gender'] : null,
                    'user_role' => isset($item['Emp_Rolename']) ? $item['Emp_Rolename'] : null,
                    'joining_date' => !empty($item['Emp_JoiningDate']) ? DBdatetimeformat($item['Emp_JoiningDate']) : null,
                    'status' => isset($item['Emp_Active']) ? $item['Emp_Active'] : null,
                    'employee_status' => isset($item['Emp_Status']) ? $item['Emp_Status'] : null,
                    'email' => isset($item['Emp_OfficialMail']) ? $item['Emp_OfficialMail'] : null,
                    'reporting_manager' => isset($item['Emp_FirstApprover']) ? $item['Emp_FirstApprover'] : null,
                    'upload_status' => 0,
                    'error_status' => 0,
                    'error_remarks' => null,
                ];

                $exists = $this->where('emp_id', $item['pk_Emp_Code'])->exists();

                if ($exists) {
                    $valuesToInsertOrUpdate['updated_at'] = now();
                } else {
                    $valuesToInsertOrUpdate['created_at'] = now();
                }

                $this->updateOrInsert(
                    ['emp_id' => $item['pk_Emp_Code']],
                    $valuesToInsertOrUpdate
                );
            }
        }
        return response()->json(['message' => 'Data processed successfully.']);
    }

    // public function store($data)
    // {
    //     try {
    //         $batchSize = 500;

    //         // Split data into chunks to handle large datasets
    //         $chunks = array_chunk($data['Result'], $batchSize);

    //         foreach ($chunks as $chunk) {
    //             $insertArray = [];

    //             foreach ($chunk as $item) {
    //                 $insertArray[] = [
    //                     'emp_id' => $item['pk_Emp_Code'] ?? null,
    //                     'emp_name' => $item['Emp_Name'] ?? null,
    //                     'gender' => $item['Emp_Gender'] ?? null,
    //                     'user_role' => $item['Emp_Rolename'] ?? null,
    //                     'joining_date' => !empty($item['Emp_JoiningDate']) ? DBdatetimeformat($item['Emp_JoiningDate']) : null,
    //                     'status' => $item['Emp_Active'] ?? null,
    //                     'employee_status' => $item['Emp_Status'] ?? null,
    //                     'email' => $item['Emp_OfficialMail'] ?? null,
    //                     'reporting_manager' => $item['Emp_FirstApprover'] ?? null,
    //                     'upload_status' => 0,
    //                     'error_status' => 0,
    //                     'error_remarks' => null,
    //                     'created_at' => now(),
    //                     'updated_at' => now(),
    //                 ];
    //             }

    //             // Insert the batch into the database
    //             $this->insert($insertArray);
    //         }

    //         return response()->json(['message' => 'Data processed successfully.']);
    //     } catch (\Exception $ex) {
    //         // Handle exceptions and return an error response
    //         return response()->json([
    //             'message' => 'An error occurred while processing data.',
    //             'error' => $ex->getMessage(),
    //         ], 500);
    //     }
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
    public function updateErrorStatus($emp_id, $errorMessage)
    {
        $update_data = [
            'error_status' => 1,
            'error_remarks' => $errorMessage,
        ];

        return $this->where('emp_id', $emp_id)->update($update_data);
    }
    public function updateAllErrorStatus()
    {

        $result = $this->where('error_status', '!=', 1)->update(['error_status' => 1]);
        return $result;
    }


    public function list()
    {
        $request = request();
        $search = '';

        $query = $this->select('masters_employee_temp.*');
        $query = $this->where('error_status', 1);

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query->orWhere('emp_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('emp_name', 'LIKE', '%' . $search . '%');
            });
        }

        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('id', 'DESC');


        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }

        $data = $query->get();

        $datas = [
            'data' => $data,
            'total_records' => $total_records
        ];

        return $datas;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('masters_employee_temp'));
    }
}
