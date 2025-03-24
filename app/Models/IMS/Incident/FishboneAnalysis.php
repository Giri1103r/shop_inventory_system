<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FishboneAnalysis extends Model
{
    use  HasFactory;


    protected $table = 'ims_incident_fishboneanalysis';
    protected $primaryKey = 'id';

    protected $fillable = [
        'incident_id',
        'accident_id',
        'fire_id',
        'investigation_id',
        'fishbone',
        'fishbone_image',
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

    // public function storeFishbone($accident_id, $incident_id, $fire_id ,$investigation_id)
    // {
    //     $request = request();

    //     // Get the fishbone data from the request
    //     $fishboneData = $request->input('fishbone', []);

    //     // Prepare the data to be saved in JSON format
    //     $dataToSave = [
    //         'incident_id' => $incident_id,
    //         'accident_id' => $accident_id,
    //         'fire_id' => $fire_id,
    //         'investigation_id' => $investigation_id,
    //         'created_by' => Auth::id(),
    //         'created_at' => now(),
    //         'fishbone' => json_encode($fishboneData) // Convert fishbone data to JSON
    //     ];

    //     // Save the data to the database (assuming a model named Fishbone)
    //     $this->create($dataToSave);
    // }


    public function storeFishbone($accident_id, $incident_id, $fire_id, $investigation_id)
    {
        $request = request();

        $fishboneData = $request->input('fishbone', []);
        $imageData = $request->input('fishbone_image');

        $dataToSave = [
            'incident_id' => $incident_id,
            'accident_id' => $accident_id,
            'fire_id' => $fire_id,
            'investigation_id' => $investigation_id,
            'created_by' => Auth::id(),
            'created_at' => now(),
            'fishbone' => json_encode($fishboneData)
        ];

        if ($imageData) {
            $image = str_replace('data:image/png;base64,', '', $imageData);
            $image = str_replace(' ', '+', $image);
            $imageDataDecoded = base64_decode($image);

            $fileName = 'fishbone_' . time() . '.png';

            if ($incident_id) {
                $directory = public_path('uploads/initial/incident/fishbone_images/' . $incident_id);
            } elseif ($accident_id) {
                $directory = public_path('uploads/initial/incident/fishbone_images/' . $accident_id);
            } elseif ($fire_id) {
                $directory = public_path('uploads/initial/incident/fishbone_images/' . $fire_id);
            }

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $filePath = $directory . "/" . $fileName;
            file_put_contents($filePath, $imageDataDecoded);

            if ($incident_id) {
                $dataToSave['fishbone_image'] = 'public/uploads/initial/incident/fishbone_images/' . $incident_id . "/" . $fileName;
            } elseif ($accident_id) {
                $dataToSave['fishbone_image'] = 'public/uploads/initial/incident/fishbone_images/' . $accident_id . "/" . $fileName;
            } elseif ($fire_id) {
                $dataToSave['fishbone_image'] = 'public/uploads/initial/incident/fishbone_images/' . $fire_id . "/" . $fileName;
            }
        }

        $this->create($dataToSave);
    }
}
