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
        'injury_id',
        'injury_person_id',
        'injury_person_name',
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
        'trash' => 'NO',
    ];


    public function getEmpdetails()
    {
        $request = request();

        $partyname = $request->input('partyname');
        $acc_prim_add = $request->input('acc_prim_add');
        $accident_id = $request->input('accident_id');
        $injuredPerson_type = $request->input('injuredPerson_type');

        if (decryptId($injuredPerson_type) == 1 || decryptId($injuredPerson_type) == 2) {
            $partyname = decryptId($partyname);
        } elseif (decryptId($injuredPerson_type) == 3) {
            $partyname = $partyname;
        }

        $query = $this->select('ims_accident_body_parts.*');

        if ($acc_prim_add == 'acc_prim_add') {
            $query->where('injury_person_id', $partyname)
                ->orWhere('injury_person_name', $partyname)
                ->where('status', 'T');
        } else {
            $query->where('injury_person_id', $partyname)
                ->orWhere('injury_person_name', $partyname)
                ->where('accident_id', $accident_id)
                ->where('status', 'Y');
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
                'injury_person_id' => decryptId($request->injuredPerson),
                'injury_person_name' => $request->injuredPerson,
                'imgMapdata' => postData($request, 'imgMapdata'),
                'body_part_image' => $filePath,
                'updated_by' => Auth::id(),
                'status' => 'Y'
            ];

            $updtBody =  $this->where('id', $request['body_prim_id'])->update($locdatas);
        } else {
            $locdatas = [
                'accident_id' => decryptId($request->accident_id),
                'injury_person_id' => decryptId($request->injuredPerson),
                'injury_person_name' => $request->injuredPerson,
                'imgMapdata' => postData($request, 'imgMapdata'),
                'body_part_image' => $filePath,
                'created_by' => Auth::id(),
                'status' => 'T'
            ];
            $updtBody =  $this->create($locdatas);
        }

        if (!empty($updtBody)) {
            $data = [
                'status' => true
            ];
        } else {
            $data = [
                'status' => false
            ];
        }

        echo json_encode($data);
    }

    public function delete_temprow()
    {
        $this->where('status', 'T')->delete();
    }

   
    public function updateStatusForIncident($accidentId, array $excludedEmpIds)
    {
        return $this->where('accident_id', $accidentId)
            ->where(function ($query) use ($excludedEmpIds) {
                $query->whereNotIn('injury_person_id', $excludedEmpIds)
                    ->whereNotIn('injury_person_name', $excludedEmpIds);
            })
            ->update(['status' => 'N']);
    }
}
