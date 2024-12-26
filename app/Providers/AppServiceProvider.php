<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Models\Notification;
use App\Models\NotificationLog;
use App\Models\Language;
use App\Models\Master\UserRole;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(255);
        Paginator::useBootstrap();

        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        defined('DAYS') or define('DAYS', $days);

        $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        defined('SHORT_DAYS') or define('SHORT_DAYS', $days);


        $days = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        defined('MONTH') or define('MONTH', $days);


        $days =  ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        defined('SHORT_MONTH') or define('SHORT_MONTH', $days);

        defined('ADD_DATE') or define('ADD_DATE', 1);
        defined('ADD_MONTH') or define('ADD_MONTH', 2);
        defined('ADD_YEAR') or define('ADD_YEAR', 3);


        defined('MENU') or define('MENU', 'template_left_menu');

        defined('ROLE_SUPERADMIN') or define('ROLE_SUPERADMIN', 1);
        defined('ROLE_ADMIN') or define('ROLE_ADMIN', 2);
        defined('ROLE_EHS_OFFICER') or define('ROLE_EHS_OFFICER', 3);
        defined('ROLE_HOD') or define('ROLE_HOD', 4);
        defined('ROLE_EHS_HEAD') or define('ROLE_EHS_HEAD', 6);
        defined('ROLE_PLANT_HEAD') or define('ROLE_PLANT_HEAD', 7);
        defined('ROLE_STORE_MANAGER') or define('ROLE_STORE_MANAGER', 5);
        defined('ROLE_TRAINER') or define('ROLE_TRAINER', 8);
        defined('ROLE_USER') or define('ROLE_USER', 9);


        defined('STATUS_HOD_APPROVAL_PENDING') or define('STATUS_HOD_APPROVAL_PENDING', 1);
        defined('STATUS_HOD_APPROVED') or define('STATUS_HOD_APPROVED', 2);
        defined('STATUS_HOD_REJECTED') or define('STATUS_HOD_REJECTED', 3);
        defined('STATUS_EHS_APPROVAL_PENDING') or define('STATUS_EHS_APPROVAL_PENDING', 4);
        defined('STATUS_EHS_APPROVED') or define('STATUS_EHS_APPROVED', 5);
        defined('STATUS_EHS_REJECTED') or define('STATUS_EHS_REJECTED', 6);
        defined('STATUS_USER_APPLIED') or define('STATUS_USER_APPLIED', 7);


        defined('TYPE_PPE_REQUEST') or define('TYPE_PPE_REQUEST', 1);
        defined('TYPE_PPE_EXEMPTION') or define('TYPE_PPE_EXEMPTION', 2);

        defined('CHEMICAL_DEPARTMENT') or define('CHEMICAL_DEPARTMENT', 53);


        // Safety Permit
        defined('STATUS_EHS_VERIFICATION_PENDING') or define('STATUS_EHS_VERIFICATION_PENDING', 1);
        defined('STATUS_EHS_APPROVE_PENDING') or define('STATUS_EHS_APPROVE_PENDING', 2);
        defined('STATUS_EHS_HOLD') or define('STATUS_EHS_HOLD', 3);
        defined('STATUS_EHS_DECLINE') or define('STATUS_EHS_DECLINE', 4);
        defined('STATUS_EHS_REASSIGN') or define('STATUS_EHS_REASSIGN', 5);
        defined('STATUS_PLANT_HEAD_PENDING') or define('STATUS_PLANT_HEAD_PENDING', 6);
        defined('STATUS_PLANT_HEAD_APPROVED') or define('STATUS_PLANT_HEAD_APPROVED', 7);
        defined('STATUS_EHS_RESUME') or define('STATUS_EHS_RESUME', 8);
        defined('STATUS_PERMIT_EXPIRED') or define('STATUS_PERMIT_EXPIRED', 9);
        defined('STATUS_PERMIT_EXTENDED') or define('STATUS_PERMIT_EXTENDED', 10);
        defined('STATUS_PERMIT_EXTENDED_APPROVAL') or define('STATUS_PERMIT_EXTENDED_APPROVAL', 11);
        defined('STATUS_PERMIT_EXTENDED_REJECTED') or define('STATUS_PERMIT_EXTENDED_REJECTED', 12);
        defined('STATUS_PLANTHEAD_REJECTED') or define('STATUS_PLANTHEAD_REJECTED', 13);



        View::composer('*', function ($view) {

            /**
             * Left Menu Function
             */

            $mymenu = range(1, 150);

            if (Auth::check()) {

                if (CheckUserRole(ROLE_SUPERADMIN)) {

                    $roleIds = string_to_array(Auth::user()->role);
                    $userRoles =  UserRole::whereIn('id', $roleIds)->get();

                    $mymenu = [];
                    foreach ($userRoles as $role) {

                        $permissionArray = ($role->role_permission == "" || $role->role_permission == null) ? [] : string_to_array($role->role_permission);

                        $mymenu = array_unique(array_merge($mymenu, $permissionArray));
                    }

                    $mymenu = range(1, 150);
                } else {

                    $roleIds = string_to_array(Auth::user()->role);
                    $userRoles =  UserRole::whereIn('id', $roleIds)->get();

                    $mymenu = [];
                    foreach ($userRoles as $role) {
                        $permissionArray = ($role->role_permission == "" || $role->role_permission == null) ? [] : string_to_array($role->role_permission);

                        $mymenu =   array_unique(array_merge($mymenu, $permissionArray));
                    }
                }
            }

            $menu = DB::table(MENU)
                ->select('id', 'name', 'namekey', 'link', 'icon', 'parent_id', 'is_parent', 'is_module', 'sort_order')
                ->where('status', 1)
                ->where('trash', 'NO')
                ->whereIn('id', $mymenu)
                ->orderBy('parent_id', 'asc')
                ->orderBy('sort_order', 'asc')
                ->get();

            $menu_lsit = get_admin_menu($menu);

            View::share('left_menu', $menu_lsit);

            if (session()->has('locale')) {
                $langid = session()->get('locale');
            } else {
                $langid = env('APP_LOCALE');
            }

            $currentlanguage = Language::where('short_name', $langid)->first();
            View::share('currentlanguage', $currentlanguage);


            $languageDetails = Language::orderBy('sort_order', 'ASC')->get();
            View::share('languageDetails', $languageDetails);

            if (Auth::check()) {
                $theme = Auth::user()->theme;
            } else {
                $theme = 'light-skin';
            }

            if ($theme  == '' ||  $theme  == null ||  $theme  == 'light-skin') {
                $themetype = 'light-skin';
            } else {
                $themetype = 'dark-skin';
            }

            View::share('themetype', $themetype);


            /**
             * Notification Function
             */

            $notification_list_array = Notification::select('*')
                ->whereRaw("FIND_IN_SET(?, assigned_user) > 0", [Auth::id()]);

            $notification_list_array = $notification_list_array->orderBy('id', 'DESC')->paginate(10);
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
                $data['icon'] = $message->icon;
                $data['web_link'] = $listdata->web_link;
                $data['time'] = timeago($listdata->created_at);
                $data['created_at'] = Displaydatetimeformat($listdata->created_at);
                $data['read_status'] = $viewed_status;

                $data_array[] = $data;
            }
            $unreadCount = $notification_list['total'] - $userReadCount;
            View::share('unreadCount', $unreadCount);
            View::share('notification_list', $data_array);
        });
    }
}
