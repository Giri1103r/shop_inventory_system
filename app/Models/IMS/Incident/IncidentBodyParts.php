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
        'row_id',
        'injury_id',
        'injury_person_id',
        'injured_person_type',
        'injury_person_name',
        'imgMapdata',
        'body_parts',
        'body_parts_label',
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
        // dd($request);
        $partyname = $request->input('partyname');
        $acc_prim_add = $request->input('acc_prim_add');
        $incident_id = $request->input('incident_id');
        $random_id = $request->input('random_id');
        $injuredPerson_type = $request->input('injuredPerson_type');
        $injury_id = $request->input('injury_id');
        // dd($injury_id);

        if (decryptId($injuredPerson_type) == 1 || decryptId($injuredPerson_type) == 2) {
            $partyname = decryptId($partyname);
        } elseif (decryptId($injuredPerson_type) == 3) {
            $partyname = $partyname;
        }

        $query = $this->select('ims_incident_body_parts.*');

        if ($acc_prim_add == 'acc_prim_add') {
            $query->where('random_id', $random_id)
                ->where('injured_person_type', decryptId($injuredPerson_type))
                ->where('status', 'T')
                ->where('trash', 'NO')
                ->where(function ($q) use ($partyname) {
                    $q->where('injury_person_id', $partyname)
                        ->orWhere('injury_person_name', $partyname);
                });
        } elseif ($acc_prim_add == 'acc_prim_edit') {
            $query->where('random_id', $random_id)
                ->where('incident_id', $incident_id)
                ->where('injury_id', $injury_id)
                ->where('injured_person_type', decryptId($injuredPerson_type))
                ->where('trash', 'NO')
                ->where(function ($q) {
                    $q->where('status', 'Y')
                        ->orWhere('status', 'N')
                        ->orWhere('status', 'T');
                })
                ->where(function ($q) use ($partyname) {
                    $q->where('injury_person_id', $partyname)
                        ->orWhere('injury_person_name', $partyname);
                });
        } else {
            $query->where('random_id', $random_id)
                ->where('incident_id', $incident_id)
                ->where('injury_id', decryptId($injury_id))
                ->where('injured_person_type', decryptId($injuredPerson_type))
                ->where('status', 'Y')
                ->where('trash', 'NO')
                ->where(function ($q) use ($partyname) {
                    $q->where('injury_person_id', $partyname)
                        ->orWhere('injury_person_name', $partyname);
                });
        }

        $get_data = $query->get();

        $response['empdata'] = $get_data;

        return response()->json($response);
    }

    public function apigetEmpdetails()
    {
        $request = request();
        $partyname = $request->input('partyname');
        $random_id = $request->input('randomID');
        $rowId = $request->input('rowId');
        $acc_prim_add = $request->input('acc_prim_add');
        $injury_id = $request->input('injury_id');
        $injuredPerson_type = $request->input('injuredPerson_type');
        // $incident_id = $request->input('incident_id');


        $query = IncidentBodyParts::query();

        if ($acc_prim_add == "acc_prim_add") {
            $query->where('random_id', $random_id)
                ->where('id', $partyname)
                ->where('row_id', $rowId)
                ->where('injured_person_type', $injuredPerson_type)
                ->where(function ($q) use ($injury_id) {
                    $q->where('injury_id', $injury_id)
                        ->orWhereNull('injury_id');
                })
                ->whereIn('status', ['Y', 'N', 'T'])
                ->where('trash', 'NO');
        } else {
            $query->where('random_id', $random_id)
                ->where('id', $partyname)
                ->where('row_id', $rowId)
                ->where('injured_person_type', $injuredPerson_type)
                ->where('injury_id', $injury_id)
                ->where('status', 'Y')
                ->where('trash', 'NO');
        }

        $get_data = $query->get();
        $response['empdata'] = $get_data;
        // dd($get_data);
        return response()->json($response);
    }
    public function addInjury()
    {
        $request = request();
        $random_id = $request->random_id;
        $folderPath = 'incident/body_parts/' . $random_id;

        Storage::makeDirectory($folderPath);

        // Set permission to 0777 (you must use chmod with full path)
        $fullPath = storage_path("app/public/uploads/{$folderPath}");
        chmod($fullPath, 0777);

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


        if ($request['body_prim_id'] != 0 && $request['incident_id'] != 0) {
            $locdatas = [
                'incident_id' => $request->incident_id,
                'random_id' => $random_id,
                'injury_person_id' => decryptId($request->injuredPerson),
                'injured_person_type' => decryptId($request->injury_person_type),
                'injury_person_name' => $request->injuredPerson,
                'imgMapdata' => postData($request, 'imgMapdata'),
                'body_parts' => $request->humanbodyinjury,
                'body_parts_label' => $request->humanbodyinjurylabel,
                'body_part_image' => $storedImagePath,
                'updated_by' => Auth::id(),
                'status' => 'T'
            ];
            $updtBody =  $this->where('id', $request['body_prim_id'])->update($locdatas);
        } elseif ($request['body_prim_id'] != 0 && $request['incident_id'] == 0) {
            $locdatas = [
                'incident_id' => $request->incident_id,
                'random_id' => $random_id,
                'injury_person_id' => decryptId($request->injuredPerson),
                'injured_person_type' => decryptId($request->injury_person_type),
                'injury_person_name' => $request->injuredPerson,
                'imgMapdata' => postData($request, 'imgMapdata'),
                'body_parts' => $request->humanbodyinjury,
                'body_parts_label' => $request->humanbodyinjurylabel,
                'body_part_image' => $storedImagePath,
                'updated_by' => Auth::id(),
                'status' => 'T'
            ];
            $updtBody =  $this->where('id', $request['body_prim_id'])->update($locdatas);
        } elseif ($request['body_prim_id'] == 0 && $request['incident_id'] != 0) {
            $locdatas = [
                'incident_id' => $request->incident_id,
                'random_id' => $random_id,
                'injury_person_id' => decryptId($request->injuredPerson),
                'injured_person_type' => decryptId($request->injury_person_type),
                'injury_person_name' => $request->injuredPerson,
                'imgMapdata' => postData($request, 'imgMapdata'),
                'body_parts' => $request->humanbodyinjury,
                'body_parts_label' => $request->humanbodyinjurylabel,
                'body_part_image' => $storedImagePath,
                'updated_by' => Auth::id(),
                'status' => 'T'
            ];
            $updtBody =  $this->create($locdatas);
        } else {
            $locdatas = [
                'incident_id' => decryptId($request->incident_id),
                'random_id' => $random_id,
                'injury_person_id' => decryptId($request->injuredPerson),
                'injured_person_type' => decryptId($request->injury_person_type),
                'injury_person_name' => $request->injuredPerson,
                'imgMapdata' => postData($request, 'imgMapdata'),
                'body_parts' => $request->humanbodyinjury,
                'body_parts_label' => $request->humanbodyinjurylabel,
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

    public function addInjuryApi()
    {
        $request = request();

        $random_id = $request->random_id;
        $folderPath = 'incident/body_parts/' . $random_id;
        Storage::makeDirectory($folderPath);

        // Set permission to 0777 (you must use chmod with full path)
        $fullPath = storage_path("app/public/uploads/{$folderPath}");
        chmod($fullPath, 0777);

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


        if ($request['body_prim_id'] != 0 && $request['incident_id'] != 0) {
            $locdatas = [
                'incident_id' => $request->incident_id,
                'random_id' => $random_id,
                'row_id' => $request->row_id,
                'injured_person_type' => $request->injury_person_type,
                'imgMapdata' => postData($request, 'imgMapdata'),
                'body_parts' => $request->humanbodyinjury,
                'body_parts_label' => $request->humanbodyinjurylabel,
                'body_part_image' => $storedImagePath,
                'updated_by' => Auth::id(),
                'status' => 'T'
            ];
            if ($request->injury_person_type == 3) {
                $locdatas['injury_person_name'] = $request->injuredPerson;
            } else {
                $locdatas['injury_person_id'] = $request->injuredPerson;
            }
            $updtBody =  $this->where('id', $request['body_prim_id'])->update($locdatas);
        } elseif ($request['body_prim_id'] != 0 && $request['incident_id'] == 0) {
            $locdatas = [
                'incident_id' => $request->incident_id,
                'random_id' => $random_id,
                'row_id' => $request->row_id,
                'injured_person_type' => $request->injury_person_type,
                'imgMapdata' => postData($request, 'imgMapdata'),
                'body_parts' => $request->humanbodyinjury,
                'body_parts_label' => $request->humanbodyinjurylabel,
                'body_part_image' => $storedImagePath,
                'updated_by' => Auth::id(),
                'status' => 'T'
            ];
            if ($request->injury_person_type == 3) {
                $locdatas['injury_person_name'] = $request->injuredPerson;
            } else {
                $locdatas['injury_person_id'] = $request->injuredPerson;
            }
            $updtBody =  $this->where('id', $request['body_prim_id'])->update($locdatas);
        } elseif ($request['body_prim_id'] == 0 && $request['incident_id'] != 0) {
            $locdatas = [
                'incident_id' => $request->incident_id,
                'random_id' => $random_id,
                'row_id' => $request->row_id,
                'injured_person_type' => $request->injury_person_type,
                'imgMapdata' => postData($request, 'imgMapdata'),
                'body_parts' => $request->humanbodyinjury,
                'body_parts_label' => $request->humanbodyinjurylabel,
                'body_part_image' => $storedImagePath,
                'updated_by' => Auth::id(),
                'status' => 'T'
            ];
            if ($request->injury_person_type == 3) {
                $locdatas['injury_person_name'] = $request->injuredPerson;
            } else {
                $locdatas['injury_person_id'] = $request->injuredPerson;
            }
            $updtBody =  $this->create($locdatas);
        } else {

            $locdatas = [
                'incident_id' => $request->incident_id,
                'random_id' => $random_id,
                'row_id' => $request->row_id,
                'injured_person_type' => $request->injury_person_type,
                'imgMapdata' => postData($request, 'imgMapdata'),
                'body_parts' => $request->humanbodyinjury,
                'body_parts_label' => $request->humanbodyinjurylabel,
                'body_part_image' => $storedImagePath,
                'created_by' => Auth::id(),
                'status' => 'T'
            ];
            if ($request->injury_person_type == 3) {
                $locdatas['injury_person_name'] = $request->injuredPerson;
            } else {
                $locdatas['injury_person_id'] = $request->injuredPerson;
            }
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
    // public function delete_temprow()
    // {
    //     $this->where('status', 'T')->delete();
    // }
    public function delete_temprow($random_id, $injured_person_type, $injured_person_id)
    {
        return $this->where('status', 'T')
            ->where('random_id', $random_id)
            ->where('injured_person_type', $injured_person_type)
            ->where(function ($q) use ($injured_person_type, $injured_person_id) {
                if ($injured_person_type == 3) {
                    $q->where('injury_person_name', $injured_person_id);
                } else {
                    $q->where('injury_person_id', $injured_person_id);
                }
            })
            ->delete();
    }


    // public function updateStatusForIncident($random_id, $incident_id, $injured_person_type, array $excludedEmpIds)
    // {

    //     return $this->where('random_id', $random_id)
    //         ->where('injured_person_type', $injured_person_type)
    //         ->where(function ($query) use ($excludedEmpIds) {
    //             $query->whereNotIn('injury_person_id', $excludedEmpIds)
    //                 ->whereNotIn('injury_person_name', $excludedEmpIds);
    //         })
    //         ->update([
    //             'incident_id' => $incident_id,
    //             'status' => 'N'
    //         ]);
    // }

    public function updateStatusForIncident($random_id, $incident_id, $injured_person_type, array $excludedValues)
    {
        $query = $this->where('random_id', $random_id)
            ->where('injured_person_type', $injured_person_type)
            ->where('status', 'T');

        // Exclude by type
        if ($injured_person_type == 1 || $injured_person_type == 2) {
            // Exclude by ID
            $query->whereNotIn('injury_person_id', $excludedValues);
        } elseif ($injured_person_type == 3) {
            // Exclude by Name
            $query->whereNotIn('injury_person_name', $excludedValues);
        }

        // Update the matching rows
        return $query->update([
            'incident_id' => $incident_id,
            'status'      => 'N'
        ]);
    }


    public function updateBodyParts($random_id, $injury_person_id, $incident_id, array $injuryDetailsArray)
    {

        $update_array = [
            'injury_person_id' => $injuryDetailsArray['injury_person_id'] ?? null,
            'injured_person_type' => $injuryDetailsArray['injury_person_type'] ?? null,
            'injury_person_name' => $injuryDetailsArray['injury_person_name'] ?? null,
            'status' => 'N'
        ];

        $update_array = $this->where('injury_id', $injury_person_id)->where('incident_id', $incident_id)
            ->where('random_id', $random_id)->update($update_array);


        return $update_array;
    }

    public function countBodyPart()
    {
        // Step 1: Get all related incident body part IDs (non-trashed)
        $bodyIds = DB::table('ims_initial_incident as inc')
            ->leftJoin('ims_incident_body_parts as body', 'body.incident_id', '=', 'inc.id')
            ->where('inc.trash', 'NO')
            ->pluck('body.id')
            ->filter()
            ->toArray();

        // Step 2: If no body part entries found, return all with count = 0
        if (empty($bodyIds)) {
            return DB::table('ims_accident_injury_parts')
                ->pluck('part_name')
                ->map(function ($part) {
                    return [
                        'body_part' => $part,
                        'count' => 0
                    ];
                })->toArray();
        }

        // Step 3: Count matching parts using complex LIKE condition
        $bodyParts = DB::table('ims_accident_injury_parts as t1')
            ->select('t1.part_name', DB::raw('COALESCE(COUNT(t2.body_parts_label), 0) as count'))
            ->join('ims_incident_body_parts as t2', function ($join) {
                $join->on(
                    DB::raw("CONCAT(',', REPLACE(t2.body_parts_label, ' ', ''), ',')"),
                    'LIKE',
                    DB::raw("CONCAT('%,', REPLACE(t1.part_name, ' ', ''), ',%')")
                );
            })
            ->whereIn('t2.id', $bodyIds)
            ->groupBy('t1.part_name')
            ->get();

        // Step 4: Build map of counts for easier lookup
        $obserdata = [];
        foreach ($bodyParts as $item) {
            $obserdata[$item->part_name] = (array) $item;
        }

        // Step 5: Ensure all parts are included in result, with default count = 0
        $result = [];
        $allParts = DB::table('ims_accident_injury_parts')->pluck('part_name');

        foreach ($allParts as $part) {
            $result[] = [
                'body_part' => $part,
                'count' => $obserdata[$part]['count'] ?? 0
            ];
        }

        return $result;
    }
}
