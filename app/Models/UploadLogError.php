<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Scopes\TrashScope;

class UploadLogError extends Model
{
    use HasFactory;

    protected $table = 'template_admin_upload_log_error';
    protected $fillable = [
        'id',
        'upload_id',
        'line_no',
        'error',
        'status',
        'trash',
        'created_at',
        'updated_at',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('template_admin_upload_log_error'));
    }

    public function list()
    {
        $request = request();
        $search = '';

        $logid = decryptId($request->logid);
        $query = $this->select('template_admin_upload_log_error.*')->where('upload_id', $logid);

        if ($request->search['value'] != null || $request->search['value'] != '') {

            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query->orWhere('line_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('error', 'LIKE', '%' . $search . '%');
            });
        }

        $data_count = $query;
        $total_records = $data_count->count();

        $query->orderBy('id', 'DESC');


        if ($request->length != -1) {
            $query->offset($request->start)->limit($request->length);
        }

        $data = $query->get();

        $datas = [
            'data' => $data,
            'total_records' => $total_records
        ];

        return $datas;
    }

    public function exportdata()
    {
        $request = request();
        $search = '';

        $logid = decryptId($request->logid);

        $query = $this->select('template_admin_upload_log_error.*')->where('upload_id', $logid);

        if ($request->search != null || $request->search != '') {

            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query->orWhere('line_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('error', 'LIKE', '%' . $search . '%');
            });
        }

        $query->orderBy('id', 'DESC');
        $data = $query->get();
        return $data;

    }
}
