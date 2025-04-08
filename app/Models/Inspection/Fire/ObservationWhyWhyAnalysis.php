<?php

namespace App\Models\Inspection\Fire;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ObservationWhyWhyAnalysis extends Model
{
    use  HasFactory;


    protected $table = 'inspection_fire_observation_whywhy';
    protected $primaryKey = 'id';

    protected $fillable = [
        'inspection_id',
        'observation_id',
        'why_1',
        'why_2',
        'why_3',
        'why_4',
        'why_5',
        'main_root_cause',
        'corrective_action',
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

    public function store($id, $observation_id)
    {
        $request = request();
        $this->where('observation_id', decryptId($request->observation_id))->update(['trash' => 'YES'],['status' => '0']);
        $data = array(
            'inspection_id' => decryptId($request->inspection_id),
            'observation_id' => decryptId($request->observation_id),
            'why_1' => $request->why_1,
            'why_2' => $request->why_2,
            'why_3' => $request->why_3,
            'why_4' => $request->why_4,
            'why_5' => $request->why_5,
            'main_root_cause' => $request->main_root_cause,
            'corrective_action' => $request->corrective_action,
            'created_by' => Auth::id(),
        );

        return $this->create($data);
    }
}
