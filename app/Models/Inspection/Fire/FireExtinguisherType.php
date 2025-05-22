<?php

namespace App\Models\Inspection\Fire;

use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;

class FireExtinguisherType extends Model
{
    protected $table = 'inspection_fire_fire_extinguisher_type';

    protected $fillable = [
        'id',
        'name',
        'status',
        'trash',
    ];

    public function getTypes()
    {
        return $this->where('status',1)->where('trash','NO')->get();
    }

    public function GetApi()
    {
        $data = $this->where('status',1)->where('trash','NO')->get();

        $refined_data = [];

        if(count($data) > 0)
        {
            foreach($data as $index=> $values)
            {
                $refined_data[$index] = [
                    'id' => $values->id,
                    'fire_extinguisher_name' => $values->name,
                ];
            }

            return $refined_data;
        }

        return false;
        
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_fire_fire_extinguisher_type'));
    }
}
