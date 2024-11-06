<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Scopes\TrashScope;

class LeftMenu extends Model
{
    use  HasFactory;


    protected $table = 'template_left_menu';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'link',
        'icon',
        'parent_id',
        'permission',
        'is_parent',
        'is_module',
        'sort_order',
        'status',
        'trash',
        'created_at',
        'updated_at',

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    public function list()
    {

        $menuList = $this->select('id', 'name', 'namekey', 'link', 'icon', 'parent_id','permission', 'is_parent', 'is_module', 'sort_order')
            ->where('status', 1)
            ->where('trash', 'NO')
            ->orderBy('id', 'asc')
            ->orderBy('parent_id', 'asc')
            ->orderBy('sort_order', 'asc')
            ->get();

        return $menuList;
    }


    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('template_left_menu'));
    }
}
