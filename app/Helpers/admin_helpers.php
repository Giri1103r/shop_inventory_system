<?php

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;


use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Notification;
use App\Models\Master\Location;

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

                $string .= '<a href="#'.$parenetlinkid.'" data-bs-toggle="collapse">' . $value["name"] . '<span class="menu-arrow"></span></a>';

                $parentdetails = array(
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'menu_id' => $parenetlinkid
                );
                $string .= get_admin_menuchild($menu_array[$value['id']], $menu_array, $parentdetails);
                $string .= '</li>';
            } else {
                $string .= ' <li><a href="' . admin_url($value["link"]) . ';">' . $value["name"] . '</a></li>';

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

            case 'employee':
                $count = 0;
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
