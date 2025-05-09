<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use Validator;
use Exception;

use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationLog;

class NotificationController extends BaseController
{
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */


    public function notification(Request $request): JsonResponse
    {
        try {

            if (Auth::user()) {


                $notification_list_array = Notification::select('*')
                    ->whereRaw("FIND_IN_SET(?, assigned_user) > 0", [Auth::id()]);

                $notification_list_array = $notification_list_array->orderBy('id','DESC')->paginate($request->input('per_page', 10));
                $notification_list = $notification_list_array->toArray();

                $userReadCount = NotificationLog::where('user_id', Auth::id())->count();
                $data_array = [];
                foreach ($notification_list_array as $listdata) {
                    $data = [];
                    $message =   json_decode($listdata->mobile_notification);
                    $viewed_user =   string_to_array($listdata->viewed_user);
                    $viewed_status = 0;
                    if (in_array(Auth::id(), $viewed_user)) {
                        $viewed_status = 1;
                    } else {
                        $viewed_status = 0;
                    }


                    $data['id'] = $listdata->id;
                    $data['title'] =  $message->title;
                    $data['message'] = $message->message;
                    $data['icon'] = url($message->icon);
                    $data['time'] = timeago($listdata->created_at);
                    $data['created_at'] = Displaydatetimeformat($listdata->created_at);
                    $data['read_status'] = $viewed_status;
                    $data['module_type'] = isset($message->module)? $message->module : null ;
                    $data['module_sub_type'] = isset($listdata->module_sub_type)? $listdata->module_sub_type : null ;
                    $data['module_id'] = isset($message->id)? $message->id : null ;

                    $data_array[] = $data;
                }

                $notification_details = [
                    'per_page' => $notification_list['per_page'],
                    'current_page' => $notification_list['current_page'],
                    'from' => $notification_list['from'],
                    'to' => $notification_list['to'],
                    'total' => $notification_list['total'],
                    'total_read' => $userReadCount,
                    'total_unread' => $notification_list['total'] - $userReadCount,
                    'total_page' => $notification_list['last_page'],
                    'list' => $data_array,
                ];

                $success = [
                    'notification_details' => $notification_details
                ];

                return $this->sendResponse($success, 'User Notification Details');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $ex) {

            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised','error' =>  $ex]);
        }
    }

    public function notificationUpdate(Request $request): JsonResponse
    {

        $notificationId = $request->notification_id;
        $user_id = Auth::id();

        $notificationCheck = Notification::select('*')
            ->whereRaw("FIND_IN_SET(?, assigned_user) > 0", [$user_id])
            ->where('id', $notificationId)
            ->count();

        $notify_array = array(
            'notification_id' => $notificationId,
            'user_id' => $user_id,
        );

        $viewdCount = NotificationLog::where($notify_array)->count();
        $success = [];

        if($viewdCount > 0){

            return $this->sendResponse($success, 'User Notification already viewed');
        }

        if ($notificationCheck == 1) {

            $notification = Notification::select('*')
                ->whereRaw("FIND_IN_SET(?, assigned_user) > 0", [$user_id])
                ->where('id', $notificationId)
                ->first();

            $notification_read_user = [];
            if ($notification->viewed_user == '' || $notification->viewed_user == null) {
                $notification_read_user = [];
            } else {
                $notification_read_user = string_to_array($notification->viewed_user);
            }

            $notification_read_user[] = $user_id;

            $notification->viewed_user = array_to_string($notification_read_user);
            $notification->update();


            NotificationLog::create($notify_array);



            return $this->sendResponse($success, 'User Notification details updated');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }
}
