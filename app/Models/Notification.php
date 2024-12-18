<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

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

        $query = $this->select('*');

        /**
         * Role Based list view condition start
         */

        if (CheckUserRole(ROLE_SUPERADMIN)) {
            $query->where('template_notification.trash', 'NO');
        } elseif (CheckUserRole(ROLE_TRAINER)) {
            $trainer = DB::table('masters_employee')
                ->select('id')
                ->where('emp_id', Auth::user()->employee_id)
                ->first();

            if ($trainer) {
                $query->where('template_notification.assigned_user', $trainer->id)
                    ->where('template_notification.trash', 'NO');
            }
        } elseif (Auth::user()->role == ROLE_USER) {
            $notification_type = $query->where('notification_type', 2)->first();
            if ($notification_type && $notification_type->notification_type == 2) {
                $nomination = DB::table('masters_employee')
                    ->select('id')
                    ->where('emp_id', Auth::user()->employee_id)
                    ->first();
                if ($nomination) {
                    $assignedUserId = $nomination->id;

                    $query->whereRaw("FIND_IN_SET(?, assigned_user)", [$assignedUserId])
                        ->where('template_notification.trash', 'NO');
                }
            }
        }
        /**
         * Role Based list view condition end
         */
        if ($request->search['value'] != null || $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->where(function ($q) use ($search) {
                $q->orWhere('notification_message', 'LIKE', '%' . $search . '%')
                    ->orWhere('notification_type', 'LIKE', '%' . $search . '%')
                    ->orWhere('web_link', 'LIKE', '%' . $search . '%')
                    ->orWhere('created_by', 'LIKE', '%' . $search . '%');
            });
        }

        $data_count = $query->count();
        $total_records = $data_count;

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
