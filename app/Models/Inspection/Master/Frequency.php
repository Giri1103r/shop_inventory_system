<?php

namespace App\Models\Inspection\Master;

use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;

class Frequency extends Model
{
    protected $table = 'inspection_frequency_option';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'frequency_name',
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
    public function getFrequency(){
        return $this->where('status',1)->get();
    }
    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_frequency_option'));

    }


}
