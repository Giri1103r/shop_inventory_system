<?php

namespace App\Models\Inspection;

use Illuminate\Database\Eloquent\Model;

class RRAASignatureUpload extends Model
{
    protected $table = 'inspection_rraa_signatureupload';

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
