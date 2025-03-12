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
        defined('ROLE_WORKER_REQUEST') or define('ROLE_WORKER_REQUEST', 12);
        defined('ROLE_PARAMEDICS') or define('ROLE_PARAMEDICS', 13);
        defined('ROLE_L1_EHS_OFFCIER') or define('ROLE_L1_EHS_OFFCIER', 14);
        defined('ROLE_CERTIFIED_FIRST_AIDER') or define('ROLE_CERTIFIED_FIRST_AIDER', 15);
        defined('ROLE_DOCTOR') or define('ROLE_DOCTOR', 16);
        defined('ROLE_NURSE') or define('ROLE_NURSE', 17);


        defined('NEW_TRAINING_SCHEDULE') or define('NEW_TRAINING_SCHEDULE', 1);
        defined('VP_APPROVE') or define('VP_APPROVE', 2);
        defined('VP_REJECTED') or define('VP_REJECTED', 3);
        defined('TRAINING_RESCHEDULE_APPROVAL') or define('TRAINING_RESCHEDULE_APPROVAL', 4);
        defined('TRAINING_NOMINATION_COMPLETED') or define('TRAINING_NOMINATION_COMPLETED', 5);
        defined('TRAINING_START') or define('TRAINING_START', 6);
        defined('TRAINING_FEEDBACK_ADMIN_APPROVE') or define('TRAINING_FEEDBACK_ADMIN_APPROVE', 7);
        defined('TRAINING_COMPLETED') or define('TRAINING_COMPLETED',8);




        defined('STATUS_HOD_APPROVAL_PENDING') or define('STATUS_HOD_APPROVAL_PENDING', 1);
        defined('STATUS_HOD_APPROVED') or define('STATUS_HOD_APPROVED', 2);
        defined('STATUS_HOD_REJECTED') or define('STATUS_HOD_REJECTED', 3);
        defined('STATUS_EHS_APPROVAL_PENDING') or define('STATUS_EHS_APPROVAL_PENDING', 4);
        defined('STATUS_EHS_APPROVED') or define('STATUS_EHS_APPROVED', 5);
        defined('STATUS_EHS_REJECTED') or define('STATUS_EHS_REJECTED', 6);
        defined('STATUS_USER_APPLIED') or define('STATUS_USER_APPLIED', 7);
        defined('STATUS_ISSUED') or define('STATUS_ISSUED', 8);



        defined('TYPE_PPE_REQUEST') or define('TYPE_PPE_REQUEST', 1);
        defined('TYPE_PPE_EXEMPTION') or define('TYPE_PPE_EXEMPTION', 2);
        defined('TYPE_OHC_MEDICINE') or define('TYPE_OHC_MEDICINE', 3);
        defined('TYPE_OHC_MEDICINE_RECEIVING') or define('TYPE_OHC_MEDICINE_RECEIVING', 4);
        defined('TYPE_OHC_MEDICINE_REQUISITION') or define('TYPE_OHC_MEDICINE_REQUISITION', 5);
        defined('TYPE_OHC_MEDICINE_STOCK') or define('TYPE_OHC_MEDICINE_STOCK', 6);
        defined('TYPE_OHC_ISSUANCE') or define('TYPE_OHC_ISSUANCE', 7);


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
        defined('STATUS_CANCELLED') or define('STATUS_CANCELLED', 14);
        defined('STATUS_CLOSED') or define('STATUS_CLOSED', 15);
        defined('STATUS_EHS_OFFICER_UPDATED') or define('STATUS_EHS_OFFICER_UPDATED', 16);


        // OHC Management

        // Medicine Approval

        defined('STATUS_OHC_MEDICINE_REQUEST') or define('STATUS_OHC_MEDICINE_REQUEST', 1);
        defined('STATUS_OHC_EHS_HEAD_APPROVAL_PENDING') or define('STATUS_OHC_EHS_HEAD_APPROVAL_PENDING', 2);
        defined('STATUS_OHC_EHS_HEAD_APPROVED') or define('STATUS_OHC_EHS_HEAD_APPROVED', 3);
        defined('STATUS_OHC_EHS_HEAD_REJECTED') or define('STATUS_OHC_EHS_HEAD_REJECTED', 4);

        // medicine receiving approval

        defined('STATUS_OHC_STOCK_REQUEST') or define('STATUS_OHC_STOCK_REQUEST', 1);
        defined('STATUS_OHC_EHS_VERIFICATION_PENDING') or define('STATUS_OHC_EHS_VERIFICATION_PENDING', 2);
        defined('STATUS_OHC_EHS_VERIFIED') or define('STATUS_OHC_EHS_VERIFIED', 3);
        defined('STATUS_OHC_EHS_REJECTED') or define('STATUS_OHC_EHS_REJECTED', 4);
        defined('STATUS_OHC_L1_EHS_VERIFICATION_PENDING') or define('STATUS_OHC_L1_EHS_VERIFICATION_PENDING', 5);
        defined('STATUS_OHC_L1_EHS_VERIFIED') or define('STATUS_OHC_L1_EHS_VERIFIED', 6);
        defined('STATUS_OHC_L1_EHS_REJECTED') or define('STATUS_OHC_L1_EHS_REJECTED', 7);
        defined('STATUS_OHC_AGM_APPROVAL_PENDING') or define('STATUS_OHC_AGM_APPROVAL_PENDING', 8);
        defined('STATUS_OHC_AGM_APPROVED') or define('STATUS_OHC_AGM_APPROVED', 9);
        defined('STATUS_OHC_AGM_REJECTED') or define('STATUS_OHC_AGM_REJECTED', 10);
        defined('STATUS_OHC_OPEN') or define('STATUS_OHC_OPEN', 11);
        defined('STATUS_OHC_CLOSE') or define('STATUS_OHC_CLOSE', 12);

        // Requisition status
        defined('STATUS_OHC_REQUISITION_STOCK_REQUEST') or define('STATUS_OHC_REQUISITION_STOCK_REQUEST', 1);
        defined('STATUS_OHC_PARAMEDICS_APPROVAL_PENDING') or define('STATUS_OHC_PARAMEDICS_APPROVAL_PENDING', 2);
        defined('STATUS_OHC_PARAMEDICS_APPROVED') or define('STATUS_OHC_PARAMEDICS_APPROVED', 3);
        defined('STATUS_OHC_PARAMEDICS_REJECTED') or define('STATUS_OHC_PARAMEDICS_REJECTED', 4);
        defined('STATUS_OHC_REQUISITION_OPEN') or define('STATUS_OHC_REQUISITION_OPEN', 5);
        defined('STATUS_OHC_REQUISITION_CLOSE') or define('STATUS_OHC_REQUISITION_CLOSE', 6);


        // IMS  EHS_REVIEW
        defined('EHS_REVIEW') or define('EHS_REVIEW', 1);
        defined('EHS_VERIFY') or define('EHS_VERIFY', 2);
        defined('EHS_APPROVAL') or define('EHS_APPROVAL', 3);

        // IMS Incident

        defined('STATUS_INCIDENT_REPORT') or define('STATUS_INCIDENT_REPORT', 1);
        defined('STATUS_INVESTIGATION_PENDING') or define('STATUS_INVESTIGATION_PENDING', 2);
        defined('STATUS_UAUC_PENDING') or define('STATUS_UAUC_PENDING', 3);
        defined('STATUS_RISKANALYSIS_PENDING') or define('STATUS_RISKANALYSIS_PENDING', 4);
        defined('STATUS_EHSVERIFY_PENDING') or define('STATUS_EHSVERIFY_PENDING', 5);
        defined('STATUS_ACTION_PENDING') or define('STATUS_ACTION_PENDING', 6);
        defined('STATUS_EHSAPPROVAL_PENDING') or define('STATUS_EHSAPPROVAL_PENDING', 7);
        defined('STATUS_EHSAPPROVAL_REJECTED') or define('STATUS_EHSAPPROVAL_REJECTED', 8);

        defined('STATUS_ACCIDENT_REPORT') or define('STATUS_ACCIDENT_REPORT', 1);
        defined('STATUS_ACCIDENT_CLOSED') or define('STATUS_ACCIDENT_CLOSED', 9);
        defined('STATUS_INCIDENT_CLOSED') or define('STATUS_INCIDENT_CLOSED', 9);




        //Incident
        defined('CHECKLIST_TYPE') or define('CHECKLIST_TYPE', 1);
        defined('CHECKLIST_SUB_TYPE') or define('CHECKLIST_SUB_TYPE', 2);

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
