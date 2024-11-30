<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Scopes\TrashScope;

class UploadLog extends Model
{
    use HasFactory;


    protected $table = 'template_admin_upload_log';
    protected $fillable = [
        'id',
        'upload_type',
        'upload_status',
        'file_name',
        'file_orgname',
        'file_path',
        'file_size',
        'file_extension',
        'created_by',
        'status',
        'trash',
        'created_at',
        'updated_at',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected static function booted()
    {
        static::addGlobalScope(new TrashScope('template_admin_upload_log'));
    }

    public function list()
    {
        $request = request();
        $search = '';

        $query = $this->select('template_admin_upload_log.*', 'template_admin_upload_log_type.type_name','users.name')
            ->leftJoin('template_admin_upload_log_type', 'template_admin_upload_log.upload_type', '=', 'template_admin_upload_log_type.id')
            ->leftJoin('users', 'template_admin_upload_log.created_by', '=', 'users.id');

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($query) use ($search) {
                $query->orWhere('template_admin_upload_log_type.type_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('users.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('file_orgname', 'LIKE', '%' . $search . '%');
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


    protected function updates($id, $update_array)
    {
        return $this->where('id', $id)->update($update_array);
    }
}
