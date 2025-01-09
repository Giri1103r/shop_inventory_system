<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use DB;
use Str;
use Illuminate\Support\Facades\Auth;
use Session;
use Exception;
use DataTables;

use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationLog;


class NotificationController extends Controller
{

    private $notification;
    private $notificationlog;

    public function __construct()
    {
        //$this->middleware('auth');
        $this->notification = new Notification();
        $this->notificationlog = new NotificationLog();
    }

    public function notificationList(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                try {
                    $data = $this->notification->list();

                    $datatables = Datatables::of($data['data'])
                        ->addIndexColumn()

                        ->addColumn('action', function ($row) {
                            $btn = '';
                            if (!empty($row->web_link)) {
                                $btn = '<a href="' . admin_url('notification/view/' . encryptId($row->id)) . '" class="" title="View"><i class="fa-solid fa-eye"></i></a> ';
                            }
                            return $btn;
                        })
                        ->editColumn('notification_type', function ($row) {
                            if ($row->notification_type == 1) {
                                $btn = "PPE";
                            } elseif ($row->notification_type == 2) {
                                $btn = "TRAINING";
                            } elseif ($row->notification_type == 3) {
                                $btn = "PTW";
                            }
                            return $btn;
                        })
                        ->editColumn('mobile_notification', function ($row) {

                            $mobileNotification = json_decode($row->mobile_notification, true);

                            return $mobileNotification['message'] ?? '';
                        })
                        ->editColumn('datetime', function ($row) {
                            return timeago($row->created_at);
                        })
                        ->removeColumn([
                            'assigned_user',
                            'created_at',
                            'created_by',
                            'id',
                            'module_type',
                            'status',
                            'trash',
                            'updated_at',
                            'updated_by',
                            'viewed_user',
                            'web_link',
                        ])
                        ->rawColumns(['action', 'mobile_notification'])
                        ->setFilteredRecords($data['total_records'])
                        ->setTotalRecords($data['total_records'])
                        ->skipPaging()
                        ->make(true);
                    return $datatables;
                } catch (Exception $ex) {
                    report($ex);
                    return response()->json(['status' => 'error', 'msg' => 'Please try after some time'], 406);
                }
            }
        }

        $data = array();
        return view('admin.notification.list', $data);
    }


    public function notificationAllRead(Request $request)
    {

        $userId = Auth::id();

        $notificationViewed =   $this->notificationlog->where('user_id', $userId)->pluck('notification_id');

        $unreadmsg = $this->notification;
        if (count($notificationViewed) > 0) {
            $unreadmsg =  $unreadmsg->whereNotIn('id', $notificationViewed);
        }

        $unreadmsg = $unreadmsg->whereRaw("FIND_IN_SET(?, assigned_user) > 0", [$userId])->get();

        if (count($unreadmsg) > 0) {
            foreach ($unreadmsg as $unread) {

                $notify_array = array(
                    'notification_id' => $unread->id,
                    'user_id' => $userId,
                );

                $notification_read_user = [];
                if ($unread->viewed_user == '' || $unread->viewed_user == null) {
                    $notification_read_user = [];
                } else {
                    $notification_read_user = string_to_array($unread->viewed_user);
                }

                $notification_read_user[] = $userId;

                $unread->viewed_user = array_to_string($notification_read_user);
                $unread->update();


                NotificationLog::create($notify_array);
            }
        }

        return redirect()->back();
    }

    public function notificationView(Request $request)
    {

        $id = decryptId($request->id);

        $userId = Auth::id();

        $notify_array = array(
            'notification_id' => $id,
            'user_id' => $userId,
        );

        $notification = $this->notification->find($id);

        $viewedList = $notification->viewed_user;

        $viewedListArray = string_to_array($viewedList);

        if (!in_array($userId, $viewedListArray)) {

            $notify_array = array(
                'notification_id' => $notification->id,
                'user_id' => $userId,
            );

            $notification_read_user = [];
            if ($notification->viewed_user == '' || $notification->viewed_user == null) {
                $notification_read_user = [];
            } else {
                $notification_read_user = string_to_array($notification->viewed_user);
            }

            $notification_read_user[] = $userId;

            $notification->viewed_user = array_to_string($notification_read_user);
            $notification->update();


            NotificationLog::create($notify_array);
        }

        $redirectUrl = $notification->web_link;

        return redirect($redirectUrl);
    }
}
