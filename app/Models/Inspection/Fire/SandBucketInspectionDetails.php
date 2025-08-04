<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class SandBucketInspectionDetails extends Model
{
    protected $table = 'inspection_fire_sand_bucket_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'inspection_id',
        'location',
        'fire_bucket_stand_no',
        'fire_bucket_no',
        'condition',
        'fire_bucket_condition',
        'paint_condition',
        'sand_quantity',
        'approach',
        'remarks',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'status',
        'trash',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];

    public function selectOne($id)
    {
        return $this->where('inspection_id', $id)->where('status', 1)->where('trash', 'NO')->first();
    }

    public function store($id)
    {
        $request = request();

        $location = $request->exact_location;
        $fire_sand_bucket_stand_no = $request->fire_sand_bucket_stand_no;
        $fire_sand_bucket_no = $request->fire_sand_bucket_no;
        $condition = $request->condition;
        $fire_bucket_condition = $request->fire_bucket_condition;
        $paint_condition = $request->paint_condition;
        $sand_quality = $request->qualtiy_quantity_sand;
        $approach = $request->approach;
        $remarks = $request->remarks;



        foreach ($location as $index => $location) {
            $data = array(
                'inspection_id' => $id,
                'location' => ($location),
                'fire_bucket_stand_no' => ($fire_sand_bucket_stand_no[$index]),
                'fire_bucket_no' => ($fire_sand_bucket_no[$index]),
                'condition' => decryptId($condition[$index]),
                'fire_bucket_condition' => decryptId($fire_bucket_condition[$index]),
                'paint_condition' => decryptId($paint_condition[$index]),
                'sand_quantity' => decryptId($sand_quality[$index]),
                'approach' => $approach[$index],
                'remarks' => $remarks[$index],
                'created_by' => Auth::id(),
            );

            $this->create($data);
        }
    }

    public function store_api($id)
    {
        $request = request();

        $location = $request->location;
        $fire_sand_bucket_stand_no = $request->fire_sand_bucket_stand_no;
        $fire_sand_bucket_no = $request->fire_sand_bucket_no;
        $condition = $request->condition;
        $fire_bucket_condition = $request->fire_bucket_condition;
        $paint_condition = $request->paint_condition;
        $sand_quality = $request->qualtiy_quantity_sand;
        $approach = $request->approach;
        $remarks = $request->remarks;


        foreach ($location as $index => $location) {
            $data = array(
                'inspection_id' => $id,
                'location' => ($location),
                'fire_bucket_stand_no' => ($fire_sand_bucket_stand_no[$index]),
                'fire_bucket_no' => ($fire_sand_bucket_no[$index]),
                'condition' => ($condition[$index]),
                'fire_bucket_condition' => ($fire_bucket_condition[$index]),
                'paint_condition' => ($paint_condition[$index]),
                'sand_quantity' => ($sand_quality[$index]),
                'approach' => $approach[$index],
                'remarks' => $remarks[$index],
                'created_by' => Auth::id(),
            );
            $this->create($data);
        }
    }

    public function GetDetails($id)
    {
        return $this->where('inspection_id', $id)->where('status', 1)->where('trash', 'NO')->get();
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_sand_bucket_details'));
    }
}
