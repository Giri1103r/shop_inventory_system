<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Models\Master\Location;
use App\Scopes\TrashScope;

class Language extends Model
{
    use  HasFactory;


    protected $table = 'template_language';
    protected $primaryKey = 'id';

    protected $fillable = [
        'short_name',
        'long_name',
        'direction',
        'english',
        'native',
        'sort_order',
        'created_by',
        'updated_by',
        'status',
        'trash',
        'created_at',
        'updated_at'
    ];


    public function list()
    {

        $query = $this->select('template_language.*')
            ->orderBy('sort_order', 'ASC');

        $data = $query->get();

        return $data;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('template_language'));
    }
}
