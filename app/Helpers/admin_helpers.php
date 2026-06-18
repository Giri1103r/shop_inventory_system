<?php

use App\Models\Master\Category;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Master\Work;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\Master\Company;
use App\Models\Master\Employee;
use App\Models\Master\Location;

use App\Models\Master\UserRole;
use App\Models\Master\Department;
use App\Models\Master\PpeRequest;
use Illuminate\Support\Facades\DB;


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
            case 'category':
                $count = Category::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'CAT-' . getautogen($count);
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
                $count = Company::where('status', 1)->count();
                break;
            case 'location':
                $count = Location::where('status', 1)->count();
                break;
            case 'unit':
                $count = Unit::where('status', 1)->count();
                break;
            case 'department':
                $count = Department::where('status', 1)->count();
                break;
            case 'employee':
                $count = Employee::where('status', 1)->count();
                break;
            case 'work':
                $count = Work::where('status', 1)->count();
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
            $menu_array[$value->parent_id][$i]['sort_order'] = $value->sort_order;
            $i++;
        }
        $html = "";


        $html .= '<tbody>';

        if (count($menu_array) > 0) {
            foreach ($menu_array[0] as $key => $value) {

                $class = $value['parent_id'];
                if ($value['is_parent'] != 0) {

                    $html .= '<tr> <td>' . $value['name'] . '<span style="float:right;"> <input type="checkbox" name="menu_' . $value['id'] . '_all" class="parent " data-id="' . encryptId($value['id']) . '" data-parentid="" id ="checkbox_' . encryptId($value['id']) . '"> <label for="checkbox_' . encryptId($value['id']) . '"> Select All </label> </span></td><td colspan="5"></td> </tr>';

                    if ($value['is_parent'] == '1' && isset($menu_array[$value['id']])) {

                        $html .= getRoleMenuChild($menu_array[$value['id']], $menu_array, 0);
                    }
                } else {

                    $html .= '<tr>
                    <td>' . $value['name'] . ' </td>
                    <td><input type="checkbox" name="menu_' . $value['id'] . '_add" class="" data-id="" data-parentid="" ></td>
                    <td><input type="checkbox" name="menu_' . $value['id'] . '_edit" class="" data-id="" data-parentid="" ></td>
                    <td><input type="checkbox" name="menu_' . $value['id'] . '_view" class="" data-id="" data-parentid="" ></td>
                    <td><input type="checkbox" name="menu_' . $value['id'] . '_delete" class="" data-id="" data-parentid="" ></td>
                    <td><input type="checkbox" name="menu_' . $value['id'] . '_export" class="" data-id="" data-parentid="" ></td>
                    </tr>';
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

                $string .= '<tr> <td style="padding:10px;padding-left:' . $i . 'rem;">' . $value['name'] . '  <span style="float:right;"> <input type="checkbox" name="menu_' . $value['id'] . '_all" class="parent ' . $clsss . '" data-id="' . encryptId($value['id']) . '" data-parentid="' . encryptId($value['parent_id']) . '" id ="checkbox_' . encryptId($value['id']) . '"> <label for="checkbox_' . encryptId($value['id']) . '"> Select All </label></span> </td><td colspan="5"></td> </tr>';



                $string .= getRoleMenuChild($menu_array[$value['id']], $menu_array, $i, $clsss);
            } else {
                $string .= '<tr>
                    <td style="padding:10px;padding-left:' . $i . 'rem;">' . $value['name'] . '</td>
                    <td><input type="checkbox" name="menu_' . $value['id'] . '_add"  class="child ' . $clsss . " " . encryptId($value['parent_id']) . '" data-id="" data-parentid="" ></td>
                    <td><input type="checkbox" name="menu_' . $value['id'] . '_edit" class="child ' . $clsss . " " . encryptId($value['parent_id']) . '" data-id="" data-parentid="" ></td>
                    <td><input type="checkbox" name="menu_' . $value['id'] . '_view" class="child ' . $clsss . " " . encryptId($value['parent_id']) . '" data-id="" data-parentid="" ></td>
                    <td><input type="checkbox" name="menu_' . $value['id'] . '_delete" class="child ' . $clsss . " " . encryptId($value['parent_id']) . '" data-id="" data-parentid="" ></td>
                    <td><input type="checkbox" name="menu_' . $value['id'] . '_export" class="child ' . $clsss . " " . encryptId($value['parent_id']) . '" data-id="" data-parent></td>
                </tr>';
            }
        }


        return $string;
    }
}
