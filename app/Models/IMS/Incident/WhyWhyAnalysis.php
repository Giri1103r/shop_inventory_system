<?php

namespace App\Models\IMS\Incident;



use Carbon\Carbon;
use App\Scopes\TrashScope;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhyWhyAnalysis extends Model
{
    use  HasFactory;


    protected $table = 'ims_incident_whywhyanalysis';
    protected $primaryKey = 'id';

    protected $fillable = [
        'incident_id',
        'investigation_id',
        'why_1',
        'why_2',
        'why_3',
        'why_4',
        'why_5',
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

    public function store($incident_id, $investigation_id)
    {
        $request = request();
    
        if ($request->filled('whywhyanalysis') && is_array($request->whywhyanalysis)) {
            foreach ($request->whywhyanalysis as $analysis) {
                // Trim and filter out empty values
                $filteredAnalysis = array_filter(array_map('trim', [
                    'why_1' => $analysis['whywhyanalysis_first'] ?? null,
                    'why_2' => $analysis['whywhyanalysis_second'] ?? null,
                    'why_3' => $analysis['whywhyanalysis_third'] ?? null,
                    'why_4' => $analysis['whywhyanalysis_forth'] ?? null,
                    'why_5' => $analysis['whywhyanalysis_fifth'] ?? null,
                ]));
    
                // Only insert if at least one field has a value
                if (!empty($filteredAnalysis)) {
                    $filteredAnalysis['incident_id'] = $incident_id;
                    $filteredAnalysis['investigation_id'] = $investigation_id;
                    $filteredAnalysis['created_at'] = now();
                    $filteredAnalysis['updated_at'] = now();
                    $filteredAnalysis['created_by'] = Auth::id();
    // dd($filteredAnalysis);
                    return $this->create($filteredAnalysis);
                    if (!$inserted) {
                        return false; // If any insert fails, return false
                    }
                }
            }
    
            return true; // Return true if at least one row was inserted
        }
    
        return false; // Return false if no valid data was provided
    }
    
}
