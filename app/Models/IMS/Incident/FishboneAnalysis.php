<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FishboneAnalysis extends Model
{
    use  HasFactory;


    protected $table = 'ims_incident_fishboneanalysis';
    protected $primaryKey = 'id';

    protected $fillable = [
        'incident_id',
        'investigation_id',
        'fishbone',
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

    public function storeFishbone($incident_id, $investigation_id)
    {
        $request = request();

        // Get the fishbone data from the request
        $fishboneData = $request->input('fishbone', []);

        // Prepare the data to be saved in JSON format
        $dataToSave = [
            'incident_id' => $incident_id,
            'investigation_id' => $investigation_id,
            'created_by' => Auth::id(),
            'created_at' => now(),
            'fishbone' => json_encode($fishboneData) // Convert fishbone data to JSON
        ];

        // Save the data to the database (assuming a model named Fishbone)
        $this->create($dataToSave);
    }
}
