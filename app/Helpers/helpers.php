<?php


use Carbon\Carbon;
use App\Models\User;
use App\Models\FcmToken;
use App\Models\LeftMenu;
use Illuminate\Support\Str;
use App\Models\Master\Fleet;
use App\Models\Notification;
use App\Models\UploadLogType;
use App\Models\UserPermission;
use App\Models\Master\Employee;
use App\Models\Master\UserRole;
use App\Models\Master\Department;
use App\Models\Master\PpeRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Master\ForkLiftType;
use App\Models\Master\PpeExemption;
use App\Models\Permit\SafetyPermit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Models\Master\TrainingSchedule;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Inspection\MSDSCheckList;
use App\Models\Inspection\RRAACheckList;
use App\Models\Inspection\Master\Frequency;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\WebPushConfig;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Master\ChecklistSubType;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;
use App\Models\OhcManagement\SafetyPettyLogbook\SafetyPettyChecklist;

if (!function_exists('get_encryptVal')) {

    function get_encryptVal($id)
    {

        return strtr(base64_encode($id), '+/=', '-_,');
    }
}

if (!function_exists('get_decryptVal')) {

    function get_decryptVal($id)
    {
        return base64_decode(strtr($id, '-_,', '+/='));
    }
}

if (!function_exists('DBdateformat')) {

    function DBdateformat($date)
    {
        if ($date == '' || $date == null) {
            return '';
        }
        return date('Y-m-d', strtotime($date));
    }
}

if (!function_exists('DBdatetimeformat')) {

    function DBdatetimeformat($date)
    {
        if ($date == '' || $date == null) {
            return '';
        }
        return date('Y-m-d H:i:s', strtotime($date));
    }
}

if (!function_exists('Displaydateformat')) {

    function Displaydateformat($date)
    {
        if ($date == '' || $date == null || $date == '1970-01-01') {
            return '';
        }

        return date('d-m-Y', strtotime($date));
    }
}

if (!function_exists('Displaytimeformat')) {

    function Displaytimeformat($date)
    {
        if ($date == '' || $date == null) {
            return '';
        }
        return date('H:i', strtotime($date));
    }
}

if (!function_exists('datastringreplace')) {

    function datastringreplace($date)
    {

        return str_replace("/", "-", $date);
    }
}

if (!function_exists('Displaydatetimeformat')) {

    function Displaydatetimeformat($date)
    {

        return date('d-m-Y H:i:s', strtotime($date));
    }
}

if (!function_exists('todaydate')) {

    function todaydate()
    {

        return date('d-m-Y');
    }
}

if (!function_exists('currenttime')) {

    function currenttime()
    {

        return date('H:i A');
    }
}

if (!function_exists('todayDbdate')) {

    function todayDbdate()
    {

        return date('Y-m-d');
    }
}

if (!function_exists('todaydatetime')) {

    function todaydatetime()
    {

        return date('d-m-Y H:i:s');
    }
}

if (!function_exists('todayDBdatetime')) {

    function todayDBdatetime()
    {

        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('monthyear')) {

    function monthyear()
    {

        return date('F,Y');
    }
}

if (!function_exists('currentyear')) {

    function currentyear()
    {

        return date('Y');
    }
}

if (!function_exists('print_array')) {

    function print_array($data, $exit = true)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        if ($exit)
            exit;
    }
}

if (!function_exists('parseData')) {

    function parseData($results = array(), $postval = '', $retunval = '')
    {
        $results = (is_array($results) && $results != FALSE) ? (object) $results : $results;
        return ($results != FALSE && isset($results->$postval) && ($results->$postval != '')) ? $results->$postval : $retunval;
    }
}

if (!function_exists('userDetails')) {

    function userDetails()
    {

        $request = request();
        $input['useragent'] = $request->server('HTTP_USER_AGENT');
        $input['ip'] = $request->ip();
        return ($input);
    }
}
if (!function_exists('trainingStatusCount')) {
    function trainingStatusCount($type = '', $params = [])
    {
        $training = new TrainingSchedule();
        return $training->statusCount($type, $params);
    }
}

if (!function_exists('permitStatusCount')) {
    function permitStatusCount($type = '', $params = [])
    {
        $Safetypermit = new SafetyPermit();
        return $Safetypermit->statusCount($type, $params);
    }
}

if (!function_exists('insertUserLog')) {

    function insertUserLog($event = '', $custom_msg = '')
    {
        $request = request();

        if (Auth::check()) {
            $input['user_id'] = Auth::id();
        }

        $input['params'] = json_encode($request->all());
        $input['user_agent'] = $request->server('HTTP_USER_AGENT');
        $input['user_ip'] = $request->ip();
        $input['request_type'] = $request->method();
        $input['page_url'] = $request->fullUrl();
        $input['user_event'] = $event;
        $input['custom_msg'] = $custom_msg;

        if ($request->ajax()) {
            $input['is_ajax'] = 'YES';
        }

        DB::table('template_user_page_visit')->insert($input);

        return true;
    }
}

// if (!function_exists('string_to_array')) {

//     function string_to_array($string, $separate = ',')
//     {
//         return array_map('trim', explode($separate, $string));
//     }
// }
if (!function_exists('string_to_array')) {
    function string_to_array($string, $separate = ',')
    {
        if (is_null($string) || $string === '') {
            return [];
        }

        return array_map('trim', explode($separate, $string));
    }
}

if (!function_exists('array_to_string')) {

    function array_to_string($array, $separate = ',')
    {

        return implode($separate, $array);
    }
}

if (!function_exists('merge_two_array')) {

    function merge_two_array($array1, $array2)
    {

        return $array = array_values(array_unique(array_merge($array1, $array2)));
    }
}

if (!function_exists('arrayEncrypt')) {

    function arrayEncrypt($arrayVal)
    {
        return array_map("encryptId", $arrayVal);
    }
}

if (!function_exists('arrayDecrypt')) {

    function arrayDecrypt($arrayVal)
    {
        if (count($arrayVal) > 0) {
            return array_map("decryptId", $arrayVal);
        } else {
            return [];
        }
    }
}

if (!function_exists('admin_url')) {

    function admin_url($value = "")
    {
        return config('constants.ADMIN_URL') . $value;
    }
}

if (!function_exists('priceRound')) {

    function priceRound($amount)
    {

        return round($amount);
        if (is_float($amount)) {
            return round($amount);
        } else {
            return $amount;
        }
    }
}

if (!function_exists('get_constant')) {

    function get_constant($value)
    {

        return config('constants.' . $value);
    }
}

if (!function_exists('getConstant')) {

    function getConstant($value)
    {

        $user = DB::table('template_constants')->select('value')->where('name', $value)->first();

        if ($user == null) {
            return '';
        } else {
            return $user->value;
        }
    }
}

if (!function_exists('encryptId')) {

    function encryptId($value)
    {

        $action = 'encrypt';
        $string = $value;
        $output = false;
        $encrypt_method = "AES-256-CBC";

        $secret_key = 'P(0p!e@e$k';
        $secret_iv = 'Peop!eDe$k';

        // hash
        $key = hash('sha256', $secret_key);

        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {

            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }
}

if (!function_exists('decryptId')) {

    function decryptId($encrypted)
    {

        $action = 'decrypt';
        $string = $encrypted;
        $output = false;
        $encrypt_method = "AES-256-CBC";

        $secret_key = 'P(0p!e@e$k';
        $secret_iv = 'Peop!eDe$k';

        // hash
        $key = hash('sha256', $secret_key);

        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {

            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }
}

if (!function_exists('getUsername')) {

    function getUsername($userid)
    {

        $user = DB::table('users')->select('name')->where('id', $userid)->where('trash', 'NO')->first();

        if ($user == null) {
            return '';
        } else {
            return $user->name;
        }
    }
}
if (!function_exists('getEmployeename')) {

    function getEmployeename($userid)
    {

        $user = DB::table('masters_employee')->select('emp_name')->where('id', $userid)->where('trash', 'NO')->first();

        if ($user == null) {
            return '';
        } else {
            return $user->emp_name;
        }
    }
}
if (!function_exists('getEmployeeType')) {

    function getEmployeeType($userid)
    {

        $employee_type_name = DB::table('ohc_master_employee_cum_patient_employee_type')->select('employee_type_name')->where('id', $userid)->where('trash', 'NO')->first();

        if ($employee_type_name == null) {
            return '';
        } else {
            return $employee_type_name->employee_type_name;
        }
    }
}
if (!function_exists('getUserdesignation')) {
    function getUserdesignation($userid)
    {
        $user = DB::table('users')
            ->select('users.name', 'master_designation.designation as designation_name')
            ->leftJoin('master_designation', 'users.designation', '=', 'master_designation.id')
            ->where('users.id', $userid)
            ->where('users.trash', 'NO')
            ->first();

        if ($user == null) {
            return '';
        } else {
            return $user->designation_name ?? '';
        }
    }
}

if (!function_exists('getUseremail')) {

    function getUseremail($userid)
    {

        $user = DB::table('users')->select('email')->where('id', $userid)->where('trash', 'NO')->first();

        if ($user == null) {
            return '';
        } else {
            return $user->email;
        }
    }
}

if (!function_exists('getYesNoStatus')) {

    function getYesNoStatus($value)
    {
        $status = "";
        if ($value == 0) {
            $status = "NO";
        } elseif ($value == 1) {
            $status = "YES";
        } else {
            $status = "YES";
        }

        return $status;
    }
}

if (!function_exists('getChecked')) {

    function getChecked($value)
    {
        $status = "";
        if ($value == 0) {
            $status = "";
        } elseif ($value == 1) {
            $status = "checked";
        } else {
            $status = "";
        }

        return $status;
    }
}

if (!function_exists('getCheckedVal')) {

    function getCheckedVal($value, $check)
    {
        $status = "";
        if ($value == $check) {
            $status = "checked";
        } else {
            $status = "";
        }

        return $status;
    }
}

if (!function_exists('getSelected')) {

    function getSelected($value, $check)
    {
        $status = "";
        if ($value != $check) {
            $status = "";
        } elseif ($value == $check) {
            $status = "selected";
        } else {
            $status = "";
        }

        return $status;
    }
}

if (!function_exists('getActive')) {

    function getActive($value)
    {
        $status = "";
        if ($value == 0) {
            $status = "";
        } elseif ($value == 1) {
            $status = "active";
        } else {
            $status = "";
        }

        return $status;
    }
}

if (!function_exists('getCustomValue')) {

    function getCustomValue($table, $column, $value)
    {

        $returnval = DB::table($table)->where('id', $value)->where('trash', 'NO')->first();

        if ($returnval != null)
            return $returnval->$column;
        else
            return '';
    }
}

if (!function_exists('getSingleArray')) {

    function getSingleArray($key, $array_val = [])
    {
        $return_array = array();
        foreach ($array_val as $array) {

            $return_array[] = $array->$key;
        }

        return $return_array;
    }
}

if (!function_exists('getSingleArray')) {
    function getMimetype($type)
    {

        switch ($type) {
            case "pdf":
                $mime = "application/pdf";
                break;
            case "csv":
                $mime = "text/csv";
                break;
            case "doc":
                $mime = "application/msword";
                break;
            case "docx":
                $mime = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
                break;
            case "jpeg":
                $mime = "image/jpeg";
                break;
            case "jpg":
                $mime = "image/jpeg";
                break;
            case "tif":
                $mime = "image/tiff";
                break;
            case "tiff":
                $mime = "image/tiff";
                break;
            case "txt":
                $mime = "text/plain";
                break;
            case "xls":
                $mime = "application/vnd.ms-excel";
                break;
            case "xlsx":
                $mime = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                break;
            case "zip":
                $mime = "application/zip";
                break;
            default:
                $mime = "";
                break;
        }

        return $mime;
    }
}

if (!function_exists('getMultipleValue')) {

    function getMultipleValue($table, $commaVal, $condCol, $dataCol)
    {
        $arrayCond = string_to_array($commaVal);

        $returnVal = DB::table($table)->whereIn($condCol, $arrayCond)->pluck($dataCol);

        $returnVal = array_to_string($returnVal->toArray(), ', ');


        return  $returnVal;
    }
}

if (!function_exists('selectIfInString')) {

    function selectIfInString($value, $commaVal)
    {
        $arrayVal = string_to_array($commaVal);

        if (in_array($value, $arrayVal)) {
            $returnVal = "Selected";
        } else {
            $returnVal = "";
        }

        return  $returnVal;
    }
}

if (!function_exists('selectIfInArray')) {

    function selectIfInArray($value, $arrayVal)
    {

        if (in_array($value, $arrayVal)) {
            $returnVal = "Selected";
        } else {
            $returnVal = "";
        }

        return  $returnVal;
    }
}

if (!function_exists('getDiscountPercent')) {

    function getDiscountPercent($total_amount, $discount_amount)
    {

        if (
            $discount_amount == 0 || $discount_amount == '' || $discount_amount == null ||
            $total_amount == 0 || $total_amount == '' || $total_amount == null
        ) {
            return 0;
        }


        $discount_percent = round(($discount_amount / $total_amount) * 100, 2);
        return  100 - $discount_percent;
    }
}

if (!function_exists('getProfileImage')) {

    function getProfileImage($image)
    {
        if ($image == null || $image == '') {
            return asset('public/assets/images/avatars/default_profile.png');
        } else {
            $file = asset('public/uploads/profile/' . $image);
            if (does_url_exists($file)) {
                return asset('public/uploads/profile/' . $image);
            }
            return asset('public/asset/admin/images/avatars/default_profile.png');
        }
    }
}

if (!function_exists('does_url_exists')) {
    function does_url_exists($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($code == 200) {
            $status = true;
        } else {
            $status = false;
        }
        curl_close($ch);
        return $status;
    }
}

if (!function_exists('getSequenceno')) {
    function getSequenceno($date, $id)
    {

        $d = strtotime($date);
        $seqno = date("ym", $d) . "0000" . $id;

        return $seqno;
    }
}

if (!function_exists('SanitizeInput')) {
    function SanitizeInput($string = '')
    {
        $returnString = preg_replace('/[^a-zA-Z0-9&-_! ]/s', '', $string);
        return $returnString;
    }
}

if (!function_exists('SanitizeInputArray')) {
    function SanitizeInputArray($input = [])
    {
        $returnArray = [];
        for ($i = 0; $i < count($input); $i++) {
            $returnArray[$i] = preg_replace('/[^a-zA-Z0-9&-_! ]/s', '', $input[$i]);
        }

        return $returnArray;
    }
}

if (!function_exists('getCustomColumn')) {

    function getCustomColumn($table, $columnName, $condCol, $dataCol)
    {
        $returnVal = DB::table($table)->where($condCol, $columnName)->first();

        return $returnVal->$dataCol;
    }
}

if (!function_exists('string_replace_custom')) {

    function string_replace_custom($replace_array, $replace_with, $string)
    {
        $returnVal = $string;
        foreach ($replace_array as $replace) {
            if (str_contains($string, $replace))
                $returnVal = str_replace($replace, $replace_with, $returnVal);
        }
        return $returnVal;
    }
}

if (!function_exists('getSelected')) {

    function getSelected($value, $check)
    {
        $status = "";
        if ($value != $check) {
            $status = "";
        } elseif ($value == $check) {
            $status = "selected";
        } else {
            $status = "";
        }

        return $status;
    }
}

if (!function_exists('getUser')) {

    function getUser($id)
    {
        return User::find($id);
    }
}

if (!function_exists('generateNumberArray')) {

    function generateNumberArray($number, $multiplier = '')
    {
        if ($multiplier  == '') {
            return range(1, $number);
        } else {
            return range(1, $number, $multiplier);
        }
    }
}

if (!function_exists('getUserRoleName')) {

    function getUserRoleName($userid)
    {

        $user =  User::find($userid);

        $userroleid = string_to_array($user->role);

        $roleName = array_to_string(UserRole::whereIn('id', $userroleid)->pluck('role_name')->toArray(), ", ");

        return $roleName;
    }
}

if (!function_exists('getUserRoleId')) {

    function getUserRoleId($userid)
    {

        $user =  User::find($userid);

        $userroleid = string_to_array($user->role);

        $roleids = UserRole::whereIn('id', $userroleid)->pluck('id')->toArray();

        return $roleids;
    }
}

if (!function_exists('isAdmin')) {

    function isAdmin()
    {
        if (in_array(ROLE_SUPERADMIN, getUserRoleId(Auth::id())) || in_array(ROLE_ADMIN, getUserRoleId(Auth::id()))) {
            return TRUE;
        }

        return FALSE;
    }
}

if (!function_exists('CheckUserRole')) {

    function CheckUserRole($roleId)
    {

        $user =  User::find(Auth::id());

        $userroleid = string_to_array($user->role);

        $roleids = UserRole::whereIn('id', $userroleid)->pluck('id')->toArray();

        if (in_array($roleId, $roleids)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
if (!function_exists('CheckUserPermission')) {

    function CheckUserPermission($permissionType)
    {
        $userRoleId = Auth::user()->role;

        $currentPath = Request::path();
        $menuId = LeftMenu::where('link', 'LIKE', "%{$currentPath}%")->select('id')->first();

        if (!$menuId) {
            return false;
        }
        $leftmenu_id =  $menuId->id;

        $userPermission = UserPermission::where('role_id', $userRoleId)
            ->where('menu_id', $leftmenu_id)
            ->value('role_permissions');
        if ($userPermission) {
            $permissionsArray = json_decode($userPermission, true);
            return isset($permissionsArray[$permissionType]) && $permissionsArray[$permissionType] == 1;
        }

        return false;
    }
}


if (!function_exists('getEndDate')) {

    function getEndDate($fromdate, $addValue, $type = 1, $endDate = "")
    {

        $fromdate = DateTime::createFromFormat('d-m-Y', $fromdate);

        $futureDateTarget = new DateTime($fromdate->format('Y-m-d'));

        if ($type === 1) {
            $addValue = $addValue - 1;
            $futureDateTarget->modify('+' . $addValue . ' days');
        } elseif ($type === 2) {
            $futureDateTarget->modify('+' . $addValue . ' months');
        } elseif ($type === 3) {
            $futureDateTarget->modify('+' . $addValue . ' years');
        } else {
            return null;
        }

        if ($endDate != '') {
            $endDate = DateTime::createFromFormat('d-m-Y', $endDate);
            if ($endDate && $futureDateTarget > $endDate) {
                $futureDateTarget = $endDate;
            }
        }

        $formattedDate = $futureDateTarget->format('d-m-Y');
        return $formattedDate;
    }
}

if (!function_exists('replaceNullWithEmpty')) {

    function replaceNullWithEmpty(&$item, $key)
    {
        if (is_array($item)) {
            array_walk_recursive($item, 'replaceNullWithEmpty');
        } else {
            $item = ($item === null) ? "" : $item;
        }
    }
}

if (!function_exists('profileImage')) {

    function profileImage($userid)
    {

        $user =  User::find($userid);

        if ($user->profile_image == null ||  $user->profile_image == '') {

            $profile_image = 'public/assets/images/default/user.jpeg';
        } else {

            $imagePath = 'public/uploads/profile/' . $user->profile_image;
            if (File::exists($imagePath)) {
                $profile_image = $imagePath;
            } else {
                $profile_image = 'public/assets/images/default/user.jpeg';
            }
        }

        return $profile_image;
    }
}

/*
 * Notification
 */

if (!function_exists('timeago')) {

    function timeago($datetime)
    {
        $created = new Carbon($datetime);
        $now = Carbon::now();
        $diff = $created->diffInSeconds($now);

        if ($diff < 10) {
            $diff = round($diff);
            return $diff . ' seconds ago';
        } elseif ($diff < 60) {
            $diff = round($diff);
            return $diff . ' seconds ago';
        } elseif ($diff < 3600) {
            $minutes = round($diff / 60);
            return $minutes . ' minutes ago';
        } elseif ($diff < 36000) {
            $hours = round($diff / 3600);
            return $hours . ' hours ago';
        } elseif ($created->isToday()) {
            return $created->format('g:i A'); // Today, display only time
        } elseif ($created->isYesterday()) {
            return 'Yesterday';
        } elseif ($diff < 2419200) {
            $days = round($created->diffInDays($now));
            return $days . ' days ago';
        } elseif ($diff < 7257600) {
            $months = round($created->diffInMonths($now));
            return $months . ' months ago';
        } elseif ($diff < 31536000) {
            $years = round($created->diffInYears($now));
            return $years . ' years ago';
        } else {
            $years = round($created->diffInYears($now));
            return $years . ' years ago';
        }

        return $created->format('F j, Y'); // Default format
    }
}

/*
 * Admin Base URL
 */

if (!function_exists('getHost')) {

    function getHost()
    {
        return env('APP_URL', "");
    }
}

function postData($results = array(), $postval = '', $retunval = '')
{
    $results = (is_array($results) && $results != FALSE) ? (object) $results : $results;
    return ($results != FALSE && isset($results->$postval) && ($results->$postval != '')) ? $results->$postval : $retunval;
}

/**
 * Notificaion Save
 */

if (!function_exists('notificationSave')) {

    function notificationSave($data = [])
    {

        Notification::create($data);
    }
}

/*
 * Mobile Push Notification
 */

if (!function_exists('mobilePushNotification')) {

    function mobilePushNotification($userId, $data)
    {
        /**
         * Get Android FCM Token
         */

        $androidToken = getFCMtoken($userId, $type = 'android')->toArray();
        androidNotification($androidToken, $data);

        /**
         * Get iOS FCM Token
         */

        $iosToken = getFCMtoken($userId, $type = 'ios')->toArray();
        iosNotification($iosToken, $data);
    }
}

if (!function_exists('getFCMtoken')) {

    function getFCMtoken($userId, $type = '')
    {

        $fcmToken =  FcmToken::select('token');
        $fcmToken = $fcmToken->where('user_id', $userId);

        if ($type != '') {
            $fcmToken = $fcmToken->where('device_type', $type);
        }

        $fcmToken = $fcmToken->pluck('token');

        return $fcmToken;
    }
}

if (!function_exists('androidNotification')) {

    function androidNotification($deviceTokens, $data)
    {

        if (count($deviceTokens) > 0) {
            $messaging = app('firebase.messaging');
            $message = CloudMessage::fromArray([

                'notification' => [
                    "title" => $data['title'],
                    "body" => $data['message'],

                ],
            ]);

            $data = $messaging->sendMulticast($message, $deviceTokens);
        }
    }
}

if (!function_exists('iosNotification')) {

    function iosNotification($deviceTokens, $data)
    {

        if (count($deviceTokens) > 0) {
            $messaging = app('firebase.messaging');
            $message = CloudMessage::fromArray([

                'notification' => [
                    "title" => $data['title'],
                    "body" => $data['message'],

                ],
            ]);

            $data = $messaging->sendMulticast($message, $deviceTokens);
        }
    }
}

if (!function_exists('removeSpace')) {

    function removeSpace($value)
    {
        $value = str_replace(' ', '', $value);
        $value = preg_replace('/\s+/', '', $value);

        return $value;
    }
}

if (!function_exists('get_financial_year_dates')) {
    function get_financial_year_dates($input_date = null)
    {
        if ($input_date === null) {
            $input_date = date('Y-m-d');
        }

        $dateComparison = date('m-d', strtotime($input_date));

        if ($dateComparison < '04-01') {
            $currentYear = date('Y', strtotime($input_date)) - 1;
        } else {
            $currentYear = date('Y', strtotime($input_date));
        }

        $financialYearStart = date('Y-04-01', strtotime($currentYear . '-04-01'));
        $financialYearEnd = date('Y-03-31', strtotime(($currentYear + 1) . '-03-31'));

        return [
            'start_date' => $financialYearStart,
            'end_date' => $financialYearEnd
        ];
    }
}

if (!function_exists('getautogen')) {
    function getautogen($number)
    {
        if ($number < 10000) {
            $number = "0" . $number;
        }
        if ($number < 1000) {
            $number = "0" . $number;
        }
        if ($number < 100) {
            $number = "0" . $number;
        }
        if ($number < 10) {
            $number = "0" . $number;
        }
        return $number;
    }
}

if (!function_exists('public_path')) {

    function public_path($value = "")
    {
        return config('constants.ADMIN_URL') . "public/" . $value;
    }
}

if (!function_exists('public_css')) {

    function public_css($value = "")
    {
        return config('constants.ADMIN_URL') . "public/assets/css/" . $value;
    }
}

if (!function_exists('public_js')) {

    function public_js($value = "")
    {
        return config('constants.ADMIN_URL') . "public/assets/js/" . $value;
    }
}

if (!function_exists('public_plugins')) {

    function public_plugins($value = "")
    {
        return config('constants.ADMIN_URL') . "public/assets/plugins/" . $value;
    }
}

if (!function_exists('public_image')) {

    function public_image($value = "")
    {
        return config('constants.ADMIN_URL') . "public/assets/images/" . $value;
    }
}

if (!function_exists('uploadFile')) {

    function uploadFile($file, $folder, $fileName, $disk = 'public')
    {

        if (!Storage::disk($disk)->exists($folder)) {
            Storage::disk($disk)->makeDirectory($folder);
        }

        Storage::disk($disk)->putFileAs($folder, $file, $fileName);

        return $folder . '/' . $fileName;
    }
}

if (!function_exists('private_storage')) {
    function private_storage($link)
    {

        return storage_path('app/public/' . $link);
    }
}

if (!function_exists('exportsamplefile')) {

    function exportsamplefile($type)
    {

        $result =  UploadLogType::where('key_name', $type)->first();

        return $result;
    }
}



if (!function_exists('getroleuserid')) {

    function getroleuserid($roleid)
    {
        return $userdetails =    User::whereRaw('FIND_IN_SET(' . $roleid . ', role)')->pluck('id');
    }
}

if (!function_exists('secondsToMinutesAndSeconds')) {
    /**
     * Convert seconds to minutes and seconds.
     *
     * @param int $seconds
     * @return string
     */
    function secondsToMinutesAndSeconds($seconds)
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }
}

if (!function_exists('getMonth')) {
    function getMonth($created_at)
    {
        $month_name = Carbon::parse($created_at)->format('F');
        return $month_name;
    }

    if (!function_exists('getPpeType')) {

        function getPpeType($userid)
        {

            $ppe_type = DB::table('masters_ppetype')->select('ppe_type')->where('id', $userid)->where('trash', 'NO')->first();

            if ($ppe_type == null) {
                return '';
            } else {
                return $ppe_type->ppe_type;
            }
        }
    }
    if (!function_exists('getPpename')) {

        function getPpename($userid)
        {

            $ppe_name = DB::table('ppe_master_ppetypemaster')->select('ppe_name')->where('id', $userid)->where('trash', 'NO')->first();

            if ($ppe_name == null) {
                return '';
            } else {
                return $ppe_name->ppe_name;
            }
        }
    }
    if (!function_exists('getDepartment')) {

        function getDepartment($userid)
        {

            $department_name = DB::table('masters_department')->select('department_name')->where('id', $userid)->where('status', 1)->where('trash', 'NO')->first();

            if ($department_name == null) {
                return '';
            } else {
                return $department_name->department_name;
            }
        }
    }
    if (!function_exists('getTopic')) {

        function getTopic($userid)
        {

            $topic_name = DB::table('training_masters_topic')->select('topic_name')->where('id', $userid)->where('status', 1)->where('trash', 'NO')->first();

            if ($topic_name == null) {
                return '';
            } else {
                return $topic_name->topic_name;
            }
        }
    }

    if (!function_exists('getEmployeeId')) {

        function getEmployeeId($userid)
        {

            $emp_id = DB::table('masters_employee')->select('emp_id')->where('id', $userid)->where('status', 1)->where('trash', 'NO')->first();

            if ($emp_id == null) {
                return '';
            } else {
                return $emp_id->emp_id;
            }
        }
    }
    if (!function_exists('getMSDSCount')) {

        function getMSDSCount()
        {
            $data = MSDSCheckList::get()->count();
            return $data;
        }
    }

    if (!function_exists('getSPLBCount')) {

        function getSPLBCount()
        {
            $data = SafetyPettyChecklist::get()->count();
            return $data;
        }
    }

    if (!function_exists('getRRAACount')) {

        function getRRAACount()
        {
            $data = RRAACheckList::get()->count();
            return $data;
        }
    }

    if (!function_exists('getCategoryname')) {

        function getCategoryname($id)
        {
            $category_name = ChecklistType::where('id', $id)->where('trash', 'NO')->first();

            if ($category_name === null) {
                return '';
            }

            return $category_name->category_name;
        }
    }

    if (!function_exists('getFrequencyname')) {

        function getFrequencyname($id)
        {
            $frequency_name = Frequency::where('id', $id)->where('trash', 'NO')->first();

            if ($frequency_name === null) {
                return '';
            }

            return $frequency_name->frequency_name;
        }
    }


    if (!function_exists('getUnitname')) {

        function getUnitname($userid)
        {

            $unit_name = DB::table('masters_unit')->select('unit_name')->where('id', $userid)->where('trash', 'NO')->first();

            if ($unit_name == null) {
                return '';
            } else {
                return $unit_name->unit_name;
            }
        }
    }

    if (!function_exists('GetForkLiftType')) {

        function GetForkLiftType($id)
        {

            $forklift_type = ForkLiftType::where('id', $id)->where('trash', 'NO')->first();

            if ($forklift_type == null) {
                return '';
            } else {
                return $forklift_type->forklift;
            }
        }
    }


    if (!function_exists('GetFrequency')) {

        function GetFrequency($id)
        {

            $forklift_type = Frequency::where('id', $id)->where('trash', 'NO')->first();

            if ($forklift_type == null) {
                return '';
            } else {
                return $forklift_type->frequency_name;
            }
        }
    }

    if (!function_exists('getItemCode')) {

        function getItemCode($userid)
        {

            $item_code = DB::table('ppe_stock_inventory')->select('item_code')->where('id', $userid)->where('trash', 'NO')->first();

            if ($item_code == null) {
                return '';
            } else {
                return $item_code->item_code;
            }
        }
    }

    if (!function_exists('getCompanyname')) {

        function getCompanyname($userid)
        {

            $company_name = DB::table('company_management')->select('company_name')->where('id', $userid)->where('trash', 'NO')->first();

            if ($company_name == null) {
                return '';
            } else {
                return $company_name->company_name;
            }
        }
    }

    if (!function_exists('getLocationname')) {

        function getLocationname($userid)
        {

            $location_name = DB::table('masters_location')->select('location_name')->where('id', $userid)->where('trash', 'NO')->first();

            if ($location_name == null) {
                return '';
            } else {
                return $location_name->location_name;
            }
        }
    }

    if (!function_exists('getBloodGroupname')) {

        function getBloodGroupname($userid)
        {

            $blood_group_name = DB::table('masters_blood_group')->select('blood_group_name')->where('id', $userid)->where('trash', 'NO')->first();

            if ($blood_group_name == null) {
                return '';
            } else {
                return $blood_group_name->blood_group_name;
            }
        }
    }
    if (!function_exists('getStatus')) {

        function getStatus($userid)
        {

            $status = DB::table('status')->select('approve_status')->where('id', $userid)->first();

            if ($status == null) {
                return '';
            } else {
                return $status->approve_status;
            }
        }
    }
    if (!function_exists('getMedicinename')) {

        function getMedicinename($userid)
        {

            $medicine = DB::table('ohc_master_medicine')->select('medicine')->where('id', $userid)->first();

            if ($medicine == null) {
                return '';
            } else {
                return $medicine->medicine;
            }
        }
    }
    if (!function_exists('gethsn')) {

        function gethsn($userid)
        {

            $hsn = DB::table('ohc_master_medicine')->select('hsn')->where('id', $userid)->first();

            if ($hsn == null) {
                return '';
            } else {
                return $hsn->hsn;
            }
        }
    }
    if (!function_exists('getSuggestedBy')) {

        function getSuggestedBy($userid)
        {

            $suggested_by = DB::table('ohc_management_opd_patient_suggested_by')->select('suggested_by')->where('id', $userid)->where('status', 1)->where('trash', 'NO')->first();

            if ($suggested_by == null) {
                return '';
            } else {
                return $suggested_by->suggested_by;
            }
        }
    }
    if (!function_exists('getPatientStatus')) {

        function getPatientStatus($userid)
        {

            $patient_status = DB::table('ohc_management_opd_patient_status')->select('patient_status')->where('id', $userid)->where('status', 1)->where('trash', 'NO')->first();

            if ($patient_status == null) {
                return '';
            } else {
                return $patient_status->patient_status;
            }
        }
    }
    if (!function_exists('getReferedVechicle')) {

        function getReferedVechicle($userid)
        {

            $refered_vechicle = DB::table('ohc_management_opd_patient_refered_vechicle')->select('refered_vechicle')->where('id', $userid)->where('status', 1)->where('trash', 'NO')->first();

            if ($refered_vechicle == null) {
                return '';
            } else {
                return $refered_vechicle->refered_vechicle;
            }
        }
    }
    if (!function_exists('getPersonalCondition')) {

        function getPersonalCondition($userid)
        {

            $injured_condtion = DB::table('ohc_opd_injured_condition')->select('injured_condtion')->where('id', $userid)->where('status', 1)->where('trash', 'NO')->first();

            if ($injured_condtion == null) {
                return '';
            } else {
                return $injured_condtion->injured_condtion;
            }
        }
    }
    function removeUnderScore($string)
    {
        return Str::replace('_', " ", $string);
    }

    if (!function_exists('get_permit_no')) {
        function get_permit_no($id)
        {

            $data = SafetyPermit::where('id', $id)->select('permit_id')->first();
            return $data->permit_id;
        }
    }

    if (!function_exists('ShoerequestStatusCount')) {
        function ShoerequestStatusCount($type = '', $params = [])
        {
            $request = new PpeRequest();
            return $request->statusCount($type, $params);
        }
    }

    if (!function_exists('ShoeExemptionStatusCount')) {
        function ShoeExemptionStatusCount($type = '', $params = [])
        {
            $exemption = new PpeExemption();
            return $exemption->statusCount($type, $params);
        }
    }

    if (!function_exists('getDocumentReviewDate')) {
        function getDocumentReviewDate($type)
        {
            return $type . ' - ' . now()->format('d-m-Y');
        }
    }
    if (!function_exists('getCheckListType')) {
        function getCheckListType($type)
        {

            $data = ChecklistType::where('category_name', $type)->first();
            if ($data) {
                return $data->id;
            }
            return 0;
        }
    }

    if (!function_exists('GetSubChecklistTypeName')) {
        function GetSubChecklistTypeName($id)
        {
            $data = ChecklistSubType::where('id', $id)->first();
            if ($data) {
                return $data->subcategory_name;
            }
            return false;
        }
    }
    if (!function_exists('GetChecklistTypeDate')) {
        function GetChecklistTypeDate($id)
        {
            $data = ChecklistSubTypeDataName::where('id', $id)->first();
            if ($data) {
                return $data->name;
            }
            return false;
        }
    }


    if (!function_exists('GetEHSOfficer')) {
        function GetEHSOfficer()
        {
            $data = User::whereRaw('FIND_IN_SET(' . ROLE_EHS_OFFICER . ', role)')->where('status', 1)->where('trash', 'NO')->get();

            if (count($data) != 0) {
                return $data;
            }

            return false;
        }
    }

    if (!function_exists('GetLevelOneManager')) {
        function GetLevelOneManager()
        {
            $data = User::whereRaw('FIND_IN_SET(' . ROLE_L1_MANAGER . ', role)')->where('status', 1)->where('trash', 'NO')->get();

            if (count($data) != 0) {
                return $data;
            }

            return false;
        }
    }

    if (!function_exists('GetLevelTwoManager')) {
        function GetLevelTwoManager()
        {
            $data = User::whereRaw('FIND_IN_SET(' . ROLE_L1_MANAGER . ', role)')->where('status', 1)->where('trash', 'NO')->get();

            if (count($data) != 0) {
                return $data;
            }

            return false;
        }
    }

    if (!function_exists('GetSuperAdmin')) {
        function GetSuperAdmin()
        {
            $data = User::whereRaw('FIND_IN_SET(' . ROLE_SUPERADMIN . ', role)')->pluck('id')->toArray();

            if (count($data) != 0) {
                return $data;
            }

            return false;
        }
    }


    if (!function_exists('getFloormanager')) {
        function getFloormanager()
        {
            $data = User::whereRaw('FIND_IN_SET(' . ROLE_FLOOR_MANAGER . ', role)')->where('status', 1)->where('trash', 'NO')->get();

            if (count($data) != 0) {
                return $data;
            }

            return false;
        }
    }
    if (!function_exists('getSafetyOfficer')) {
        function getSafetyOfficer()
        {
            $data = User::whereRaw('FIND_IN_SET(' . ROLE_SAFETY_OFFICER . ', role)')->where('status', 1)->where('trash', 'NO')->get();

            if (count($data) != 0) {
                return $data;
            }

            return false;
        }
    }
    if (!function_exists('getMedicalAssistant')) {
        function getMedicalAssistant()
        {
            $data = User::whereRaw('FIND_IN_SET(' . ROLE_MEDICIAL_ASSISITANT . ', role)')->where('status', 1)->where('trash', 'NO')->get();

            if (count($data) != 0) {
                return $data;
            }

            return false;
        }
    }

    if (!function_exists('getCheckListQuestion')) {
        function getCheckListQuestion($id)
        {
            $data = ChecklistType::join('inspection_master_checklist_subtype', 'inspection_master_checklist_type.id', '=', 'inspection_master_checklist_subtype.category_id')
                ->join('inspection_master_checklist_sub_type_data', 'inspection_master_checklist_subtype.id', '=', 'inspection_master_checklist_sub_type_data.checklist_sub_type_id')
                ->join('inspection_master_checklist_sub_type_data_name', 'inspection_master_checklist_subtype.id', '=', 'inspection_master_checklist_sub_type_data_name.checklist_sub_type_data_id')
                ->join('inspection_master_checklist_option', 'inspection_master_checklist_option.id', '=', 'inspection_master_checklist_type.questionary')
                ->where('inspection_master_checklist_type.id', $id)

                ->get([
                    'inspection_master_checklist_subtype.*',
                    'inspection_master_checklist_type.*',
                    'inspection_master_checklist_sub_type_data.*',
                    'inspection_master_checklist_subtype.id as sub_type_id',
                    'inspection_master_checklist_sub_type_data_name.*',
                    'inspection_master_checklist_sub_type_data_name.id as checklist_id',
                    'inspection_master_checklist_sub_type_data_name.name as checklist_name',
                    'inspection_master_checklist_option.type',
                ]);
            $data = $data->groupBy('subcategory_name');

            if ($data) {
                return $data;
            }
            return false;
        }
    }

    if (!function_exists('getoption')) {
        function getoption($id)
        {

            $data = ChecklistType::join('inspection_master_checklist_option', 'inspection_master_checklist_option.id', '=', 'inspection_master_checklist_type.questionary')
                ->where('inspection_master_checklist_type.id', $id)

                ->first([
                    'inspection_master_checklist_option.type'
                ]);

            if ($data) {
                return $data;
            }
            return false;
        }
    }

    if (!function_exists('getShift')) {

        function getShift($userid)
        {

            $shift = DB::table('inspection_shift_option')->select('shift')->where('id', $userid)->where('trash', 'NO')->first();

            if ($shift == null) {
                return '';
            } else {
                return $shift->shift;
            }
        }
    }

    if (!function_exists('getSubcategoryname')) {

        function getSubcategoryname($userid)
        {

            $subcategory_name = DB::table('inspection_master_checklist_subtype')->select('subcategory_name')->where('id', $userid)->where('trash', 'NO')->first();

            if ($subcategory_name == null) {
                return '';
            } else {
                return $subcategory_name->subcategory_name;
            }
        }
    }
    if (!function_exists('getSubcategoryDataname')) {

        function getSubcategoryDataname($userid)
        {

            $name = DB::table('inspection_master_checklist_sub_type_data_name')->select('name')->where('id', $userid)->where('trash', 'NO')->first();

            if ($name == null) {
                return '';
            } else {
                return $name->name;
            }
        }
    }

    if (!function_exists('GetSafetySignature')) {

        function GetSafetySignature($userid, $id, $type)
        {

            $name = DB::table('inspection_safety_signatureupload')->select('*')->where('emp_id', $userid)->where('inspection_id', $id)->where('type', $type)->where('trash', 'NO')->first();
            if ($name == null) {
                return '';
            } else {
                return $name->file_path;
            }
        }
    }

    if (!function_exists('GetSignature')) {
        function GetSignature($userid, $id, $type)
        {
            switch ($type) {
                case RRAA_INSPECTION:
                    $name = DB::table('inspection_rraa_signatureupload')->select('*')->where('emp_id', $userid)->where('inspection_id', $id)->where('trash', 'NO')->first();
                    if ($name == null) {
                        return '';
                    } else {
                        return $name->file_path;
                    }
                case MSDS_INSPECTION:

                    $name = DB::table('inspection_msds_signatureupload')->select('*')->where('emp_id', $userid)->where('inspection_id', $id)->where('trash', 'NO')->first();
                    if ($name == null) {
                        return '';
                    } else {
                        return $name->file_path;
                    }
                    case OHC_TYPE_MEDICINE_REQUISTION_FLOOR:
                        $name = DB::table('inspection_ohc_signatureupload')->select('*')->where('emp_id', $userid)->where('ohc_id', $id)->where('trash', 'NO')->first();
                        if ($name == null) {
                            return '';
                        } else {
                            return $name->file_path;
                        }
            }
        }
    }

    if (!function_exists('getInspectionStatus')) {
        function getInspectionStatus($id)
        {
            if ($id == WAITING_FOR_EHS_OFFICER_VERIFICATION) {
                return 'Waiting For EHS Officer Verification';
            } else if ($id == WAITING_FOR_CAPA_ACTION) {
                return 'Waiting for CAPA Action';
            } else if ($id == WAITING_FOR_CAPA_VERIFICATION) {
                return 'Waiting For CAPA Verification';
            } else if ($id == WAITING_FOR_L1_VERIFICATION) {
                return 'EHS Officer Approved - Waiting for Level one Manager Verification';
            } else if ($id == WAITING_FOR_L2_VERIFICATION) {
                return 'Level One Manager Approved - Waiting for Leven two Manager Verification';
            } else if ($id == INSPECTION_APPROVED) {
                return 'Closed';
            } else if ($id == EHS_OFFICER_REJECTED) {
                return 'EHS Officer Rejected - Waiting For CAPA Action';
            } else if ($id == L1_MANAGER_REJECTED) {
                return 'Level One Manager Rejected - Waiting For CAPA Action';
            } else if ($id == L2_MANAGER_REJECTED) {
                return 'Level Two Manager Rejected - Waiting For CAPA Action';
            }

            return 'Inspection Creation';
        }
    }

    if (!function_exists('getohcrequisitionfloorstatus')) {
        function getohcrequisitionfloorstatus($id)
        {
            if ($id == FLOOR_MANAGER_APPROVAL_PENDING) {
                return 'floor manager Approval Pending';
            } else if ($id == FLOOR_MANAGER_APPROVED) {
                return 'floor Manager Approved';
            } else if ($id == FLOOR_MANAGER_REJECTED) {
                return 'floor Manager rejected';
            } else if ($id == SAFETY_OFFICER_APPROVAL_PENDING) {
                return 'Safet Officer Approval Pending';
            } else if ($id == SAFETY_OFFICER_APPROVED) {
                return 'Safet Officer Approved';
            }

            return 'OHC Creation';
        }
    }

    if (!function_exists('getShiftname')) {

        function getShiftname($shift_id)
        {

            $shift_name = DB::table('inspection_shift_option')->select('shift')->where('id', $shift_id)->where('trash', 'NO')->first();

            if ($shift_name == null) {
                return '';
            } else {
                return $shift_name->shift;
            }
        }
    }

    // Monthly Eye Wash Sequence
    if (!function_exists('MEWSequence')) {
        function MEWSequence()
        {
            return 'MEW-000001';
        }
    }

    if (!function_exists('getGMInspectionStatus')) {
        function getGMInspectionStatus($id)
        {
            if ($id == GEMBA_WALK_INSPECTION_START) {
                return 'Gemba Walk Start';
            } else if ($id == GEMBA_WALK_INSPECTION_WAITING_FOR_CAPA_ACTION) {
                return 'Waiting for CAPA Action';
            } else if ($id == GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION) {
                return 'Waiting for Floor manager Action';
            } else if ($id == GEMBA_WALK_INSPECTION_WAITING_FOR_EHS_OFFICER_VERIFICATION) {
                return 'Waiting for EHS Officer Verification';
            } else if ($id == GEMBA_WALK_INSPECTION_CLOSED) {
                return 'Inspection Closed';
            }

            return 'Inspection Creation';

            return '<span class="badge ' . $badgeClass . '">' . $status . '</span>';
        }
    }

    // Fire Inspection Hooter Sequence
    if(!function_exists('HooterSequence'))
    {
        function HooterSequence()
        {
            return 'HTR-000001';
        }
    }

    // Fire Inspection Folder Name
    if(!function_exists('GetTypeName'))
    {
        function GetTypeName($id)
        {
            switch($id)
            {
                case HOOTER_INSPECTION:
                    return 'Hooter-Inspection';
                    break;
                
                default:
                    break;
            }   
        }
    }

    // Get Department Name
    if(!function_exists('GetDeptName'))
    {
        function GetDeptName($id)
        {
            $data = Department::where('id',$id)->first();

            if($data)
            {
                return $data->department_name;
            }
        }
    }
}
