<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccidentBodyParts extends Model
{
    use  HasFactory;


    protected $table = 'ims_accident_body_parts';
    protected $primaryKey = 'id';

    protected $fillable = [
        'accident_id',
        'injury_person_type',
        'injury_person_id',
        'injury_person_name',
        'injury_person_designation',
        'injury_person_department_id',
        'nature_of_injury',
        'imgMapdata',
        'body_part_image',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];


    public function getEmpdetails()
    {
        $request = request();

        $partyname = $request->input('partyname');
        $acc_prim_id = $request->input('acc_prim_id');
        $acc_prim_add = $request->input('acc_prim_add');
        $injuredPerson_type = $request->input('injuredPerson_type');

        if (decryptId($injuredPerson_type) == 1) {
            $partyname = decryptId($partyname);
        }

        $query = $this->select('incident_body_parts.*');

        if ($acc_prim_add == 'acc_prim_add') {
            $query->where([
                ['inc_emp_id', '=', $partyname],
                ['status', '=', 'T']
            ]);
        } else {
            $query->where([
                ['inc_emp_id', '=', $partyname],
                ['accident_id', '=', $acc_prim_id],
                ['status', '=', 'Y']
            ]);
        }

        $get_data = $query->get();  // Executes the query
        //dd($get_data->toSql(), $query->getBindings());

        $response['empdata'] = $get_data;

        return response()->json($response);
    }

    public function addInjury()
    {
        $request = request();
       
        $folderPath = 'uploads/accident_body_parts/';

        $base64String = $_POST['bodypartimage'];
        // Decode the base64 string

        $image = base64_decode(str_replace('data:image/png;base64,', '', $base64String));

        // Save the image as a file
        $fileName = time() . uniqid() . '.png';
        $filePath = $folderPath . $fileName;
        Storage::put($filePath, $image);


        // dd($file_put_contents);

        if ($request['body_prim_id'] != 0) {
            $locdatas = [
                'accident_id' => decryptId($request->accident_id),
                'injury_person_type' => decryptId($request->injury_person_type),
                'injury_person_id' => decryptId($request->injury_person_id),
                'injury_person_name' => $request->injury_person_name,
                'injury_person_designation' => $request->injury_person_designation,
                'injury_person_department_id' => decryptId($request->injury_person_department_id),
                'nature_of_injury' => decryptId($request->nature_of_injury),
                'imgMapdata' => postData($request, 'imgMapdata'),
                'body_part_image' => $filePath,
                'updated_by' => Auth::id()
            ];
            return  $this->where('id', $request['body_prim_id'])->update($locdatas);
        } else {
            $locdatas = [
             'accident_id' => decryptId($request->accident_id),
                'injury_person_type' => decryptId($request->injury_person_type),
                'injury_person_id' => decryptId($request->injury_person_id),
                'injury_person_name' => $request->injury_person_name,
                'injury_person_designation' => $request->injury_person_designation,
                'injury_person_department_id' => decryptId($request->injury_person_department_id),
                'nature_of_injury' => decryptId($request->nature_of_injury),
                'imgMapdata' => postData($request, 'imgMapdata'),
                'body_part_image' => $filePath,
                'created_by' => Auth::id()
            ];
            return  $this->create($locdatas);
        }

   
    }

    public function delete_temprow()
    {
        $this->where('status', 'T')->delete();
    }

    public function updateStatusForIncident($incidentId, $excludedEmpIds)
    {
        return $this->where('inc_id', $incidentId)
            ->whereNotIn('inc_emp_id', $excludedEmpIds)
            ->update(['status' => 'N']);
    }
}
