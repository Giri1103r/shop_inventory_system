<?php

namespace App\Models\Master;

use App\Scopes\TrashScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PpeType extends Model
{
    protected $table = 'masters_ppetype';
    protected $primaryKey = 'id';

    protected $fillable = [

        'ppe_type',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    public function getPpetypedata()
    {
        return PpeType::where('trash', 'NO')->where('status', '=', 1)->get();
    }
   
}
