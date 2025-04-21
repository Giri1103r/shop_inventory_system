<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IncidentBodyParts extends Model
{
    use  HasFactory;


    protected $table = 'ims_incident_body_parts';
    protected $primaryKey = 'id';

    protected $fillable = [
        'incident_id',
        'random_id',
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
        // dd($acc_prim_add);
        $random_id = $request->input('random_id');
        $injuredPerson_type = $request->input('injuredPerson_type');

        if (decryptId($injuredPerson_type) == 1 || decryptId($injuredPerson_type) == 2) {
            $partyname = decryptId($partyname);
        } elseif (decryptId($injuredPerson_type) == 3) {
            $partyname = $partyname;
        }

        $query = $this->select('ims_incident_body_parts.*');

        if ($acc_prim_add == 'acc_prim_add') {
            $query->where('random_id', $random_id)
                ->where('status', 'T')
                ->where(function ($q) use ($partyname) {
                    $q->where('injury_person_id', $partyname)
                        ->orWhere('injury_person_name', $partyname);
                });
        } else {
            $query->where('random_id', $random_id)
                ->where('status', 'Y')
                ->where(function ($q) use ($partyname) {
                    $q->where('injury_person_id', $partyname)
                        ->orWhere('injury_person_name', $partyname);
                });
        }

        $get_data = $query->get();  // Executes the query
        //dd($get_data->toSql(), $query->getBindings());

        $response['empdata'] = $get_data;

        return response()->json($response);
    }

    public function addInjury()
    {
        $request = request();
        $random_id = $request->random_id;
        $folderPath = 'incident/body_parts/' . $random_id;

        $base64String = $request->bodypartimage;

        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
            $imageType = $matches[1]; // Extract extension (png, jpg, jpeg)
            $imageData = substr($base64String, strpos($base64String, ',') + 1);
            $imageData = base64_decode($imageData);

            if ($imageData === false) {
                return response()->json(['status' => false, 'message' => 'Invalid Base64 image'], 400);
            }

            // Generate unique filename
            $fileName = time() . uniqid() . '.' . $imageType;
            $filePath = $folderPath . '/' . $fileName;

            Storage::put($filePath, $imageData);

            $storedImagePath = $filePath;
        }


        if ($request['body_prim_id'] != 0) {
            $locdatas = [
                'incident_id' => decryptId($request->incident_id),
                'random_id' => $random_id,
                'injury_person_id' => decryptId($request->injuredPerson),
                'injury_person_name' => $request->injuredPerson,
                'imgMapdata' => postData($request, 'imgMapdata'),
                'body_part_image' => $storedImagePath,
                'updated_by' => Auth::id(),
                'status' => 'Y'
            ];
            $updtBody =  $this->where('id', $request['body_prim_id'])->update($locdatas);
        } else {
            $locdatas = [
                'incident_id' => decryptId($request->incident_id),
                'random_id' => $random_id,
                'injury_person_id' => decryptId($request->injuredPerson),
                'injury_person_name' => $request->injuredPerson,
                'imgMapdata' => postData($request, 'imgMapdata'),
                'body_part_image' => $storedImagePath,
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


    public function updateStatusForIncident($random_id, $incident_id, array $excludedEmpIds)
    {

        // dd($random_id, $incident_id);
        return $this->where('random_id', $random_id)
            ->where(function ($query) use ($excludedEmpIds) {
                $query->whereNotIn('injury_person_id', $excludedEmpIds)
                    ->whereNotIn('injury_person_name', $excludedEmpIds);
            })
            ->update([
                'incident_id' => $incident_id,
                'status' => 'N'
            ]);
    }
}
