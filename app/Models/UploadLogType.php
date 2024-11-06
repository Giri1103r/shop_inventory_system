<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Scopes\TrashScope;

class UploadLogType extends Model
{
    use HasFactory;


    protected $table = 'template_admin_upload_log_type';
    protected $fillable = [
        'id',
        'type_name',
        'key_name',
        'sample_file',
        'file_name',
        'created_by',
        'updated_by',
        'status',
        'trash',
        'created_at',
        'updated_at',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('template_admin_upload_log_type'));
    }

}
