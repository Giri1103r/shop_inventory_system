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
        defined('ROLE_VISE_PRESIDENT') or define('ROLE_VISE_PRESIDENT', 10);
        defined('ROLE_INFRA_LEAD') or define('ROLE_INFRA_LEAD', 11);
        defined('ROLE_WORKER_REQUEST') or define('ROLE_WORKER_REQUEST', 12);
        defined('ROLE_PARAMEDICS') or define('ROLE_PARAMEDICS', 13);
        defined('ROLE_L1_EHS_OFFCIER') or define('ROLE_L1_EHS_OFFCIER', 14);
        defined('ROLE_CERTIFIED_FIRST_AIDER') or define('ROLE_CERTIFIED_FIRST_AIDER', 15);
        defined('ROLE_DOCTOR') or define('ROLE_DOCTOR', 16);
        defined('ROLE_NURSE') or define('ROLE_NURSE', 17);
        defined('ROLE_FIRE_ASSOCIATES') or define('ROLE_FIRE_ASSOCIATES', 18);
        defined('ROLE_L1_MANAGER') or define('ROLE_L1_MANAGER', 19);
        defined('ROLE_L2_MANAGER') or define('ROLE_L2_MANAGER', 20);
        defined('ROLE_FLOOR_MANAGER') or define('ROLE_FLOOR_MANAGER', 21);
        defined('ROLE_UNIT_HEAD') or define('ROLE_UNIT_HEAD', 22);
        defined('ROLE_SAFETY_OFFICER') or define('ROLE_SAFETY_OFFICER', 23);
        defined('ROLE_MEDICAL_ASSISTANT') or define('ROLE_MEDICAL_ASSISTANT', 24);
        defined('ROLE_NURSING_OFFICER') or define('ROLE_NURSING_OFFICER', 25);
        defined('ROLE_CLEANER') or define('ROLE_CLEANER', 26);
        defined('ROLE_INSPECTION_CREATOR') or define('ROLE_INSPECTION_CREATOR', 27);
        defined('ROLE_DASHBOARD_VIEWER') or define('ROLE_DASHBOARD_VIEWER', 28);

      

        View::composer('*', function ($view) {

            /**
             * Left Menu Function
             */

            $mymenu = range(1, 300);

            if (Auth::check()) {

                if (CheckUserRole(ROLE_SUPERADMIN)) {

                    $roleIds = string_to_array(Auth::user()->role);
                    $userRoles =  UserRole::whereIn('id', $roleIds)->get();

                    $mymenu = [];
                    foreach ($userRoles as $role) {

                        $permissionArray = ($role->role_permission == "" || $role->role_permission == null) ? [] : string_to_array($role->role_permission);

                        $mymenu = array_unique(array_merge($mymenu, $permissionArray));
                    }

                    $mymenu = range(1, 300);
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
