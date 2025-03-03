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
// dd($request);
        // Get Fishbone data from the request
        $fishboneData = $request->input('fishbone', []);
        $imageData = $request->input('fishbone_image'); // Get base64 image
// dd($imageData);
        // Prepare data to save in JSON format
        $dataToSave = [
            'incident_id' => $incident_id,
            'accident_id' => $accident_id,
            'fire_id' => $fire_id,
            'investigation_id' => $investigation_id,
            'created_by' => Auth::id(),
            'created_at' => now(),
            'fishbone' => json_encode($fishboneData) // Convert fishbone data to JSON
        ];

        // Save Fishbone Image
        if ($imageData) {
            $image = str_replace('data:image/png;base64,', '', $imageData);
            $image = str_replace(' ', '+', $image);
            $imageDataDecoded = base64_decode($image);

            // Generate a unique filename
            $fileName = 'fishbone_' . time() . '.png';
            $directory = public_path('uploads/initial/incident/fishbone_images/' . $incident_id);

            // Ensure the directory exists
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Save image in file system
            $filePath = $directory . "/" . $fileName;
            file_put_contents($filePath, $imageDataDecoded);

            // Store the image path in the database (without 'public/' for accessibility)
            $dataToSave['fishbone_image'] = 'public/uploads/initial/incident/fishbone_images/' . $incident_id . "/" . $fileName;
        }
// dd($dataToSave);

        // Save to database (assuming a Fishbone model)
        $this->create($dataToSave);
    }
}
