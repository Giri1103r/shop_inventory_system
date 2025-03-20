<?php

namespace App\Models\Inspection\Safety;

use Illuminate\Database\Eloquent\Model;

class SignatureUpload extends Model
{
    protected $table = 'inspection_safety_signatureupload';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'type',
        'inspection_id',
        'file_path',
        'file_name',
        'file_orgname',
        'file_extension',
        'status',
        'trash',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'status' => 1,
        'trash' => 'NO'
    ];
}
