<?php

namespace App\Models\Inspection\MSDS\Master;

use App\Scopes\TrashScope;
use Illuminate\Database\Eloquent\Model;

class NFARatingValue extends Model
{
    protected $table = 'msds_master_nfa_rating_value';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'rating_value',
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

    public function getNFARatingValue()
    {
        $data = $this->get();
        return $data;
    }

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('msds_master_nfa_rating_value'));
    }
}
