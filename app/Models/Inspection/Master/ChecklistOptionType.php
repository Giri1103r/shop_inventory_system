<?php

namespace App\Models\Inspection\Master;

use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;

class ChecklistOptionType extends Model
{
    protected $table = 'inspection_master_checklist_option';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'type',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function Getall(){
        return $this->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('inspection_master_checklist_option'));
    }

}

