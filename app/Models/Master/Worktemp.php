<?php

namespace App\Models\Master;

use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Worktemp extends Model
{
    use  HasFactory;


    protected $table = 'masters_work_temp';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'emp_id',
        'emp_name',
        'gender',
        'nationality',
        'biometric_code',
        'doi',
        'exit_date',
        'mobile_no',
        'company',
        'subdepartment',
        'unit',
        'department',
        'designation',
        'status',
        'wfemptype',
        'skill',
        'upload_status',
        'created_at',
        'updated_at',
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
        $batchSize = 500;
        $chunks = array_chunk($data, $batchSize);
        foreach ($chunks as $chunk) {
            foreach ($chunk as $item) {
                $cleanUnit = isset($item['Unit']) ? str_replace(["\r", "\n"], '', trim($item['Unit'])) : null;

                $status = isset($item['Status']) ? ($item['Status'] === 'Y' ? 1 : 0) : null;

              
                $valuesToInsertOrUpdate = [
                    'emp_name' => isset($item['EmpName']) ? $item['EmpName'] : null,
                    'gender' => isset($item['Gender']) ? $item['Gender'] : null,
                    'nationality' => isset($item['Nationality']) ? $item['Nationality'] : null,
                    'biometric_code' => isset($item['BiometricCode']) ? $item['BiometricCode'] : null,
                    'doi' => DBdatetimeformat($item['Doi']),
                    'exit_date' => $item['ExitDate'] != '' ? DBdatetimeformat($item['ExitDate']) : null,
                    'mobile_no' => isset($item['MobileNo']) ? $item['MobileNo'] : null,
                    'company' => isset($item['Company']) ? $item['Company'] : null,
                    'subdepartment' => isset($item['Subdepartment']) ? $item['Subdepartment'] : null,
                    'unit' => $cleanUnit,
                    'department' => isset($item['Dept']) ? $item['Dept'] : null,
                    'designation' => isset($item['Designation']) ? $item['Designation'] : null,
                    'status' => $status,
                    'wfemptype' => isset($item['WfEmpType']) ? $item['WfEmpType'] : null,
                    'skill' => isset($item['Skill']) ? $item['Skill'] : null,
                    'upload_status' => 0,
                    'error_status' => 0,
                    'error_remarks' => null,

                ];

                $exists = $this->where('emp_id', $item['EmpId'])->exists();

                if ($exists) {
                    $valuesToInsertOrUpdate['updated_at'] = now();
                } else {
                    $valuesToInsertOrUpdate['created_at'] = now();
                }

                $this->updateOrInsert(
                    ['emp_id' => $item['EmpId']],
                    $valuesToInsertOrUpdate
                );
            }
        }

        return response()->json(['message' => 'Data processed successfully.']);
    }


    public function updates($workid)
    {

        $request = request();
        $update_data = array(
            'upload_status' => 1,
            'error_status' => 0,
        );
        return $this->where('emp_id', $workid)->update($update_data);
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

        $query = $this->select('masters_work_temp.*');
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
        static::addGlobalScope(new TrashScope('masters_work_temp'));
    }
}
