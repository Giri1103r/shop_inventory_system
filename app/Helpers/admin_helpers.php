<?php

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

use App\Models\Master\Company;
use App\Models\Master\Location;
use App\Models\Master\Unit;
use App\Models\Master\Department;
use App\Models\Master\Topic;
use App\Models\Master\UserRole;
use App\Models\Master\Employee;
use App\Models\Master\Work;
use App\Models\Permit\SafetyPermit;

use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Notification;
use App\Models\OhcManagement\UserMedicineRequisition;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\WebPushConfig;



/*
 * Menu bar start
 */

if (!function_exists('get_admin_menu')) {

    function get_admin_menu($menu)
    {

        $menu_array = array();
        $i = 0;

        foreach ($menu as $key => $value) {

            $id = $value->id;
            $link_name  =  __('leftmenu.menu_' . $id);

            $menu_array[$value->parent_id][$i]['id'] = $value->id;
            $menu_array[$value->parent_id][$i]['name'] =  $value->name;
            $menu_array[$value->parent_id][$i]['link'] = $value->link;
            $menu_array[$value->parent_id][$i]['icon'] = $value->icon;
            $menu_array[$value->parent_id][$i]['is_parent'] = $value->is_parent;
            $menu_array[$value->parent_id][$i]['parent_id'] = $value->parent_id;
            $menu_array[$value->parent_id][$i]['sort_order'] = $value->sort_order;
            $i++;
        }

        $html = "";


        $html .= '<ul id="side-menu">';

        if (count($menu_array) > 0) {
            foreach ($menu_array[0] as $key => $value) {

                $target = "_self";
                $href = "#";

                if ($value['is_parent'] != 0) {

                    $href = "javascript:void(0)";
                    $link_name = $value['name'];


                    $link_icon = $value['icon'];

                    $parenetlinkid = "link_" . encryptId($value['id']);

                    $html .= '<li>';
                    $html .= '<a href="#' . $parenetlinkid . '" data-bs-toggle="collapse"> <i class="' . $link_icon . '"></i> <span> ' . $link_name . ' </span> <span class="menu-arrow"></span> </a>';

                    if ($value['is_parent'] == '1' && isset($menu_array[$value['id']])) {
                        $parentdetails = array(
                            'id' => $value['id'],
                            'name' => $value['name'],
                            'menu_id' => $parenetlinkid
                        );
                        $html .= get_admin_menuchild($menu_array[$value['id']], $menu_array, $parentdetails);
                    }

                    $html .= '</li>';
                } else {

                    $href = admin_url($value['link']);
                    $link_name = $value['name'];
                    $link_icon = $value['icon'];

                    $html .= '<li><a href="' . $href . '"><i class="' . $link_icon . '"></i><span> ' . $link_name . ' </span></a></li>';
                }
            }
        }
        $html .= '</ul>';
        return $html;
    }
}

if (!function_exists('get_admin_menuchild')) {

    function get_admin_menuchild($menu, $menu_array, $parent)
    {

        $id = $parent['id'];
        $string = "";

        $string .= '<div class="collapse" id="' . $parent['menu_id'] . '">';
        $string .= ' <ul class="nav-second-level">';

        foreach ($menu as $key => $value) {

            $target = "_self";
            $href = "#";

            if ($value['is_parent'] == '1' && isset($menu_array[$value['id']])) {

                $string .= ' <li>';

                $parenetlinkid = "link_" . encryptId($value['id']);

                $string .= '<a href="#' . $parenetlinkid . '" data-bs-toggle="collapse">' . $value["name"] . '<span class="menu-arrow"></span></a>';

                $parentdetails = array(
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'menu_id' => $parenetlinkid
                );
                $string .= get_admin_menuchild($menu_array[$value['id']], $menu_array, $parentdetails);
                $string .= '</li>';
            } else {
                $string .= ' <li><a href="' . admin_url($value["link"]) . '">' . $value["name"] . '</a></li>';
            }
        }
        $string .= '</ul>';
        $string .= '</div>';

        return $string;
    }
}

/*
 * Partner ID generate start
 */
if (!function_exists('getsequence')) {

    function getsequence($type)
    {
        switch ($type) {

            case 'user':
                $sequence = Str::random(5);
                break;
            case 'company':
                $count = Company::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'COMPANY-' . getautogen($count);
                break;
            case 'location':
                $count = Location::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'LOC-' . getautogen($count);
                break;
            case 'unit':
                $count = Unit::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'Unit-' . getautogen($count);
                break;
            case 'department':
                $count = Department::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'DEP-' . getautogen($count);
                break;
            case 'role':
                $count = UserRole::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'ROLE-' . getautogen($count);
                break;
            case 'topic':
                $count = Topic::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'TOPIC-' . getautogen($count);
                break;
            case 'safetypermit':
                $count = SafetyPermit::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'ORD/' . getautogen($count);
                break;
            case 'requistion':
                $count = UserMedicineRequisition::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'REQ-' . getautogen($count);
                break;
            default:
                $sequence = Str::random(5);
                break;
        }
        return $sequence;
    }
}

if (!function_exists('gettotalCount')) {

    function gettotalCount($type)
    {

        switch ($type) {

            case 'company':
                $count = Company::count();
                break;
            case 'location':
                $count = Location::count();
                break;
            case 'unit':
                $count = Unit::count();
                break;
            case 'department':
                $count = Department::count();
                break;
            case 'employee':
                $count = Employee::count();
                break;
            case 'work':
                $count = Work::count();
                break;
            default:
                $count = 0;
                break;
        }

        return $count;
    }
}

if (!function_exists('getYearArray')) {
    function getYearArray($startYear = null)
    {
        $currentYear = date('Y');
        $startYear = $startYear ?? $currentYear;
        return range($startYear, 2023);
    }
}

if (!function_exists('getMonthsArray')) {
    function getMonthsArray()
    {
        return [
            ['value' => 'January', 'name' => 'January'],
            ['value' => 'February', 'name' => 'February'],
            ['value' => 'March', 'name' => 'March'],
            ['value' => 'April', 'name' => 'April'],
            ['value' => 'May', 'name' => 'May'],
            ['value' => 'June', 'name' => 'June'],
            ['value' => 'July', 'name' => 'July'],
            ['value' => 'August', 'name' => 'August'],
            ['value' => 'September', 'name' => 'September'],
            ['value' => 'October', 'name' => 'October'],
            ['value' => 'November', 'name' => 'November'],
            ['value' => 'December', 'name' => 'December'],
        ];
    }
}


/*
 * Menu bar start
 */

if (!function_exists('getRoleMenu')) {
    function getRoleMenu($menu)
    {
        $menu_array = array();
        $i = 0;

        foreach ($menu as $key => $value) {
            $menu_array[$value->parent_id][$i]['id'] = $value->id;
            $menu_array[$value->parent_id][$i]['name'] = $value->name;
            $menu_array[$value->parent_id][$i]['link'] = $value->link;
            $menu_array[$value->parent_id][$i]['icon'] = $value->icon;
            $menu_array[$value->parent_id][$i]['is_parent'] = $value->is_parent;
            $menu_array[$value->parent_id][$i]['parent_id'] = $value->parent_id;
            $menu_array[$value->parent_id][$i]['permission'] = explode(',', trim($value->permission, '[{}]'));
            $menu_array[$value->parent_id][$i]['sort_order'] = $value->sort_order;
            $i++;
        }
        $html = "";
        $html .= '<tbody >';


        if (count($menu_array) > 0) {
            foreach ($menu_array[0] as $key => $value) {
                $class = $value['parent_id'];
                if ($value['is_parent'] != 0) {
                    // $html .= '<tr> <td>' . $value['name'] . '<span style="padding: 41px;"> <input type="checkbox" name="menu_' . $value['id'] . '_all" class="parent " data-id="' . encryptId($value['id']) . '" data-parentid="" id ="checkbox_' . encryptId($value['id']) . '"> <label for="checkbox_' . encryptId($value['id']) . '"> Select All </label> </span></td><td colspan="5"></td> </tr>';

                    $html .= '<tr> <td>' . $value['name'] . '  <span style="padding: 41px;"> <input type="checkbox" name="menu_' . $value['id'] . '_all" class="parent" data-id="' . encryptId($value['id']) . '" data-parentid="' . encryptId($value['parent_id']) . '" id ="checkbox_' . encryptId($value['id']) . '"> <label for="checkbox_' . encryptId($value['id']) . '"> Select All </label></span> </td><td colspan="5"></td> </tr>';
                    if ($value['is_parent'] == '1' && isset($menu_array[$value['id']])) {
                        $html .= getRoleMenuChild($menu_array[$value['id']], $menu_array, 0);
                    }
                } else {
                    $permissions = $value['permission'];
                    $html .= '<tr>
                            <td>' . $value['name'] . '</td></tr>
                  <tr>';

                    foreach ($permissions as $perm) {
                        $html .= '<td><div style="display: inline-flex;justify-content: space-evenly;width: 7%;">  <input type="checkbox" name="menu_' . $value['id'] . '_' . $perm . '" id="sub_' . $value['id'] . '" class="" data-id="" data-parentid="" > ' . ucfirst($perm) . '<td>';
                    }

                    $html .= '</tr>';
                }
            }
        }

        $html .= '</tbody>';
        return $html;
    }
}

if (!function_exists('getRoleMenuChild')) {

    function getRoleMenuChild($menu, $menu_array, $i = 0, $clsss = "")
    {
        $i = $i + 2;
        $string = '';

        foreach ($menu as $key => $value) {

            if ($value['is_parent'] == '1' && isset($menu_array[$value['id']])) {
                $clsss = encryptId($value['parent_id']);

                $string .= '<tr> <td style="padding:10px;padding-left:' . $i . 'rem;">' . $value['name'] . '  <span style="padding: 41px;"> <input type="checkbox" name="menu_' . $value['id'] . '_all" class="parent ' . $clsss . '" data-id="' . encryptId($value['id']) . '" data-parentid="' . encryptId($value['parent_id']) . '" id ="checkbox_' . encryptId($value['id']) . '"> <label for="checkbox_' . encryptId($value['id']) . '"> Select All </label></span> </td><td colspan="5"></td> </tr>';

                $string .= getRoleMenuChild($menu_array[$value['id']], $menu_array, $i, $clsss);
            } else {
                $permissions = $value['permission'];
                $string .= '<tr>
                            <td style="padding:10px;padding-left:' . $i . 'rem;">' . $value['name'] . '</td></tr>';
                $string .= '<tr><td colspan="5"><div style="display: inline-flex;justify-content: space-evenly;width: 100%;">';
                foreach ($permissions as $perm) {
                    $string .= '<div><input type="checkbox" name="menu_' . $value['id'] . '_' . $perm . '" class="child ' . $clsss . " " . encryptId($value['parent_id']) . '" data-id="' . encryptId($value['parent_id']) . '" data-parentid=""> ' . ucfirst($perm) . '
                             </div>';
                }
                $string .= '</div> </td></tr>';
            }
        }

        return $string;
    }
}
