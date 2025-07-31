<?php


use Carbon\Carbon;
use App\Models\User;
use App\Models\FcmToken;
use App\Models\IMS\Incident\InitialIncident;
use App\Models\LeftMenu;
use Illuminate\Support\Str;
use App\Models\Master\Fleet;
use App\Models\Notification;
use App\Models\UploadLogType;
use App\Models\UserPermission;
use App\Models\Master\Employee;
use App\Models\Master\UserRole;
use App\Models\IMS\Incident\Rcpa;
use App\Models\Master\Department;
use App\Models\Master\PpeRequest;
use App\Models\Master\TypeofWork;
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
use App\Models\Inspection\Fire\HoseBoxType;
use App\Models\Inspection\Master\Frequency;
use App\Models\Inspection\MSDS\MSDSDetails;
use App\Models\Inspection\Ohc\OhcSignature;
use App\Models\Inspection\RRAA\RRAADetails;
use Kreait\Firebase\Messaging\CloudMessage;
use App\Models\Inspection\audit\Master\Task;
use App\Models\Inspection\Fire\DetectorType;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\WebPushConfig;
use App\Models\Inspection\Fire\FireStatusLog;
use App\Models\Inspection\MSDS\MSDSCheckList;
use App\Models\Inspection\RRAA\RRAACheckList;
use App\Models\Inspection\audit\AuditAnalysis;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\Inspection\Ohc\SafetyPettyDetails;
use App\Models\Inspection\Safety\SafetyStatusLog;
use App\Models\Inspection\Safety\SignatureUpload;
use App\Models\Inspection\Fire\IsolatingValveType;
use App\Models\Inspection\Master\ChecklistSubType;
use App\Models\Inspection\Ohc\DailyVitalEquipment;
use App\Models\Inspection\Ohc\FloorStretcherFiles;
use App\Models\Inspection\Fire\FireSignatureUpload;
use App\Models\Inspection\MSDS\MSDSSignatureUpload;
use App\Models\Inspection\Ohc\SafetyPettyChecklist;
use App\Models\Inspection\RRAA\RRAASignatureUpload;
use App\Models\Inspection\Fire\FireExtinguisherType;
use App\Models\Inspection\GembaWalk\GembaWalkStatus;
use App\Models\Inspection\Ohc\FirstAiderListDetails;
use App\Models\Inspection\Fire\FireCheckListFollowUp;
use App\Models\Inspection\Master\ChecklistSubTypeData;
use App\Models\Inspection\Ohc\FirstAidRecordChecklist;
use App\Models\Inspection\GembaWalk\GembaWalkStatusLog;
use App\Models\Inspection\Safety\SafetyWalkObservation;
use App\Models\Inspection\Ohc\DailyDepartmentFirstAidBox;
use App\Models\Inspection\Master\ChecklistSubTypeDataName;
use App\Models\Inspection\Ohc\MonthlyFirstAidboxChecklist;
use App\Models\Inspection\GembaWalk\GembaWalkChecklistFile;
use App\Models\Inspection\Ohc\MedicineRequisitionSlipFloor;
use App\Models\Inspection\Environment\AmbientNoiseMonitoring;
use App\Models\Inspection\Ohc\MedicineRequistionFdoChecklist;
use App\Models\Inspection\Safety\MonthlyPhysicalEquipmentList;
use App\Models\Inspection\Safety\SafetyWalkObservationDetails;
use App\Models\Inspection\Fire\EmergencyLightInspectionDetails;
use App\Models\Inspection\Fire\MonthlyPhysicalInspectionFileUpload;
use App\Models\Inspection\GembaWalk\GembaWalkHazard;
use App\Models\Inspection\MSDS\Master\Chemical;
use App\Models\Inspection\MSDS\Master\NFARating;
use App\Models\KPI\HSCInputs;
use App\Models\KPI\HSCInputsLagging;
use App\Models\KPI\HSCInputsLeading;
use App\Models\KPI\LeadingLagging;
use App\Models\Master\PpeStockinventory;
use App\Models\OhcManagement\Opd\FirstAid;
use App\Models\OhcManagement\Opd\PrescribetoPatient;
use App\Models\OhcManagement\Opd\RoadsideFirstAid;

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

if (!function_exists('getOkNotokStatus')) {

    function getOkNotokStatus($value)
    {
        $status = "";
        if ($value == 0) {
            $status = "Not Ok";
        } elseif ($value == 1) {
            $status = "Ok";
        } else {
            $status = "-";
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


if (!function_exists('getActiveOrInactive')) {

    function getActiveOrInactive($value)
    {
        $status = "";
        if ($value == 0) {
            $status = "In-Active";
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
    if (!function_exists('getClinicalDetails')) {

        function getClinicalDetails($userid)
        {

            $personal_details = DB::table('inspection_ohc_physical_examination_clinical')->select('personal_details')->where('id', $userid)->where('status', 1)->where('trash', 'NO')->first();

            if ($personal_details == null) {
                return '';
            } else {
                return $personal_details->personal_details;
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
            $data = MSDSDetails::get()->count();
            return $data;
        }
    }
    if (!function_exists('getAnalysisCount')) {

        function getAnalysisCount()
        {
            $data = AuditAnalysis::get()->count();
            return $data;
        }
    }

    if (!function_exists('getObservation')) {

        function getObservation()
        {
            $data = FireCheckListFollowUp::get()->count();
            return $data;
        }
    }

    if (!function_exists('getSPLBCount')) {

        function getSPLBCount()
        {
            $data = SafetyPettyDetails::get()->count();
            return $data;
        }
    }

    if (!function_exists('getRRAACount')) {

        function getRRAACount()
        {
            $data = RRAADetails::get()->count();
            return $data;
        }
    }

    if (!function_exists('getFIRCount')) {

        function getFIRCount()
        {
            $data = FirstAidRecordChecklist::get()->count();
            return $data;
        }
    }

    if (!function_exists('getRCPACount')) {

        function getRCPACount()
        {
            $data = Rcpa::get()->count();
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

    if (!function_exists('getIIRTypename')) {

        function getIIRTypename($userid)
        {

            $iirType = DB::table('ims_master_incident_type')->select('*')->where('id', $userid)->where('trash', 'NO')->first();

            if ($iirType == null) {
                return '';
            } else {
                return $iirType->incident_type_name;
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


    if (!function_exists('getEquipmentName')) {

        function getEquipmentName($userid)
        {

            $equipment_name = DB::table('inspection_safety_master_equipment')->select('equipment_name')->where('id', $userid)->where('trash', 'NO')->first();

            if ($equipment_name == null) {
                return '';
            } else {
                return $equipment_name->equipment_name;
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

    if (!function_exists('getHospitalname')) {

        function getHospitalname($userid)
        {

            $hospital_name = DB::table('ohc_master_hospital_details')->select('hospital_name')->where('id', $userid)->first();

            if ($hospital_name == null) {
                return '';
            } else {
                return $hospital_name->hospital_name;
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
    if (!function_exists('getFirstAider')) {

        function getFirstAider($userid)
        {

            $certifier_name = DB::table('ohc_master_certified_first_aider')->select('certifier_name')->where('id', $userid)->where('status', 1)->where('trash', 'NO')->first();

            if ($certifier_name == null) {
                return '';
            } else {
                return $certifier_name->certifier_name;
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

    if (!function_exists('GetEHSHead')) {
        function GetEHSHead()
        {
            $data = User::whereRaw('FIND_IN_SET(' . ROLE_EHS_HEAD . ', role)')->where('status', 1)->where('trash', 'NO')->get();

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
            $data = User::whereRaw('FIND_IN_SET(' . ROLE_L2_MANAGER . ', role)')->where('status', 1)->where('trash', 'NO')->get();

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
            $data = User::whereRaw('FIND_IN_SET(' . ROLE_MEDICAL_ASSISTANT . ', role)')->where('status', 1)->where('trash', 'NO')->get();

            if (count($data) != 0) {
                return $data;
            }

            return false;
        }
    }

    if (!function_exists('getNursingOfficer')) {
        function getNursingOfficer()
        {
            $roleId = ROLE_NURSING_OFFICER;

            $data = User::whereRaw("FIND_IN_SET(?, role)", [$roleId])
                ->where('status', 1)
                ->where('trash', 'NO')
                ->get();

            return $data->isNotEmpty() ? $data : false;
        }
    }


    if (!function_exists('getCheckListQuestion')) {
        function getCheckListQuestion($id)
        {

            $data = ChecklistType::join('inspection_master_checklist_subtype', 'inspection_master_checklist_type.id', '=', 'inspection_master_checklist_subtype.category_id')
                ->join('inspection_master_checklist_sub_type_data', 'inspection_master_checklist_subtype.id', '=', 'inspection_master_checklist_sub_type_data.checklist_sub_type_id')
                ->join('inspection_master_checklist_sub_type_data_name', 'inspection_master_checklist_sub_type_data.id', '=', 'inspection_master_checklist_sub_type_data_name.checklist_sub_type_data_id')
                ->leftJoin('inspection_master_checklist_option', 'inspection_master_checklist_option.id', '=', 'inspection_master_checklist_type.questionary')
                ->where('inspection_master_checklist_type.id', $id)
                ->where('inspection_master_checklist_sub_type_data_name.status', 1)

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

    if (!function_exists('getObservationCAPAStatus')) {
        function getObservationCAPAStatus($id)
        {
            if ($id == 1) {
                return 'Open';
            } else if ($id == 2) {
                return 'In-Progress';
            } else {
                return 'Closed';
            }
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

            $name = SignatureUpload::where('emp_id', $userid)->where('inspection_id', $id)->where('type', $type)->where('trash', 'NO')->first();
            if ($name == null) {
                $name = User::where('id', $userid)->first();
                if ($name == null) {
                    return null;
                }
                return $name->signature_upload;
            } else {
                return $name->file_path;
            }
        }
    }

    if (!function_exists('GetSafetyUpdatedTime')) {
        function GetSafetyUpdatedTime($userid, $id, $type, $from_status)
        {
            $created_by = $from_status == WAITING_FOR_CAPA_ACTION ? 'created_by' : 'approved_by';

            $statusLog = SafetyStatusLog::where($created_by, $userid)
                ->where('inspection_id', $id)
                ->where('type', $type)
                ->where('from_status', $from_status)
                ->first();

            return $statusLog ?? null;
        }
    }

    if (!function_exists('GetFireUpdatedTime')) {
        function GetFireUpdatedTime($userid, $id, $type, $from_status)
        {
            $created_by = $from_status == WAITING_FOR_CAPA_ACTION ? 'created_by' : 'approved_by';

            $statusLog = FireStatusLog::where($created_by, $userid)
                ->where('inspection_id', $id)
                ->where('type', $type)
                ->where('from_status', $from_status)
                ->first();

            return $statusLog ?? null;
        }
    }


    if (!function_exists('GetOHCSignature')) {

        function GetOHCSignature($userid, $id, $type)
        {

            $name = OhcSignature::where('emp_id', $userid)->where('ohc_id', $id)->where('type', $type)->where('trash', 'NO')->first();
            if ($name == null) {
                $name = User::where('id', $userid)->first();
                if ($name == null) {
                    return null;
                }
                return $name->signature_upload;
            } else {
                return $name->file_path;
            }
        }
    }

    if (!function_exists('GetOHCMedicineFDO')) {

        function GetOHCMedicineFDO($id)
        {

            $medicineRequisition = MedicineRequistionFdoChecklist::where('reference_id', $id)->where('status', 1)->where('trash', 'NO')->get();
            return $medicineRequisition;
        }
    }

    if (!function_exists('GetOHCMedicineFloor')) {

        function GetOHCMedicineFloor($id)
        {

            $medicineRequisition = MedicineRequisitionSlipFloor::where('reference_id', $id)->where('status', 1)->where('trash', 'NO')->get();
            return $medicineRequisition;
        }
    }
    if (!function_exists('GetFirstAiderList')) {

        function GetFirstAiderList($id)
        {

            $medicineRequisition = FirstAiderListDetails::where('reference_id', $id)->where('trash', 'NO')->get();
            return $medicineRequisition;
        }
    }
    if (!function_exists('GetOHCDailyDepartment')) {

        function GetOHCDailyDepartment($id)
        {

            $medicineRequisition = DailyDepartmentFirstAidBox::where('reference_id', $id)->where('status', 1)->where('trash', 'NO')->get();
            return $medicineRequisition;
        }
    }
    if (!function_exists('GetEmeregencyLightInspection')) {

        function GetEmeregencyLightInspection($id)
        {

            $medicineRequisition = EmergencyLightInspectionDetails::where('inspection_id', $id)->where('status', 1)->where('trash', 'NO')->get();
            return $medicineRequisition;
        }
    }

    if (!function_exists('GetMonthlyAuditChecklist')) {

        function GetMonthlyAuditChecklist($id)
        {

            $medicineRequisition = MonthlyFirstAidboxChecklist::where('reference_id', $id)->where('status', 1)->where('trash', 'NO')->get();
            return $medicineRequisition;
        }
    }
    if (!function_exists('GetSignature')) {
        function GetSignature($userid, $id, $type)
        {
            switch ($type) {
                case RRAA_INSPECTION:
                    $name = RRAASignatureUpload::where('emp_id', $userid)->where('inspection_id', $id)->where('trash', 'NO')->first();
                    if ($name == null) {
                        $name = User::where('id', $userid)->first();
                        if ($name == null) {
                            return null;
                        }
                        return $name->signature_upload;
                    } else {
                        return $name->file_path;
                    }
                case MSDS_INSPECTION:
                    $name = MSDSSignatureUpload::where('emp_id', $userid)->where('inspection_id', $id)->where('trash', 'NO')->first();
                    if ($name == null) {
                        $name = User::where('id', $userid)->first();
                        if ($name == null) {
                            return null;
                        }
                        return $name->signature_upload;
                    } else {
                        return $name->file_path;
                    }
                case HOOTER_INSPECTION:
                    $name = FireSignatureUpload::where('emp_id', $userid)->where('inspection_id', $id)->where('type', HOOTER_INSPECTION)
                        ->where('status', 1)->where('trash', 'NO')->first();

                    if ($name == null) {
                        $name = User::where('id', $userid)->first();
                        if ($name == null) {
                            return null;
                        }
                        return $name->signature_upload;
                    } else {
                        return $name->file_path;
                    }

                case EMERGENCY_LIGHT_INSPECTION:
                    $name = FireSignatureUpload::where('emp_id', $userid)->where('inspection_id', $id)->where('type', EMERGENCY_LIGHT_INSPECTION)
                        ->where('status', 1)->where('trash', 'NO')->first();

                    if ($name == null) {
                        $name = User::where('id', $userid)->first();
                        if ($name == null) {
                            return null;
                        }
                        return $name->signature_upload;
                    } else {
                        return $name->file_path;
                    }

                case MONTHLY_FIRE_PUMP:
                    $name = FireSignatureUpload::where('emp_id', $userid)->where('inspection_id', $id)->where('type', MONTHLY_FIRE_PUMP)
                        ->where('status', 1)->where('trash', 'NO')->first();

                    if ($name == null) {
                        $name = User::where('id', $userid)->first();
                        if ($name == null) {
                            return null;
                        }
                        return $name->signature_upload;
                    } else {
                        return $name->file_path;
                    }
                case OBSERVATION_FOLLOWUP:
                    $name = FireSignatureUpload::where('emp_id', $userid)->where('inspection_id', $id)->where('type', OBSERVATION_FOLLOWUP)
                        ->where('status', 1)->where('trash', 'NO')->first();

                    if ($name == null) {
                        $name = User::where('id', $userid)->first();
                        if ($name == null) {
                            return null;
                        }
                        return $name->signature_upload;
                    } else {
                        return $name->file_path;
                    }

                case FIRE_EXTINGUISHER_INSPECTION:
                    $name = FireSignatureUpload::where('emp_id', $userid)->where('inspection_id', $id)->where('type', FIRE_EXTINGUISHER_INSPECTION)
                        ->where('status', 1)->where('trash', 'NO')->first();

                    if ($name == null) {
                        $name = User::where('id', $userid)->first();
                        if ($name == null) {
                            return null;
                        }
                        return $name->signature_upload;
                    } else {
                        return $name->file_path;
                    }

                case ISOLATION_VALVE_INSPECTION:
                    $name = FireSignatureUpload::where('emp_id', $userid)->where('inspection_id', $id)->where('type', ISOLATION_VALVE_INSPECTION)
                        ->where('status', 1)->where('trash', 'NO')->first();

                    if ($name == null) {
                        $name = User::where('id', $userid)->first();
                        if ($name == null) {
                            return null;
                        }
                        return $name->signature_upload;
                    } else {
                        return $name->file_path;
                    }

                case FIRE_ALARM_INSPECTION:
                    $name = FireSignatureUpload::where('emp_id', $userid)->where('inspection_id', $id)->where('type', FIRE_ALARM_INSPECTION)
                        ->where('status', 1)->where('trash', 'NO')->first();

                    if ($name == null) {
                        $name = User::where('id', $userid)->first();
                        if ($name == null) {
                            return null;
                        }
                        return $name->signature_upload;
                    } else {
                        return $name->file_path;
                    }

                case OHC_TYPE_FLOOR_STRETCHER:
                    $name = FloorStretcherFiles::where('emp_id', $userid)->where('inspection_id', $id)->where('type', OHC_TYPE_FLOOR_STRETCHER)
                        ->where('status', 1)->where('trash', 'NO')->first();

                    if ($name == null) {
                        $name = User::where('id', $userid)->first();
                        if ($name == null) {
                            return null;
                        }
                        return $name->signature_upload;
                    } else {
                        return $name->file_path;
                    }

                case FIRE_PA_SYSTEM_INSPECTION:
                    $name = FireSignatureUpload::where('emp_id', $userid)->where('inspection_id', $id)->where('type', FIRE_PA_SYSTEM_INSPECTION)
                        ->where('status', 1)->where('trash', 'NO')->first();

                    if ($name == null) {
                        $name = User::where('id', $userid)->first();
                        if ($name == null) {
                            return null;
                        }
                        return $name->signature_upload;
                    } else {
                        return $name->file_path;
                    }


                case GEMBA_WALK:
                    $name = GembaWalkChecklistFile::where('emp_id', $userid)->where('gemba_walk_id', $id)->where('trash', 'NO')->first();
                    if ($name == null) {
                        $name = User::where('id', $userid)->first();
                        if ($name == null) {
                            return null;
                        }
                        return $name->signature_upload;
                    } else {
                        return $name->file_path;
                    }

                case CO_TYPE_FIRE_EXTINGUISHER_INSPECTION:
                    $name = FireSignatureUpload::where('emp_id', $userid)->where('inspection_id', $id)->where('type', CO_TYPE_FIRE_EXTINGUISHER_INSPECTION)
                        ->where('status', 1)->where('trash', 'NO')->first();

                    if ($name == null) {
                        $name = User::where('id', $userid)->first();
                        if ($name == null) {
                            return null;
                        }
                        return $name->signature_upload;
                    } else {
                        return $name->file_path;
                    }

                case DAILY_FIRE_PUMP:
                    $name = FireSignatureUpload::where('emp_id', $userid)->where('inspection_id', $id)->where('type', DAILY_FIRE_PUMP)
                        ->where('status', 1)->where('trash', 'NO')->first();

                    if ($name == null) {
                        $name = User::where('id', $userid)->first();
                        if ($name == null) {
                            return null;
                        }
                        return $name->signature_upload;
                    } else {
                        return $name->file_path;
                    }
            }
        }
    }

    if (!function_exists('getSafetyObservationStatus')) {
        function getSafetyObservationStatus($id)
        {
            if ($id == 1) {
                return "Waiting For EHS Officer Verification";
            } else if ($id == 2) {
                return "Observation Rejected";
            } else if ($id == 3) {
                return "Observation Approved";
            } else {
                return "Inspection Creation";
            }
        }
    }


    if (!function_exists('getSafetyWalkStatus')) {
        function getSafetyWalkStatus($id)
        {
            if ($id == RESPONSIBLE_PERSON_APPROVAL_PENDING) {
                return "Waiting For Responsible Person Action";
            } else if ($id == SAFETY_WALK_EHS_OFFICER_PENDING) {
                return "Waiting For EHS Officer Approval";
            } else if ($id == SAFETY_WALK_EHS_OFFICER_REJECTED) {
                return "Rejected by EHS Officer ";
            } else if ($id == SAFETY_WALK_EHS_OFFICER_APPROVED) {
                return "Closed ";
            }else if ($id == SAFETY_WALK_EHS_OFFICER_ON_PROCESS) {
                return "Waiting for the Re-verification of Responsible Person";
            }  else {
                return "Inspection Creation";
            }
        }
    }

    if (!function_exists('getForkLiftInspectionStatus')) {
        function getForkLiftInspectionStatus($id)
        {
            if ($id == 1) {
                return "Waiting For EHS Head Verification";
            } else if ($id == 2) {
                return "Observation Rejected";
            } else if ($id == 3) {
                return "Observation Approved";
            } else {
                return "Inspection Creation";
            }
        }
    }

    if (!function_exists('getOhcHygieneCleaningStatus')) {
        function getOhcHygieneCleaningStatus($id)
        {
            if ($id == 1) {
                return "Waiting For Nursing Officer Approval";
            } else if ($id == 2) {
                return "Inspection Approved";
            } else if ($id == 3) {
                return "Inspection Rejected";
            } else {
                return "Inspection Creation";
            }
        }
    }

    if (!function_exists('getFirstAidOpdStatus')) {
        function getFirstAidOpdStatus($id)
        {
            if ($id == 1) {
                return "Waiting For EHS Officer Verification";
            } else if ($id == 2) {
                return "Observation Rejected";
            } else if ($id == 3) {
                return "Observation Approved";
            } else {
                return "Inspection Creation";
            }
        }
    }

    if (!function_exists('getMonthlyMedicineStatus')) {
        function getMonthlyMedicineStatus($id)
        {
            if ($id == 1) {
                return "Waiting For EHS Officer Verification";
            } else if ($id == 2) {
                return "Observation Rejected";
            } else if ($id == 3) {
                return "Observation Approved";
            } else {
                return "Inspection Creation";
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
                return 'Level One Manager Approved - Waiting for Level two Manager Verification';
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
                return 'Safety Officer Approval Pending';
            } else if ($id == SAFETY_OFFICER_APPROVED) {
                return 'Safety Officer Approved';
            } else if ($id == SAFETY_OFFICER_REJECTED) {
                return 'Safety Officer rejected';
            } else if ($id == MEDICAL_ASSISTANT_APPROVAL_PENDING) {
                return 'Medical Assistant /Floor Manager  Approval Pending';
            } else if ($id == MEDICAL_ASSISTANT_REJECTED) {
                return 'Medical Assistant /Floor Manager rejected';
            } else if ($id == MEDICAL_ASSISTANT_APPROVED) {
                return 'Medical Assistant /Floor Manager Approved';
            }

            return 'OHC Creation';
        }
    }

    if (!function_exists('getDiscardStatus')) {
        function getDiscardStatus($id)
        {
            if ($id == OHC_DISCARD_EHS_APPROVAL_PENDING) {
                return 'EHS Head Approval Pending';
            } else if ($id == OHC_DISCARD_EHS_APPROVED) {
                return 'EHS Head Approved';
            } else if ($id == OHC_DISCARD_EHS_REJECTED) {
                return 'EHS Head rejected';
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

    if (!function_exists('GetSafetyWalkImage')) {

        function GetSafetyWalkImage($id)
        {

            $safetyImage = DB::table('inspection_safety_walk_observation_files')->where('safety_walk_observation_id', $id)->where('trash', 'NO')->first();

            if ($safetyImage == null) {
                return false;
            } else {
                return $safetyImage->file_path;
            }
        }
    }

    if (!function_exists('GetTypeofLight')) {

        function GetTypeofLight($id)
        {

            $type = DB::table('inspection_fire_master_type_of_light')->where('id', $id)->where('trash', 'NO')->first();

            if ($type == null) {
                return false;
            } else {
                return $type->type;
            }
        }
    }


    if (!function_exists('GetTypeofjob')) {

        function GetTypeofjob($id)
        {

            $typeofJob = DB::table('ptw_masters_typeofwork')->where('id', $id)->where('trash', 'NO')->first();

            if ($typeofJob == null) {
                return false;
            } else {
                return $typeofJob->work_name;
            }
        }
    }

    if (!function_exists('GetConditionofLight')) {

        function GetConditionofLight($id)
        {

            $condition = DB::table('inspection_fire_master_condition_of_light')->where('id', $id)->where('trash', 'NO')->first();

            if ($condition == null) {
                return false;
            } else {
                return $condition->condition;
            }
        }
    }

    if (!function_exists('GetPowerSuply')) {

        function GetPowerSuply($id)
        {

            $name = DB::table('inspection_fire_master_power_supply')->where('id', $id)->where('trash', 'NO')->first();

            if ($name == null) {
                return false;
            } else {
                return $name->name;
            }
        }
    }
    if (!function_exists('getFireLightInspectionStatus')) {

        function getFireLightInspectionStatus($id)
        {

            $name = DB::table('inspection_fire_master_status')->where('id', $id)->where('trash', 'NO')->first();

            if ($name == null) {
                return false;
            } else {
                return $name->name;
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

    if (!function_exists('forkliftInspection')) {
        function forkliftInspection()
        {
            return 'FORKLIFT-INS-000001';
        }
    }


    if (!function_exists('getGMInspectionStatus')) {
        function getGMInspectionStatus($id)
        {
            if ($id == GEMBA_WALK_INSPECTION_START) {
                return 'Gemba Walk Start';
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

    if (!function_exists('getObservationStatus')) {
        function getObservationStatus($id)
        {
            if ($id == OBSERVATION_APPROVED) {
                return 'Observation Approved';
            } else if ($id == OBSERVATION_REJECTED) {
                return 'Observation Rejected';
            } else if ($id == OBSERVATION_PENDING) {
                return 'Observation Pending';
            }

            return 'Unknown';
        }
    }
    if (!function_exists('getOHCStatus')) {
        function getOHCStatus($id)
        {
            if ($id == CLEANER_SUBMITTED_THE_CHECKLIST) {
                return 'Waiting For Nursing Officer Action';
            } else if ($id == NURSING_OFFICER_REJECTED) {
                return 'Nursing Officer Rejected';
            } else if ($id == NURSING_OFFICER_SUBMITTED_THE_CHECKLIST) {
                return 'Nursing Officer Submitted';
            }
            return 'Unknown';
        }
    }
    if (!function_exists('getPhysicalConditon')) {
        function getPhysicalConditon($id)
        {
            if ($id == GOOD) {
                $physical_condition = 'Good';
            } elseif ($id == FAIR) {
                $physical_condition = 'Fair';
            } elseif ($id == POOR) {
                $physical_condition = 'Poor';
            } else {
                $physical_condition = 'Unknown';
            }
            return $physical_condition;
        }
    }
    if (!function_exists('getCableCondition')) {
        function getCableCondition($id)
        {
            if ($id == GOOD) {
                $cable_condition = 'Good';
            } elseif ($id == FAIR) {
                $cable_condition = 'Fair';
            } elseif ($id == POOR) {
                $cable_condition = 'Poor';
            } else {
                $cable_condition = 'Unknown';
            }
            return $cable_condition;
        }
    }

    if (!function_exists('getLightCondition')) {
        function getLightCondition($id)
        {
            if ($id == GOOD) {
                return 'Good';
            } else if ($id == FAIR) {
                return 'Fair';
            } else if ($id == POOR) {
                return 'Poor';
            } else if ($id == DAMAGED) {
                return 'Damaged';
            }

            return 'Unknown';
        }
    }


    if (!function_exists('getObservationType')) {
        function getObservationType($type_id)
        {
            if ($type_id == 1) {
                return 'Unsafe Act';
            } elseif ($type_id == 2) {
                return 'Unsafe Condition';
            }
        }
    }

    if (!function_exists('getRiskCategory')) {
        function getRiskCategory($type_id)
        {
            if ($type_id == 1) {
                return 'Low';
            } elseif ($type_id == 2) {
                return 'High';
            } elseif ($type_id == 3) {
                return 'Moderate';
            }
        }
    }
    if (!function_exists('getLeadingName')) {
        function getLeadingName($type_id)
        {
            $data = LeadingLagging::where('type', LEADING)->where('id', $type_id)->first();
            if ($data) {
                return $data->value;
            }
        }
    }
    if (!function_exists('getLaggingName')) {
        function getLaggingName($type_id)
        {
            $data = LeadingLagging::where('type', LAGGING)->where('id', $type_id)->first();
            if ($data) {
                return $data->value;
            }
        }
    }

    // gemba Walk

    if (!function_exists('getGembaWalkStatus')) {
        function getGembaWalkStatus($type_id)
        {
            if ($type_id == 1) {
                return 'Open';
            } elseif ($type_id == 2) {
                return 'Closed';
            } else {
                return 'Unknown';
            }
        }
    }

    if (!function_exists('getIncidentStatus')) {
        function getIncidentStatus($type_id)
        {
            if ($type_id == 1) {
                return 'Open';
            } elseif ($type_id == 2) {
                return 'In-Progress';
            } elseif ($type_id == 3) {
                return 'closed';
            } else {
                return 'Unknown';
            }
        }
    }

    if (!function_exists('getRootCause')) {
        function getRootCause($type_id)
        {
            if ($type_id == 1) {
                return 'Why - Why Analysis';
            } elseif ($type_id == 2) {
                return 'Fish Born Diagram';
            } elseif ($type_id == 3) {
                return 'N/A';
            } else {
                return 'Unknown';
            }
        }
    }


    if (!function_exists('getGembaWalkLogStatus')) {

        function getGembaWalkLogStatus($id)
        {

            $user = GembaWalkStatus::select('*')
                ->where('id', $id)
                ->first();

            if ($user == null) {
                return '';
            } else {
                return $user->status_name;
            }
        }
    }

    if (!function_exists('getGembaWalkHazardName')) {

        function getGembaWalkHazardName($id)
        {

            $user = GembaWalkHazard::select('*')
                ->where('id', $id)
                ->first();

            if ($user == null) {
                return '';
            } else {
                return $user->hazard_name;
            }
        }
    }

    if (!function_exists('getGembaWalkClosingImage')) {
        function getGembaWalkClosingImage($id, $type)
        {
            $file = GembaWalkChecklistFile::where('gemba_walk_id', $id)
                ->where('file_type', $type)
                ->where('trash', 'NO')
                ->first();

            return $file->file_path ?? '';
        }
    }


    // Fire Inspection Hooter Sequence
    if (!function_exists('FireSequence')) {
        function FireSequence($type)
        {
            switch ($type) {
                case HOOTER_INSPECTION:
                    return 'HTR-000001';
                    break;
                case EMERGENCY_LIGHT_INSPECTION:
                    return 'EML-000001';
                    break;
                case MONTHLY_FIRE_PUMP:
                    return 'MFPI-000001';
                    break;

                case FIRE_EXTINGUISHER_INSPECTION:
                    return 'FEX-000001';
                    break;

                case ISOLATION_VALVE_INSPECTION:
                    return 'IVS-000001';
                    break;

                case FIRE_ALARM_INSPECTION:
                    return 'FAI-000001';
                    break;

                case SPRINKLAR_SYSTEM_INSPECTION:
                    return 'SSI-000001';
                    break;

                case FIRE_PA_SYSTEM_INSPECTION:
                    return 'PA-000001';
                    break;

                case CO_TYPE_FIRE_EXTINGUISHER_INSPECTION:
                    return 'CTFE-000001';
                    break;

                case CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION:
                    return 'CTFE-000001';
                    break;

                case HOSE_BOX_INSPECTION:
                    return 'HBI-000001';
                    break;

                case HOSE_REEL_INSPECTION:
                    return 'HRI-000001';
                    break;

                case CERTIFIED_FIRE_FIGHTER:
                    return 'SNO-000001';
                    break;
            }
        }
    }

    // Fire Inspection Folder Name
    if (!function_exists('GetTypeName')) {
        function GetTypeName($id)
        {
            switch ($id) {
                case HOOTER_INSPECTION:
                    return 'Hooter-Inspection';
                    break;
                case FIRE_PA_SYSTEM_INSPECTION:
                    return 'PA-System-Inspection';
                    break;

                default:
                    break;
            }
        }
    }

    // Get Department Name
    if (!function_exists('GetDeptName')) {
        function GetDeptName($id)
        {
            $data = Department::where('id', $id)->first();

            if ($data) {
                return $data->department_name;
            }
        }
    }


    // Get Audit Category Type

    if (!function_exists('getCategoryType')) {
        function getCategoryType($type_id)
        {
            if ($type_id == 1) {
                return 'FIRE';
            } elseif ($type_id == 2) {
                return 'HEALTH';
            } elseif ($type_id == 3) {
                return 'SAFETY';
            } elseif ($type_id == 4) {
                return 'MIS';
            } else {
                return 'Unknown';
            }
        }
    }

    // Get Audit Task
    if (!function_exists('getTaskName')) {

        function getTaskName($id)
        {
            $audit_task_name = Task::select('task_name')->where('id', $id)->where('trash', 'NO')->first();
            if ($audit_task_name === null) {
                return '';
            }

            return $audit_task_name->task_name;
        }
    }
    if (!function_exists('getDetectorName')) {
        function getDetectorName($id)
        {
            $data = DetectorType::where('id', $id)->first();

            if ($data) {
                return $data->detector_type;
            }
        }
    }

    // Get Fire Extinguisher Type Name
    if (!function_exists('getExtinguisherTypeName')) {
        function getExtinguisherTypeName($id)
        {
            $data = FireExtinguisherType::where('id', $id)->first();
            if ($data) {
                return $data->name;
            }
            return null;
        }
    }


    // Get Fire Signature
    if (!function_exists('GetFireSignature')) {
        function GetFireSignature($userid, $id, $type)
        {
            $name = FireSignatureUpload::where('emp_id', $userid)
                ->where('inspection_id', $id)
                ->where('type', $type)
                ->where('status', 1)
                ->where('trash', 'NO')
                ->first();

            if ($name === null) {
                $user = User::find($userid);
                return $user ? $user->signature_upload : null;
            }

            return $name->file_path;
        }
    }


    // Get Floor Stretcher Signature
    if (!function_exists('GetFSSignature')) {
        function GetFSSignature($userid, $id, $type)
        {
            switch ($type) {
                case OHC_TYPE_FLOOR_STRETCHER:
                    $name = FloorStretcherFiles::where('emp_id', $userid)->where('inspection_id', $id)->where('type', OHC_TYPE_FLOOR_STRETCHER)
                        ->where('status', 1)->where('trash', 'NO')->first();

                    if ($name == null) {
                        $name = User::where('id', $userid)->first();
                        if ($name == null) {
                            return null;
                        }
                        return $name->signature_upload;
                    } else {
                        return $name->file_path;
                    }
            }
        }
    }

    // Get Hose Type Name Name
    if (!function_exists('getHoseTypeName')) {
        function getHoseTypeName($id)
        {
            $data = HoseBoxType::where('id', $id)->first();
            if ($data) {
                return $data->name;
            }
            return null;
        }
    }


    if (!function_exists('getMonthlyInspectionEquipmentname')) {

        function getMonthlyInspectionEquipmentname($userid)
        {

            $equipment_name = MonthlyPhysicalEquipmentList::select('equipment_name')->where('id', $userid)->where('trash', 'NO')->first();

            if ($equipment_name == null) {
                return '';
            } else {
                return $equipment_name->equipment_name;
            }
        }
    }
}



if (!function_exists('getGivenSignatureBlock')) {
    function getGivenSignatureBlock($type, $sub_type, $id, $fallbackName = null)
    {
        $signature = (new OhcSignature())->getGivenBy($type, $sub_type, $id);

        if ($signature && !empty($signature->file_path)) {
            $imageUrl = admin_url($signature->file_path);
            $name = $fallbackName ?? 'Signed';

            return '<img src="' . $imageUrl . '" alt="Signature" style="height: 50px;"><br>' .
                '<span>' . e($name) . '</span>';
        }

        return 'N/A';
    }
}

if (!function_exists('getReceivedSignatureBlock')) {
    function getReceivedSignatureBlock($type, $sub_type, $id, $fallbackName = null)
    {
        $signature = (new OhcSignature())->getReceivedBy($type, $sub_type, $id);

        if ($signature && !empty($signature->file_path)) {
            $imageUrl = admin_url($signature->file_path);
            $name = $fallbackName ?? 'Signed';

            return '<img src="' . $imageUrl . '" alt="Signature" style="height: 50px;"><br>' .
                '<span>' . e($name) . '</span>';
        }

        return 'N/A';
    }
}
if (!function_exists('getMonthlyPhsyicalInspectionImages')) {
    function getMonthlyPhsyicalInspectionImages($id)
    {
        $data = MonthlyPhysicalInspectionFileUpload::where('inspection_id', $id)
            ->where('status', 1)
            ->where('trash', 'NO')
            ->get();

        if ($data) {
            $groupedData = $data->groupBy('equipment_id');
            return $groupedData;
        }

        return false;
    }
}

// Get Valve Type Name
if (!function_exists('getValveTypeName')) {
    function getValveTypeName($id)
    {
        $data = IsolatingValveType::where('id', $id)->first();
        if ($data) {
            return $data->name;
        }
        return null;
    }
}
if (!function_exists('getNFARating')) {
    function getNFARating($id)
    {
        $data = NFARating::where('id', $id)->first();
        if ($data) {
            return $data->nfa_rating;
        }
        return null;
    }
}
if (!function_exists('getChemicalName')) {
    function getChemicalName($id)
    {
        $data = Chemical::where('id', $id)->first();
        if ($data) {
            return $data->chemical;
        }
        return null;
    }
}


function GetLastMonthObservation($id)
{
    $data = SafetyWalkObservation::where('id', $id)->first();
    $current_month = $data->month;
    $current_year = $data->created_at->year;

    $month = Carbon::parse($current_month)->month;
    $last_month = $month - 1;

    if ($last_month == 0) {
        $last_month = 12;
        $current_year -= 1;
    }

    $last_month_name = Carbon::createFromFormat('m', $last_month)->format('F');

    $last_month_record = SafetyWalkObservation::where('month', $last_month_name)
        ->whereYear('created_at', $current_year)
        ->get();

    return $last_month_record;
}


function GetLastMonthDetails($lastMonthObservationDetails)
{
    if (count($lastMonthObservationDetails) > 0) {
        foreach ($lastMonthObservationDetails as $lastMonthObservationDetail) {
            $lastMonth[] = SafetyWalkObservationDetails::where('safety_walk_observation_id', $lastMonthObservationDetail->id)->get();
        }
        return $lastMonth;
    }
    return false;
}

// Inspection Count DashBoard Helper
if (!function_exists('InspectionCount')) {
    function InspectionCount($from_date, $to_date)
    {
        $group_wise_models = [
            'Environment' => [
                \App\Models\Inspection\Environment\AmbientAirMonitoring::class,
                \App\Models\Inspection\Environment\AmbientNoiseMonitoring::class,
                \App\Models\Inspection\Environment\DgSetStackEmissionMonitoring::class,
                \App\Models\Inspection\Environment\LuxMonitoring::class,
                \App\Models\Inspection\Environment\WorkNoiseMonitoring::class,
                \App\Models\Inspection\Environment\WorkZoneAirMonitoring::class,
            ],

            'Fire' => [
                \App\Models\Inspection\Fire\CartridgeTypeFireExtinguisher::class,
                \App\Models\Inspection\Fire\CoTypeFireExtinguisher::class,
                \App\Models\Inspection\Fire\DailyFireHouseInspection::class,
                \App\Models\Inspection\Fire\DetectorInspection::class,
                \App\Models\Inspection\Fire\EmergencyLightInspection::class,
                \App\Models\Inspection\Fire\FireAlarmInspection::class,
                \App\Models\Inspection\Fire\FireCheckListFollowUp::class,
                \App\Models\Inspection\Fire\FireExtinguisher::class,
                \App\Models\Inspection\Fire\FireMockDrillInspection::class,
                \App\Models\Inspection\Fire\FireModularInspection::class,
                \App\Models\Inspection\Fire\HooterInspection::class,
                \App\Models\Inspection\Fire\HoseBoxInspection::class,
                \App\Models\Inspection\Fire\HoseReelHoseInspection::class,
                \App\Models\Inspection\Fire\HydrantRiserInspection::class,
                \App\Models\Inspection\Fire\IsolationValve::class,
                \App\Models\Inspection\Fire\MonthlyFirePumpHouseInspection::class,
                \App\Models\Inspection\Fire\MonthlyPhysicalInspection::class,
                \App\Models\Inspection\Fire\PASystemInspection::class,
                \App\Models\Inspection\Fire\SandBucketInspection::class,
                \App\Models\Inspection\Fire\SprinklarSystemInspection::class,
                \App\Models\Inspection\Fire\Fire::class,
            ],

            'GembaWalk' => [
                \App\Models\Inspection\GembaWalk\GembaWalk::class
            ],

            'MSDS' => [
                \App\Models\Inspection\MSDS\MSDS::class
            ],

            'RRAA' => [
                \App\Models\Inspection\RRAA\RRAADetails::class,
            ],

            'Safety' => [
                App\Models\Inspection\Safety\FireSafetyEquipment::class,
                App\Models\Inspection\Safety\ForkLiftInspection::class,
                App\Models\Inspection\Safety\MonthlyEyeWashInspection::class,
                App\Models\Inspection\Safety\MonthlyForkLiftInspection::class,
                App\Models\Inspection\Safety\OHSPlantSummaryReport::class,
                App\Models\Inspection\Safety\SafetyGalleryInspection::class,
                App\Models\Inspection\Safety\SafetyWalkObservation::class,
            ],

            'Ohc' => [
                App\Models\Inspection\Ohc\CurrentNewExtCodeDialing::class,
                App\Models\Inspection\Ohc\DailyDepartmentFirstAidBox::class,
                App\Models\Inspection\Ohc\DailyVitalEquipment::class,
                App\Models\Inspection\Ohc\EmergencyBuyerFirstAidChecklist::class,
                App\Models\Inspection\Ohc\FirstAidBagChecklist::class,
                App\Models\Inspection\Ohc\FirstAiderList::class,
                App\Models\Inspection\Ohc\FirstAidMedicineInspection::class,
                App\Models\Inspection\Ohc\FirstAidRecordChecklist::class,
                App\Models\Inspection\Ohc\FloorStretcher::class,
                App\Models\Inspection\Ohc\HealthInstrumentCalibrationDetails::class,
                App\Models\Inspection\Ohc\MedicineRequistionSlipfdodetails::class,
                App\Models\Inspection\Ohc\MedicineRequistionSlipfloordetails::class,
                App\Models\Inspection\Ohc\MonthlyFirstAidbox::class,
                App\Models\Inspection\Ohc\MonthlyMedicineStore::class,
                App\Models\Inspection\Ohc\OccupationHealthInspection::class,
                App\Models\Inspection\Ohc\OHCHygieneCleaningChecklist::class,
                // App\Models\Inspection\Ohc\PhysicalHealthExamination::class,
                App\Models\Inspection\Ohc\SafetyPettyDetails::class,
                App\Models\Inspection\Ohc\WeeklyAmbulance::class,
                App\Models\Inspection\Ohc\WeeklyFirstAidBox::class,
            ]
        ];

        $applyDateFilter = function ($query) use ($from_date, $to_date) {

            if ($from_date && $to_date) {
                $query->whereBetween('created_at', [
                    DBdateformat($from_date),
                    DBdateformat($to_date) . ' 23:59:59'
                ]);
            } elseif ($from_date) {
                $query->where('created_at', '>=', DBdateformat($from_date));
            } elseif ($to_date) {
                $query->where('created_at', '<=', DBdateformat($to_date) . ' 23:59:59');
            }

            return $query;
        };

        $groupCounts = [];
        $grandTotal = 0;

        foreach ($group_wise_models as $groupName => $models) {
            $total = 0;

            foreach ($models as $modelClass) {
                $query = $modelClass::where('status', 1)->where('trash', 'NO');
                $count = $applyDateFilter($query)->count();
                $total += $count;
            }

            $groupCounts[$groupName] = $total;
            $grandTotal += $total;
        }

        return $groupCounts;
    }
}


// Get PTW Types
if (!function_exists('GetPTWTypes')) {
    function GetPTWTypes()
    {
        $data = TypeofWork::where('status', 1)->where('trash', 'NO')->get();

        $details = [];

        foreach ($data as $data) {
            $details[] = [
                'id' => $data->id,
                'work_name' => $data->work_name,
            ];
        }

        return $details;
    }
}
function getPPERequestChartData($form_date, $to_date, $company_id)
{
    $query = PpeRequest::selectRaw('unit_id, COUNT(*) as total')
        ->groupBy('unit_id');

    if ($company_id) {
        $companyId = decryptId($company_id);
        $query->where('company_id', $companyId);
    }

    if ($form_date && $to_date) {
        $query->whereBetween('created_at', [
            DBdateformat($form_date),
            DBdateformat($to_date) . ' 23:59:59'
        ]);
    } elseif ($form_date) {
        $query->where('created_at', '>=', DBdateformat($form_date));
    } elseif ($to_date) {
        $query->where('created_at', '<=', DBdateformat($to_date) . ' 23:59:59');
    }

    $rawData = $query->get();

    $labels = [];
    $series = [];
    $ids = [];

    foreach ($rawData as $row) {
        $labels[] = getUnitname($row->unit_id);  // For x-axis labels (names)
        $series[] = $row->total;                  // Data points
        $ids[] = $row->unit_id;                   // Unit IDs for selection
    }

    return [
        'labels' => $labels,
        'series' => $series,
        'ids' => $ids,  // Added ids array here
    ];
}


function getPTWAvgTimeChartData($form_date, $to_date, $company_id)
{
    $query = SafetyPermit::where('permit_status', STATUS_CLOSED);

    if ($company_id) {
        $companyId = decryptId($company_id);
        $query->where('company_id', $companyId);
    }

    if ($form_date && $to_date) {
        $query->whereBetween('created_at', [
            DBdateformat($form_date),
            DBdateformat($to_date) . ' 23:59:59'
        ]);
    } elseif ($form_date) {
        $query->where('created_at', '>=', DBdateformat($form_date));
    } elseif ($to_date) {
        $query->where('created_at', '<=', DBdateformat($to_date) . ' 23:59:59');
    }


    $rawData = $query->selectRaw('unit_id, AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as avg_duration')
        ->groupBy('unit_id')
        ->get();


    $labels = [];
    $series = [];

    foreach ($rawData as $row) {
        $labels[] = getUnitname($row->unit_id);
        $series[] = round($row->avg_duration / 60, 2); // convert seconds to minutes
    }

    return [
        'labels' => $labels,
        'series' => $series,
    ];
}


function getHazardUnitChartData($form_date, $to_date)
{
    $query = PpeRequest::selectRaw('unit_id, COUNT(*) as total')
        ->groupBy('unit_id');

    if (!empty($form_date)) {
        $query->whereDate('created_at', '>=', DBdateformat($form_date));
    }

    if (!empty($to_date)) {
        $query->whereDate('created_at', '<=', DBdateformat($to_date));
    }

    $rawData = $query->get();

    $labels = [];
    $series = [];

    foreach ($rawData as $row) {
        $labels[] = getUnitname($row->unit_id);
        $series[] = $row->total;
    }

    return [
        'labels' => $labels,
        'series' => $series,
    ];
}

function getPPEAvailabilityChartData($form_date, $to_date, $company_id)
{
    $query = PpeStockinventory::selectRaw('sub, SUM(quantity) as total_quantity')
        ->groupBy('sub');

    // if ($company_id) {
    //     $companyId = decryptId($company_id);
    //     $query->where('company_id', $companyId);
    // }

    if ($form_date && $to_date) {
        $query->whereBetween('created_at', [
            DBdateformat($form_date),
            DBdateformat($to_date) . ' 23:59:59'
        ]);
    } elseif ($form_date) {
        $query->where('created_at', '>=', DBdateformat($form_date));
    } elseif ($to_date) {
        $query->where('created_at', '<=', DBdateformat($to_date) . ' 23:59:59');
    }

    $rawData = $query->get();

    $labels = [];
    $series = [];

    foreach ($rawData as $row) {
        $labels[] = ($row->sub);
        $series[] = (int) $row->total_quantity;
    }

    return [
        'labels' => $labels,
        'series' => $series,
    ];
}

if (!function_exists('GetLeadingCount')) {
    function GetLeadingCount(
        $id,
        $company,
        $location_id,
        $unit_id,
        $department_id,
        $year,
        $month
    ) {

        $request = Request();
        $year = $year ?: Carbon::now()->format('Y');


        $query = HSCInputsLeading::where('leading_id', $id)
            ->leftJoin('kpi_hsc_inputs', 'kpi_hsc_inputs_leading.hsc_inputs_id', '=', 'kpi_hsc_inputs.id')
            ->select('kpi_hsc_inputs_leading.*', 'kpi_hsc_inputs.*')
            ->where('calendar_year', $year);

        if (!empty($company)) {
            $query->where('kpi_hsc_inputs.company_id', decryptId($company));
        }

        if (!empty($location_id)) {
            $query->where('kpi_hsc_inputs.location_id', decryptId($location_id));
        }

        if (!empty($unit_id)) {
            $query->where('kpi_hsc_inputs.unit_id', decryptId($unit_id));
        }

        if (!empty($department_id)) {
            $query->where('kpi_hsc_inputs.department_id', decryptId($department_id));
        }


        if (!empty($month)) {
            $query->where('kpi_hsc_inputs.month', $month);
        }

        return $query->sum('kpi_hsc_inputs_leading.value');
    }
}

if (!function_exists('GetLaggingCount')) {
    function GetLaggingCount(
        $id,
        $company,
        $location_id,
        $unit_id,
        $department_id,
        $year,
        $month
    ) {
        $year = $year ?: Carbon::now()->format('Y');
        $request = Request();


        $query = HSCInputsLagging::where('lagging_id', $id)
            ->leftJoin('kpi_hsc_inputs', 'kpi_hsc_inputs_lagging.hsc_inputs_id', '=', 'kpi_hsc_inputs.id')
            ->select('kpi_hsc_inputs_lagging.*', 'kpi_hsc_inputs.*')
            ->where('calendar_year', $year);

        if (!empty($company)) {
            $query->where('kpi_hsc_inputs.company_id', decryptId($company));
        }

        if (!empty($location_id)) {
            $query->where('kpi_hsc_inputs.location_id', decryptId($location_id));
        }

        if (!empty($unit_id)) {
            $query->where('kpi_hsc_inputs.unit_id', decryptId($unit_id));
        }

        if (!empty($department_id)) {
            $query->where('kpi_hsc_inputs.department_id', decryptId($department_id));
        }


        if (!empty($month)) {
            $query->where('kpi_hsc_inputs.month', $month);
        }

        return $query->sum('kpi_hsc_inputs_lagging.value');
    }
}


if (!function_exists('GetFirstAidCount')) {
    function GetFirstAidCount(
        $company,
        $location_id,
        $unit_id,
        $department_id,
        $year,
        $month
    ) {
        $year = $year ?: Carbon::now();
        $request = Request();

        $query = FirstAid::select('masters_employee.*', 'ohc_opd_first_aid.*', 'ohc_opd_first_aid.created_at as first_aid_created_at', 'ohc_opd_first_aid.updated_at as first_aid_updated_at')
            ->leftJoin('masters_employee', 'ohc_opd_first_aid.emp_id', '=', 'masters_employee.emp_id')
            ->whereYear('ohc_opd_first_aid.created_at', $year);


        if (!empty($company)) {
            $query->where('masters_employee.company', decryptId($company));
        }

        if (!empty($location_id)) {
            $query->where('masters_employee.location', decryptId($location_id));
        }

        if (!empty($unit_id)) {
            $query->where('masters_employee.unit', decryptId($unit_id));
        }

        if (!empty($department_id)) {
            $query->where('masters_employee.department', decryptId($department_id));
        }


        if (!empty($month)) {
            $query->whereMonth('first_aid_created_at', $month);
        }
        return $query->count();
    }
}


if (!function_exists('GetRoadSideFirstAid')) {
    function GetRoadSideFirstAid(
        $company,
        $location_id,
        $unit_id,
        $department_id,
        $year,
        $month
    ) {
        $year = $year ?: Carbon::now();
        $request = Request();

        $query = RoadsideFirstAid::select('masters_employee.*', 'ohc_opd_roadside_first_aid.*', 'ohc_opd_roadside_first_aid.created_at as first_aid_created_at', 'ohc_opd_roadside_first_aid.updated_at as first_aid_updated_at')
            ->leftJoin('masters_employee', 'ohc_opd_roadside_first_aid.created_by', '=', 'masters_employee.emp_id')
            ->whereYear('ohc_opd_roadside_first_aid.created_at', $year);

        if (!empty($company)) {
            $query->where('masters_employee.company', decryptId($company));
        }

        if (!empty($location_id)) {
            $query->where('masters_employee.location', decryptId($location_id));
        }

        if (!empty($unit_id)) {
            $query->where('masters_employee.unit', decryptId($unit_id));
        }

        if (!empty($department_id)) {
            $query->where('masters_employee.department', decryptId($department_id));
        }


        if (!empty($month)) {
            $query->whereMonth('first_aid_created_at', $month);
        }
        return $query->count();
    }
}
if (!function_exists('GetPrescribeToPatient')) {
    function GetPrescribeToPatient(
        $company,
        $location_id,
        $unit_id,
        $department_id,
        $year,
        $month
    ) {
        $year = $year ?: Carbon::now();
        $request = Request();

        $query = PrescribetoPatient::whereYear('ohc_management_opd_patient.created_at', $year);

        if (!empty($company)) {
            $query->where('ohc_management_opd_patient.company_name', decryptId($company));
        }

        if (!empty($unit_id)) {
            $query->where('ohc_management_opd_patient.unit_id', decryptId($unit_id));
        }



        if (!empty($department_id)) {
            $query->where('ohc_management_opd_patient.department_id', decryptId($department_id));
        }

        if (!empty($month)) {
            $query->whereMonth('ohc_management_opd_patient.created_at', $month);
        }
        return $query->count();
    }
}



if (!function_exists('GetImsInitialIncidentReport')) {
    function GetImsInitialIncidentReport(
        $type,
        $company,
        $location_id,
        $unit_id,
        $department_id,
        $year,
        $month
    ) {
        $year = $year ?: Carbon::now();
        $request = Request();

        $query = InitialIncident::whereYear('ims_initial_incident.created_at', $year)->where('iir_type', $type);

        if (!empty($company)) {
            $query->where('ims_initial_incident.company_id', decryptId($company));
        }

        if (!empty($location_id)) {
            $query->where('masters_employee.location_id', decryptId($location_id));
        }

        if (!empty($unit_id)) {
            $query->where('ims_initial_incident.unit_id', decryptId($unit_id));
        }

        if (!empty($department_id)) {
            $query->where('ims_initial_incident.department', decryptId($department_id));
        }

        if (!empty($month)) {
            $query->whereMonth('ims_initial_incident.created_at', $month);
        }
        return $query->count();
    }
}


// API Helpers
if (!function_exists('GetStatusValue')) {
    function GetStatusValue($id)
    {
        switch ($id) {
            case WAITING_FOR_EHS_OFFICER_VERIFICATION:
                $text = "Waiting For EHS Officer Verification";
                break;
            case WAITING_FOR_CAPA_ACTION:
                $text = "Waiting For CAPA Action";
                break;
            case WAITING_FOR_CAPA_VERIFICATION:
                $text = "Waiting For CAPA Verification";
                break;
            case WAITING_FOR_L1_VERIFICATION:
                $text = "Waiting For Level-1 Manager Verification";
                break;
            case WAITING_FOR_L2_VERIFICATION:
                $text = "Waiting For Level-2 Manager Verification";
                break;
            case INSPECTION_APPROVED:
                $text = "CLOSED";
                break;
            case L2_MANAGER_REJECTED:
                $text = "LEVEL 2 OFFICER REJECTED - WAITING FOR CAPA ACTION";
                break;
            case L1_MANAGER_REJECTED:
                $text = "LEVEL 1 OFFICER REJECTED - WAITING FOR CAPA ACTION";
                break;
            case EHS_OFFICER_REJECTED:
                $text = "EHS OFFICER REJECTED - WAITING FOR CAPA ACTION";
                break;
            default:
                $text = "Unknown";
        }
        return $text;
    }
}

// Get Condition
if (!function_exists('GetConditionName')) {
    function GetConditionName($value)
    {
        switch ($value) {
            case GOOD:
                return 'Good';
            case FAIR:
                return 'Fair';
            default:
                return 'Poor';
        }
    }
}

// Get Pass or Fail
if (!function_exists('GetPassOrFail')) {
    function GetPassOrFail($value)
    {
        switch ($value) {
            case PASS:
                return 'Pass';
            case FAIL:
                return 'Fail';
            default:
                return 'N/a';
        }
    }
}

// Get Operational or Non Operational
if (!function_exists('GetOPOrNonOP')) {
    function GetOPOrNonOP($value)
    {
        switch ($value) {
            case OPERATIONAL:
                return 'Operational';
            default:
                return 'Non-Operational';
        }
    }
}
