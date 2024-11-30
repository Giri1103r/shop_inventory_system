<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Scopes\TrashScope;

class Notification extends Model
{
    use  HasFactory;


    protected $table = 'template_notification';
    protected $primaryKey = 'id';

    protected $fillable = [
        'notification_type',
        'module_type',
        'notification_message',
        'mobile_notification',
        'web_link',
        'assigned_user',
        'viewed_user',
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


    public function list()
    {
        $request = request();
        $search = '';

        $query = $this->select('*')->whereRaw("FIND_IN_SET(?, assigned_user) > 0", [Auth::id()]);

        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            // $query->where(function ($query) use ($search) {
            //     $query->orWhere('company_id', 'LIKE', '%' . $search . '%')
            //         ->orWhere('company_name', 'LIKE', '%' . $search . '%')
            //         ->orWhere('company_shortname', 'LIKE', '%' . $search . '%');
            // });
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


    public static function booted()
    {
        static::addGlobalScope(new TrashScope('template_notification'));
    }
}
