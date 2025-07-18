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

        defined('NEW_TRAINING_SCHEDULE') or define('NEW_TRAINING_SCHEDULE', 1);
        defined('VP_APPROVE') or define('VP_APPROVE', 2);
        defined('VP_REJECTED') or define('VP_REJECTED', 3);
        defined('TRAINING_RESCHEDULE_APPROVAL') or define('TRAINING_RESCHEDULE_APPROVAL', 4);
        defined('TRAINING_NOMINATION_COMPLETED') or define('TRAINING_NOMINATION_COMPLETED', 5);
        defined('TRAINING_START') or define('TRAINING_START', 6);
        defined('TRAINING_FEEDBACK_ADMIN_APPROVE') or define('TRAINING_FEEDBACK_ADMIN_APPROVE', 7);
        defined('TRAINING_COMPLETED') or define('TRAINING_COMPLETED', 8);

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
        defined('TYPE_OHC_MEDICAL_FITNESS') or define('TYPE_OHC_MEDICAL_FITNESS', 8);
        defined('TYPE_OHC_MEDICINE_DISCARD') or define('TYPE_OHC_MEDICINE_DISCARD', 9);
        defined('CHEMICAL_DEPARTMENT') or define('CHEMICAL_DEPARTMENT', 53);


        defined('FIRE_INCIDENT_REPORT') or define('FIRE_INCIDENT_REPORT', 5);
        defined('NEAR_MISS_INCIDENT_REPORT') or define('NEAR_MISS_INCIDENT_REPORT', 8);

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


        // Company Management

        defined('COMPANY_PNI') or define('COMPANY_PNI', 1);
        defined('COMPANY_PNS') or define('COMPANY_PNS', 2);
        defined('COMPANY_KPSL') or define('COMPANY_KPSL', 3);

        // incident accident
        defined('MAJOR_ACCIDENT') or define('MAJOR_ACCIDENT', 1);
        defined('MINOR_ACCIDENT') or define('MINOR_ACCIDENT', 2);
        defined('IIR_TYPE_MAJOR') or define('IIR_TYPE_MAJOR', 11);
        defined('IIR_TYPE_MINOR') or define('IIR_TYPE_MINOR', 10);
        defined('IIR_TYPE_NEAR_MISS') or define('IIR_TYPE_NEAR_MISS', 8);
        defined('IIR_TYPE_UNSAFE_ACT') or define('IIR_TYPE_UNSAFE_ACT', 23);
        defined('IIR_TYPE_UNSAFE_CONDITION') or define('IIR_TYPE_UNSAFE_CONDITION', 24);
        defined('IIR_TYPE_FIRE_INCIDENCE') or define('IIR_TYPE_FIRE_INCIDENCE', 5);
        defined('UNSAFE_ACT') or define('UNSAFE_ACT', 1);
        defined('UNSAFE_CONDITION') or define('UNSAFE_CONDITION', 2);
        defined('ALL') or define('ALL', 'ALL');



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
        defined('STATUS_OHC_REQUISITION_EHS_HEAD_APPROVAL_PENDING') or define('STATUS_OHC_REQUISITION_EHS_HEAD_APPROVAL_PENDING', 2);
        defined('STATUS_OHC_REQUISITION_EHS_HEAD_APPROVED') or define('STATUS_OHC_REQUISITION_EHS_HEAD_APPROVED', 3);
        defined('STATUS_OHC_REQUISITION_EHS_HEAD_REJECTED') or define('STATUS_OHC_REQUISITION_EHS_HEAD_REJECTED', 4);
        defined('STATUS_OHC_REQUISITION_OPEN') or define('STATUS_OHC_REQUISITION_OPEN', 5);
        defined('STATUS_OHC_REQUISITION_CLOSE') or define('STATUS_OHC_REQUISITION_CLOSE', 6);
        // Medical Certificate

        defined('STATUS_OHC_MEDICAL_PARAMEDICS_REQUEST') or define('STATUS_OHC_MEDICAL_PARAMEDICS_REQUEST', 1);
        defined('STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING') or define('STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING', 2);
        defined('STATUS_OHC_MEDICAL_DOCTOR_APPROVED') or define('STATUS_OHC_MEDICAL_DOCTOR_APPROVED', 3);
        defined('STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING') or define('STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING', 4);
        defined('STATUS_OHC_MEDICAL_EHS_HEAD_APPROVED') or define('STATUS_OHC_MEDICAL_EHS_HEAD_APPROVED', 5);
        defined('STATUS_OHC_MEDICAL_DOCTOR_REJECTED') or define('STATUS_OHC_MEDICAL_DOCTOR_REJECTED', 6);

        // medical discard approval


        defined('OHC_DISCARD_MEDICINE_CREATION') or define('OHC_DISCARD_MEDICINE_CREATION', 1);
        defined('OHC_DISCARD_EHS_APPROVAL_PENDING') or define('OHC_DISCARD_EHS_APPROVAL_PENDING', 2);
        defined('OHC_DISCARD_EHS_APPROVED') or define('OHC_DISCARD_EHS_APPROVED', 3);
        defined('OHC_DISCARD_EHS_REJECTED') or define('OHC_DISCARD_EHS_REJECTED', 4);


        // inspection ohc
        defined('OHC_TYPE_MEDICINE_REQUISTION_FDO') or define('OHC_TYPE_MEDICINE_REQUISTION_FDO', 1);
        defined('OHC_TYPE_MEDICINE_REQUISTION_FLOOR') or define('OHC_TYPE_MEDICINE_REQUISTION_FLOOR', 2);
        defined('OHC_TYPE_DAILY_DEPARTMENT_FIRST_AID_BOX') or define('OHC_TYPE_DAILY_DEPARTMENT_FIRST_AID_BOX', 3);
        defined('OHC_TYPE_OCCUPATION_HEALTH_INSPECTION') or define('OHC_TYPE_OCCUPATION_HEALTH_INSPECTION', 4);
        defined('OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST') or define('OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST', 5);
        defined('OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST') or define('OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST', 6);
        defined('OHC_SAFETY_PETTY_LOGBOOK_INSPECTION') or define('OHC_SAFETY_PETTY_LOGBOOK_INSPECTION', 9);
        defined('OHC_TYPE_DAILY_VITAL_EQUIPMENT_CHECKLIST') or define('OHC_TYPE_DAILY_VITAL_EQUIPMENT_CHECKLIST', 8);
        defined('OHC_TYPE_MONTHLY_FIRST_AID_BOX_AUDIT_INSPECTION_CHECKLIST') or define('OHC_TYPE_MONTHLY_FIRST_AID_BOX_AUDIT_INSPECTION_CHECKLIST', 7);

        defined('OHC_AMOUNT_GIVENBY_INSPECTION') or define('OHC_AMOUNT_GIVENBY_INSPECTION', 1);
        defined('OHC_AMOUNT_RECEIVEDBY_INSPECTION') or define('OHC_AMOUNT_RECEIVEDBY_INSPECTION', 2);

        defined('OHC_TYPE_FLOOR_STRETCHER') or define('OHC_TYPE_FLOOR_STRETCHER', 13);
        defined('OHC_TYPE_MONTHLY_MEDICINE_STORE') or define('OHC_TYPE_MONTHLY_MEDICINE_STORE', 19);
        defined('OHC_OPD_MEDICINE_INSPECTION') or define('OHC_OPD_MEDICINE_INSPECTION', 20);
        defined('OHC_AUDITOR_SIGN') or define('OHC_AUDITOR_SIGN', 1);
        defined('FIRST_AID_BAG_INSPECTION_CHECKLIST') or define('FIRST_AID_BAG_INSPECTION_CHECKLIST', 17);
        defined('OHC_TYPE_WEEEKLY_FIRST_AID_MEDICINE_STORE') or define('OHC_TYPE_WEEEKLY_FIRST_AID_MEDICINE_STORE', 11);
        defined('OHC_TYPE_EMERGENCY_BUYER_FIRST_AID_BAG_CHECKLIST') or define('OHC_TYPE_EMERGENCY_BUYER_FIRST_AID_BAG_CHECKLIST', 16);

        defined('DAILY_OHC_HYGIENE_CLEANING_CHECKLIST') or define('DAILY_OHC_HYGIENE_CLEANING_CHECKLIST', 15);
        defined('OHC_TYPE_PHYSICAL_HEALTH_EXAMINATION') or define('OHC_TYPE_PHYSICAL_HEALTH_EXAMINATION', 16);


        // IMS  EHS_REVIEW
        defined('EHS_REVIEW') or define('EHS_REVIEW', 1);
        defined('EHS_VERIFY') or define('EHS_VERIFY', 2);
        defined('EHS_APPROVAL') or define('EHS_APPROVAL', 3);

        // IMS Incident

        defined('STATUS_INCIDENT_REPORT') or define('STATUS_INCIDENT_REPORT', 1);
        defined('STATUS_INVESTIGATION_PENDING') or define('STATUS_INVESTIGATION_PENDING', 2);
        defined('STATUS_UAUC_PENDING') or define('STATUS_UAUC_PENDING', 3);
        defined('STATUS_RISKANALYSIS_PENDING') or define('STATUS_RISKANALYSIS_PENDING', 4);
        defined('STATUS_INVESTIGATION_CLOSED') or define('STATUS_INVESTIGATION_CLOSED', 5);
        defined('STATUS_ACTION_PENDING') or define('STATUS_ACTION_PENDING', 6);
        defined('STATUS_EHSAPPROVAL_PENDING') or define('STATUS_EHSAPPROVAL_PENDING', 7);
        defined('STATUS_EHSAPPROVAL_REJECTED') or define('STATUS_EHSAPPROVAL_REJECTED', 8);

        defined('STATUS_ACCIDENT_REPORT') or define('STATUS_ACCIDENT_REPORT', 1);
        defined('STATUS_ACCIDENT_CLOSED') or define('STATUS_ACCIDENT_CLOSED', 9);
        defined('STATUS_INCIDENT_CLOSED') or define('STATUS_INCIDENT_CLOSED', 9);




        //Incident
        defined('CHECKLIST_TYPE') or define('CHECKLIST_TYPE', 1);
        defined('CHECKLIST_SUB_TYPE') or define('CHECKLIST_SUB_TYPE', 2);

        //Upload
        defined('CHECKLIST_TYPE_UPLOAD') or define('CHECKLIST_TYPE_UPLOAD', 19);



        //Safety
        defined('WAITING_FOR_EHS_OFFICER_VERIFICATION') or define('WAITING_FOR_EHS_OFFICER_VERIFICATION', 1);
        defined('WAITING_FOR_CAPA_ACTION') or define('WAITING_FOR_CAPA_ACTION', 2);
        defined('WAITING_FOR_CAPA_VERIFICATION') or define('WAITING_FOR_CAPA_VERIFICATION', 3);
        defined('WAITING_FOR_L1_VERIFICATION') or define('WAITING_FOR_L1_VERIFICATION', 4);
        defined('WAITING_FOR_L2_VERIFICATION') or define('WAITING_FOR_L2_VERIFICATION', 5);
        defined('INSPECTION_APPROVED') or define('INSPECTION_APPROVED', 6);
        defined('EHS_OFFICER_REJECTED') or define('EHS_OFFICER_REJECTED', 7);
        defined('L1_MANAGER_REJECTED') or define('L1_MANAGER_REJECTED', 8);
        defined('L2_MANAGER_REJECTED') or define('L2_MANAGER_REJECTED', 9);


        //SAFETY WALK OBSERVATION
        defined('OBSERVATION_PENDING') or define('OBSERVATION_PENDING', 1);
        defined('OBSERVATION_REJECTED') or define('OBSERVATION_REJECTED', 2);
        defined('OBSERVATION_APPROVED') or define('OBSERVATION_APPROVED', 3);

        //OHC Hygiene Checklist
        defined('CLEANER_SUBMITTED_THE_CHECKLIST') or define('CLEANER_SUBMITTED_THE_CHECKLIST', 1);
        defined('NURSING_OFFICER_SUBMITTED_THE_CHECKLIST') or define('NURSING_OFFICER_SUBMITTED_THE_CHECKLIST', 2);
        defined('NURSING_OFFICER_REJECTED') or define('NURSING_OFFICER_REJECTED', 3);


        // INSPECTION OHC
        defined('OHC_CREATION') or define('OHC_CREATION', 1);
        defined('FLOOR_MANAGER_APPROVAL_PENDING') or define('FLOOR_MANAGER_APPROVAL_PENDING', 2);
        defined('FLOOR_MANAGER_APPROVED') or define('FLOOR_MANAGER_APPROVED', 3);
        defined('FLOOR_MANAGER_REJECTED') or define('FLOOR_MANAGER_REJECTED', 4);
        defined('SAFETY_OFFICER_APPROVAL_PENDING') or define('SAFETY_OFFICER_APPROVAL_PENDING', 5);
        defined('SAFETY_OFFICER_APPROVED') or define('SAFETY_OFFICER_APPROVED', 6);
        defined('SAFETY_OFFICER_REJECTED') or define('SAFETY_OFFICER_REJECTED', 7);
        defined('MEDICAL_ASSISTANT_APPROVAL_PENDING') or define('MEDICAL_ASSISTANT_APPROVAL_PENDING', 8);
        defined('MEDICAL_ASSISTANT_APPROVED') or define('MEDICAL_ASSISTANT_APPROVED', 9);
        defined('MEDICAL_ASSISTANT_REJECTED') or define('MEDICAL_ASSISTANT_REJECTED', 10);

        //environment
        defined('AMBIENTNOISE') or define('AMBIENTNOISE', 1);
        defined('WORKNOISE') or define('WORKNOISE', 2);
        defined('AMBIENT_AIR') or define('AMBIENT_AIR', 3);
        defined('WORKZONE_AIR') or define('WORKZONE_AIR', 4);
        defined('DGSET') or define('DGSET', 5);
        defined('LUX') or define('LUX', 6);

        //Notification Type
        defined('FIRE_INSPECTION') or define('FIRE_INSPECTION', 8);
        defined('GEMBA_WALK_NOTIIFCATION') or define('GEMBA_WALK_NOTIIFCATION', 9);
        defined('SAFETY_INSPECTION') or define('SAFETY_INSPECTION', 10);
        defined('RRAA_INSPECTION') or define('RRAA_INSPECTION', 11);
        defined('OHC_INSPECTION') or define('OHC_INSPECTION', 12);
        defined('MSDS_INSPECTION') or define('MSDS_INSPECTION', 13);

        // Gemba Walk
        defined('GEMBA_WALK_INSPECTION_START') or define('GEMBA_WALK_INSPECTION_START', 1);
        defined('GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION') or define('GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION', 2);
        defined('GEMBA_WALK_INSPECTION_WAITING_FOR_EHS_OFFICER_VERIFICATION') or define('GEMBA_WALK_INSPECTION_WAITING_FOR_EHS_OFFICER_VERIFICATION', 3);
        defined('GEMBA_WALK_INSPECTION_REJECTED') or define('GEMBA_WALK_INSPECTION_REJECTED', 4);
        defined('GEMBA_WALK_INSPECTION_CLOSED') or define('GEMBA_WALK_INSPECTION_CLOSED', 5);
        defined('GEMBA_WALK_INSPECTION_EHS_FILE_TYPE_1') or define('GEMBA_WALK_INSPECTION_EHS_FILE_TYPE_1', 1);
        defined('GEMBA_WALK_INSPECTION_EHS_FILE_TYPE_2') or define('GEMBA_WALK_INSPECTION_EHS_FILE_TYPE_2', 2);
        defined('GEMBA_WALK_INSPECTION_PASS_L1') or define('GEMBA_WALK_INSPECTION_PASS_L1', 1);
        defined('GEMBA_WALK_INSPECTION_PASS_L2') or define('GEMBA_WALK_INSPECTION_PASS_L2', 2);
        defined('GEMBA_WALK_INSPECTION_FAIL') or define('GEMBA_WALK_INSPECTION_FAIL', 3);
        defined('GEMBA_WALK_INSPECTION_PASS') or define('GEMBA_WALK_INSPECTION_PASS', 4);
        defined('GEMBA_WALK') or define('GEMBA_WALK', 16);




        //Status Log
        defined('EYE_WASH_INSPECTION') or define('EYE_WASH_INSPECTION', 1);
        defined('MONTHLY_FORKLIFT_INSPECTION') or define('MONTHLY_FORKLIFT_INSPECTION', 2);
        defined('FORKLIFT_INSPECTION') or define('FORKLIFT_INSPECTION', 3);
        defined('SAFETY_GALLERY_INSPECTION') or define('SAFETY_GALLERY_INSPECTION', 4);
        defined('OHS_SUMMARY_REPORT') or define('OHS_SUMMARY_REPORT', 5);
        defined('SAFETY_EQUIPMENT') or define('SAFETY_EQUIPMENT', 6);
        defined('SAFETY_WALK_OBSERVATION') or define('SAFETY_WALK_OBSERVATION', 7);


        // Monthly Eye Wash
        defined('GOOD') or define('GOOD', 1);
        defined('FAIR') or define('FAIR', 2);
        defined('POOR') or define('POOR', 3);
        defined('DAMAGED') or define('DAMAGED', 4);


        //Quantiy
        defined('INADEQUATE') or define('INADEQUATE', 0);
        defined('ADEQUATE') or define('ADEQUATE', 1);


        //Response Indicator
        defined('NOTWORKING') or define('NOTWORKING', 0);
        defined('WORKING') or define('WORKING', 1);

        //Working Status
        defined('OPERATIONAL') or define('OPERATIONAL', 0);
        defined('NONOPERATIONAL') or define('NONOPERATIONAL', 1);

        //Standard/Norms
        defined('STANDARD') or define('STANDARD', 1);
        defined('NORMS') or define('NORMS', 2);

        defined('FORKLIFT_INSPECTION_MONTHLY_CHECKLIST') or define('FORKLIFT_INSPECTION_MONTHLY_CHECKLIST', 1);
        defined('SAFETY_GALLERY_INSPECTION_CHECKLIST') or define('SAFETY_GALLERY_INSPECTION_CHECKLIST', 2);
        defined('MONTHLY_FIRE_PUMPHOUSE_INSPECTION_CHECKLIST') or define('MONTHLY_FIRE_PUMPHOUSE_INSPECTION_CHECKLIST', 3);
        defined('OHC_FLOOR_STRECTHER_CHECKLIST') or define('OHC_FLOOR_STRECTHER_CHECKLIST', 4);
        defined('WEEKLY_AMBULANCE_INSPECTION_CHECKLIST') or define('WEEKLY_AMBULANCE_INSPECTION_CHECKLIST', 5);
        defined('OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST') or define('OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST', 6);
        defined('CHECKLIST_AUDIT_ASSESSMENT') or define('CHECKLIST_AUDIT_ASSESSMENT', 7);
        defined('CHECKLIST_FIRE_PUMP_HOUSE_INSECTION_CHECKLIST') or define('CHECKLIST_FIRE_PUMP_HOUSE_INSECTION_CHECKLIST', 8);
        defined('INTER_UNIT_AUDIT_CHECKLIST') or define('INTER_UNIT_AUDIT_CHECKLIST', 9);
        defined('FIRE_PRE_NOC_CHECKLIST') or define('FIRE_PRE_NOC_CHECKLIST', 10);
        defined('OHC_DAILY_VITAL_EQUIPMENT_CHECKLIST') or define('OHC_DAILY_VITAL_EQUIPMENT_CHECKLIST', 11);
        defined('OHC_PHYSICAL_HEALTH_EXAMINATION') or define('OHC_PHYSICAL_HEALTH_EXAMINATION', 12);

        // Fire
        defined('HOOTER_INSPECTION') or define('HOOTER_INSPECTION', 1);
        defined('EMERGENCY_LIGHT_INSPECTION') or define('EMERGENCY_LIGHT_INSPECTION', 2);
        defined('MONTHLY_FIRE_PUMP') or define('MONTHLY_FIRE_PUMP', 14);
        defined('FIRE_MOCK_DRILL_INSPECION') or define('FIRE_MOCK_DRILL_INSPECION', 18);
        defined('FIRE_EXTINGUISHER_INSPECTION') or define('FIRE_EXTINGUISHER_INSPECTION', 3);
        defined('ISOLATION_VALVE_INSPECTION') or define('ISOLATION_VALVE_INSPECTION', 5);
        defined('FIRE_ALARM_INSPECTION') or define('FIRE_ALARM_INSPECTION', 6);
        defined('SPRINKLAR_SYSTEM_INSPECTION') or define('SPRINKLAR_SYSTEM_INSPECTION', 7);
        defined('SAND_BUCKET_INSPECTION') or define('SAND_BUCKET_INSPECTION', 8);
        defined('DETECTOR_INSPECTION') or define('DETECTOR_INSPECTION', 11);
        defined('FIRE_PA_SYSTEM_INSPECTION') or define('FIRE_PA_SYSTEM_INSPECTION', 12);
        defined('DAILY_FIRE_PUMP') or define('DAILY_FIRE_PUMP', 19);
        defined('CO_TYPE_FIRE_EXTINGUISHER_INSPECTION') or define('CO_TYPE_FIRE_EXTINGUISHER_INSPECTION', 13);
        defined('HOSE_BOX_INSPECTION') or define('HOSE_BOX_INSPECTION', 9);
        defined('CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION') or define('CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION', 15);
        defined('HOSE_REEL_INSPECTION') or define('HOSE_REEL_INSPECTION', 10);
        defined('FIRE_MODULAR_INSPECTION') or define('FIRE_MODULAR_INSPECTION', 23);
        defined('HYDRANT_RISER') or define('HYDRANT_RISER', 4);
        defined('OBSERVATION_FOLLOWUP') or define('OBSERVATION_FOLLOWUP', 17);
        defined('CERTIFIED_FIRE_FIGHTER') or define('CERTIFIED_FIRE_FIGHTER', 20);



        // OPTIONS
        defined('YES') or define('YES', 1);
        defined('NO') or define('NO', 2);

        //  template constant
        defined('PRODUCTION') or define('PRODUCTION', 1);

        // Fire File Upload
        defined('HOOTER_FILE') or define('HOOTER_FILE', 1);

        // Fire Extinguisher Type
        defined('ABC') or define('ABC', 1);
        defined('CO2') or define('CO2', 2);
        defined('WATER') or define('WATER', 3);
        defined('FOAM') or define('FOAM', 4);

        // Functional Types
        defined('FUNCTIONAL') or define('FUNCTIONAL', 1);
        defined('NON_FUNCTIONAL') or define('NON_FUNCTIONAL', 2);

        defined('PRESENT') or define('PRESENT', 1);
        defined('MISSING') or define('MISSING', 2);

        // Valve Types
        defined('GATE') or define('GATE', 1);
        defined('BALL') or define('BALL', 2);
        defined('BUTTERFLY') or define('BUTTERFLY', 3);

        defined('OPEN') or define('OPEN', 1);
        defined('CLOSE') or define('CLOSE', 2);

        // Condition Of The Glass
        defined('INTACT') or define('INTACT', 1);
        defined('BROKEN') or define('BROKEN', 2);

        defined('OK') or define('OK', 1);
        defined('NOT_OK') or define('NOT_OK', 2);

        // Audit
        defined('FIRE') or define('FIRE', 1);
        defined('HEALTH') or define('HEALTH', 2);
        defined('SAFETY') or define('SAFETY', 3);
        defined('MIS') or define('MIS', 4);

        // Pass or Fail
        defined('PASS')  or define('PASS', 1);
        defined('FAIL')  or define('FAIL', 2);

        //KPI
        defined('LEADING')  or define('LEADING', 1);
        defined('LAGGING')  or define('LAGGING', 2);


        //KPI LEADING CATEGORY
        defined('LEADING_CATEGORY_1') or define('LEADING_CATEGORY_1', 1);
        defined('LEADING_CATEGORY_2') or define('LEADING_CATEGORY_2', 2);



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
