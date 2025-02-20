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
        if (CheckUserRole(ROLE_SUPERADMIN)|| CheckUserRole(ROLE_ADMIN)) {
            $query->where('template_notification.trash', 'NO');
        } elseif (CheckUserRole(ROLE_TRAINER)) {
            $trainer = DB::table('users')
                ->select('id')
                ->where('employee_id', Auth::user()->employee_id)
                ->first();

            if ($trainer) {
                $query->where('template_notification.assigned_user', $trainer->id)
                    ->where('template_notification.trash', 'NO');
            }
        } elseif (Auth::user()->role == ROLE_EHS_HEAD) {
            $ehs = DB::table('users')
                ->select('id')
                ->where('employee_id', Auth::user()->employee_id)
                ->first();

            if ($ehs) {
                $query->where('template_notification.assigned_user', $ehs->id)
                    ->where('template_notification.trash', 'NO');
            }
        } elseif (Auth::user()->role == ROLE_USER) {

            $nominationUsers = DB::table('users')
                ->select('id')
                ->where('employee_id', Auth::user()->employee_id)
                ->first();

            if ($nominationUsers) {
                $assignedUserIdUsers = $nominationUsers ? $nominationUsers->id : null;

                $query->where(function ($query) use ($assignedUserIdUsers) {
                    if ($assignedUserIdUsers) {
                        $query->where(function ($query) use ($assignedUserIdUsers) {
                            $query->whereRaw("FIND_IN_SET(?, assigned_user)", [$assignedUserIdUsers])
                                ->where('notification_type', 2)
                                ->where('template_notification.trash', 'NO');
                        });
                    }

                    if ($assignedUserIdUsers) {
                        $query->orWhere(function ($query) use ($assignedUserIdUsers) {
                            $query->whereRaw("FIND_IN_SET(?, assigned_user)", [$assignedUserIdUsers])
                                ->where('notification_type', 1)
                                ->where('template_notification.trash', 'NO');
                        })
                            ->orWhere(function ($query) use ($assignedUserIdUsers) {
                                $query->whereRaw("FIND_IN_SET(?, assigned_user)", [$assignedUserIdUsers])
                                    ->where('notification_type', 3)
                                    ->where('template_notification.trash', 'NO');
                            });
                    }
                });
            }
        } elseif (Auth::user()->role == ROLE_HOD) {
            $nominations = DB::table('users')
                ->select('id')
                ->where('department_id', Auth::user()->department_id)
                ->WhereRaw("FIND_IN_SET(?, role)", [4])
                ->get();

            if ($nominations->isNotEmpty()) {
                $assignedUserIds = $nominations->pluck('id')->toArray();
                $query->where(function ($query) use ($assignedUserIds) {
                    foreach ($assignedUserIds as $assignedUserId) {
                        $query->orWhereRaw("FIND_IN_SET(?, assigned_user)", [$assignedUserId]);
                    }
                })->where('template_notification.trash', 'NO')
                    ->where('notification_type', 1);
            }
        } elseif (Auth::user()->role == ROLE_EHS_HEAD || Auth::user()->role == ROLE_EHS_OFFICER) {
            $nomination = DB::table('users')
                ->select('id')
                ->where('employee_id', Auth::user()->employee_id)
                ->first();

            if ($nomination) {
                $assignedUserId = $nomination->id;

                $query->where(function ($query) use ($assignedUserId) {
                    $query->whereRaw("FIND_IN_SET(?, assigned_user)", [$assignedUserId])
                        ->whereIn('notification_type', [1, 3])
                        ->where('template_notification.trash', 'NO');
                });
            }
        } elseif (Auth::user()->role == ROLE_PLANT_HEAD) {
            $nomination = DB::table('users')
                ->select('id')
                ->where('employee_id', Auth::user()->employee_id)
                ->first();

            if ($nomination) {
                $assignedUserId = $nomination->id;

                $query->whereRaw("FIND_IN_SET(?, assigned_user)", [$assignedUserId])
                    ->where('notification_type', 3)
                    ->where('template_notification.trash', 'NO');
            }
        }elseif (Auth::user()->role == ROLE_STORE_MANAGER) {
            $nomination = DB::table('users')
                ->select('id')
                ->where('employee_id', Auth::user()->employee_id)
                ->first();

            if ($nomination) {
                $assignedUserId = $nomination->id;

                $query->whereRaw("FIND_IN_SET(?, assigned_user)", [$assignedUserId])
                    ->where('notification_type', 1)
                    ->where('template_notification.trash', 'NO');
            }
        }

        /**
         * Role Based list view condition end
         */

        if (!empty($request->search['value'])) {
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
