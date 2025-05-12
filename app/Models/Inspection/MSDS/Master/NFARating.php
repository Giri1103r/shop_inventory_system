<?php

namespace App\Models\Inspection\MSDS\Master;

use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;

class NFARating extends Model
{
    protected $table = 'msds_master_nfa_rating';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'nfa_rating',
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

    public function getNFArating()
    {
        return $this->get();
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('msds_master_nfa_rating'));
    }
}
