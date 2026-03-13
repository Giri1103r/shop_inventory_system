<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


use App\Scopes\TrashScope;

class UserPermission extends Model
{
    use  HasFactory;


    protected $table = 'master_role_permission';
    protected $primaryKey = 'id';

    protected $fillable = [
        'role_id',
        'menu_id',
        'add',
        'edit',
        'delete',
        'view',
        'export',
        'role_permissions',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'

    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO',
    ];

    // protected $casts = [
    //     'role_permissions' => 'array',
    // ];



    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('master_role_permission'));
    }
}
