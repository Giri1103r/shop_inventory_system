<?php

use Carbon\Carbon;
use App\Models\User;

use App\Models\Master\Unit;
use App\Models\Master\Work;
use Illuminate\Support\Str;
use App\Models\Master\Topic;
use App\Models\Notification;
use App\Models\Master\Company;
use App\Models\IMS\Master\Hira;
use App\Models\Master\Employee;
use App\Models\Master\Location;

use App\Models\Master\UserRole;
use App\Models\Master\Department;
use Illuminate\Support\Facades\DB;
use App\Models\Permit\SafetyPermit;
use Illuminate\Support\Facades\Crypt;
use App\Models\IMS\Master\IncidentType;
use App\Models\IMS\Incident\AccidentReport;
use Kreait\Firebase\Messaging\CloudMessage;
use App\Models\IMS\Incident\InitialIncident;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\WebPushConfig;
use App\Models\OhcManagement\Master\Medicine;
use App\Models\Inspection\Master\ChecklistType;
use App\Models\OhcManagement\MedicineReceiving;
use App\Models\IMS\Incident\InitialFireIncident;
use App\Models\Inspection\GembaWalk\GembaWalk;
use App\Models\Inspection\GembaWalkChecklist;
use App\Models\OhcManagement\UserMedicineIssuance;
use App\Models\OhcManagement\Opd\PrescribetoPatient;
use App\Models\OhcManagement\UserMedicineRequisition;
use App\Models\Inspection\Master\ChecklistSubType;
use App\Models\Inspection\audit\AuditAssessment;
use App\Models\Inspection\audit\AuditAnalysis;
use App\Models\Inspection\audit\Master\Task;
use App\Models\Inspection\Environment\Environment;
use App\Models\Inspection\Fire\Fire;
use App\Models\Inspection\Ohc\SafetyPettyChecklist;
use App\Models\Inspection\Fire\DailyFireHouseInspection;
use App\Models\Inspection\audit\InterUnitAudit;
use App\Models\Inspection\Fire\FirePreNocInspection;
use App\Models\Inspection\Fire\FireCheckListFollowUp;
use App\Models\Inspection\Ohc\HealthInstrumentCalibration;
use App\Models\IMS\Incident\IncidentBodyParts;
use App\Models\Inspection\audit\MonthlyAuditPlan;
use App\Models\Master\PpeRequest;
use App\Models\Master\PpeExemption;
use App\Models\Master\TrainingSchedule;

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
            case 'inctype':
                $count = IncidentType::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'INC-TYPE-' . getautogen($count);
                break;
            case 'hira':
                $count = Hira::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'HIRA-' . getautogen($count);
                break;
            case 'accidentReportNo':
                $count = AccidentReport::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'ACCIDENT-' . getautogen($count);
                break;
            case 'incident':
                $count = InitialIncident::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'INCIDENT-' . getautogen($count);
                break;
            case 'fireincident':
                $count = InitialFireIncident::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'FIRE-INCIDENT-' . getautogen($count);
                break;
            case 'incident_checklist_type':
                $count = ChecklistType::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'CAT-' . getautogen($count);
                break;

            case 'gembaWalk':
                $count = GembaWalk::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'GMB-' . getautogen($count);
                break;

            case 'incident_checklist_subtype':
                $count = ChecklistSubType::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'SUBCAT-' . getautogen($count);
                break;
            case 'audit_assessment':
                $count = AuditAssessment::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'AUDIT-ASSESSMENT-' . getautogen($count);
                break;
            case 'audit_analysis':
                $count = AuditAnalysis::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'AUDIT-ANALYSIS-' . getautogen($count);
                break;
            case 'audit_task':
                $count = Task::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'AUDIT-TASk-' . getautogen($count);
                break;
            case 'ambientNoiseNo':
                $count = Environment::where('type', 1)->withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'AMBIENT-NOISE-' . getautogen($count);
                break;
            case 'workNoiseNo':
                $count = Environment::where('type', 2)->withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'WORK-NOISE-' . getautogen($count);
                break;
            case 'ambientAirNo':
                $count = Environment::where('type', 3)->withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'AMBIENT-AIR-' . getautogen($count);
                break;
            case 'workZoneAirNo':
                $count = Environment::where('type', 4)->withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'WORKZONE-AIR-' . getautogen($count);
                break;
            case 'dgsetNo':
                $count = Environment::where('type', 5)->withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'DG-SET-' . getautogen($count);
                break;
            case 'luxNo':
                $count = Environment::where('type', 6)->withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'LUX-' . getautogen($count);
                break;
            case 'dailyFirePumpHouseNo':
                $count = Fire::where('type', 1)->withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'DAILY-FIREPUMP-' . getautogen($count);
                break;
            case 'certifiedFireFighterNo':
                $count = Fire::where('type', 2)->withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'CERTIFIED-FIREFIGHTER-' . getautogen($count);
                break;
            case 'fireSafetyNO':
                $count = Fire::where('type', 3)->withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'FIRESAFETY-EQ-' . getautogen($count);
                break;
            case 'preNocNo':
                $count = Fire::where('type', 4)->withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'FIRE-PRENOC-' . getautogen($count);
                break;
            case 'fireModularNo':
                $count = Fire::where('type', 5)->withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'FIRE-MODULAR-' . getautogen($count);
                break;
            case 'SPLB':
                $count = SafetyPettyChecklist::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'SPLB-' . getautogen($count);
                break;
            case 'DailyfireHouse':
                $count = DailyFireHouseInspection::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'DAILY-FIRE-HOUSE-' . getautogen($count);
                break;
            case 'InterUnitAudit':
                $count = InterUnitAudit::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'INTER-UNIT-MONTHLY-AUDIT-' . getautogen($count);
                break;
            case 'FirePreNoc':
                $count = FirePreNocInspection::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'FIRE-PRENOC-' . getautogen($count);
                break;
            case 'Observation':
                $count = FireCheckListFollowUp::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'OBSERVATION-' . getautogen($count);
                break;
            case 'HealthInstrument':
                $count = HealthInstrumentCalibration::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'HEALTH-' . getautogen($count);
                break;

            case 'IncidentRandomID':
                $count = IncidentBodyParts::withoutGlobalScopes()->count();
                $count = $count + 1;
                $sequence = 'INCIDENTBODY-' . getautogen($count);
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
            case 'ppe_request':
                $count = PpeRequest::count();
                break;
            case 'ppe_exception':
                $count = PpeExemption::count();
                break;
            case 'safetypermit':
                $count = SafetyPermit::count();
                break;
            case 'training':
                $count = TrainingSchedule::count();
                break;
            case 'audit_assessment':
                $count = AuditAssessment::count();
                break;
            case 'audit_analysis':
                $count = AuditAnalysis::count();
                break;
            case 'monthly_audit':
                $count = MonthlyAuditPlan::count();
                break;
            case 'inter_unit_audit':
                $count = InterUnitAudit::count();
                break;
            default:
                $count = 0;
                break;
        }

        return $count;
    }
}

if (!function_exists('GetInspectionCount')) {
    function GetInspectionCount($type)
    {
        $group_wise_models = [];

        switch ($type) {
            case 'Environment':
                $group_wise_models['Environment'] = [
                    \App\Models\Inspection\Environment\AmbientAirMonitoring::class,
                    \App\Models\Inspection\Environment\AmbientNoiseMonitoring::class,
                    \App\Models\Inspection\Environment\DgSetStackEmissionMonitoring::class,
                    \App\Models\Inspection\Environment\LuxMonitoring::class,
                    \App\Models\Inspection\Environment\WorkNoiseMonitoring::class,
                    \App\Models\Inspection\Environment\WorkZoneAirMonitoring::class,
                ];
                break;

            case 'Fire':
                $group_wise_models['Fire'] = [
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
                ];
                break;

            case 'GembaWalk':
                $group_wise_models['GembaWalk'] = [
                    \App\Models\Inspection\GembaWalk\GembaWalk::class,
                ];
                break;

            case 'MSDS':
                $group_wise_models['MSDS'] = [
                    \App\Models\Inspection\MSDS\MSDS::class,
                ];
                break;

            case 'RRAA':
                $group_wise_models['RRAA'] = [
                    \App\Models\Inspection\RRAA\RRAADetails::class,
                ];
                break;

            case 'Safety':
                $group_wise_models['Safety'] = [
                    \App\Models\Inspection\Safety\FireSafetyEquipment::class,
                    \App\Models\Inspection\Safety\ForkLiftInspection::class,
                    \App\Models\Inspection\Safety\MonthlyEyeWashInspection::class,
                    \App\Models\Inspection\Safety\MonthlyForkLiftInspection::class,
                    \App\Models\Inspection\Safety\OHSPlantSummaryReport::class,
                    \App\Models\Inspection\Safety\SafetyGalleryInspection::class,
                    \App\Models\Inspection\Safety\SafetyWalkObservation::class,
                ];
                break;

            case 'Ohc':
                $group_wise_models['Ohc'] = [
                    \App\Models\Inspection\Ohc\CurrentNewExtCodeDialing::class,
                    \App\Models\Inspection\Ohc\DailyDepartmentFirstAidBox::class,
                    \App\Models\Inspection\Ohc\DailyVitalEquipment::class,
                    \App\Models\Inspection\Ohc\EmergencyBuyerFirstAidChecklist::class,
                    \App\Models\Inspection\Ohc\FirstAidBagChecklist::class,
                    \App\Models\Inspection\Ohc\FirstAiderList::class,
                    \App\Models\Inspection\Ohc\FirstAidMedicineInspection::class,
                    \App\Models\Inspection\Ohc\FirstAidRecordChecklist::class,
                    \App\Models\Inspection\Ohc\FloorStretcher::class,
                    \App\Models\Inspection\Ohc\HealthInstrumentCalibrationDetails::class,
                    \App\Models\Inspection\Ohc\MedicineRequistionSlipfdodetails::class,
                    \App\Models\Inspection\Ohc\MedicineRequistionSlipfloordetails::class,
                    \App\Models\Inspection\Ohc\MonthlyFirstAidbox::class,
                    \App\Models\Inspection\Ohc\MonthlyMedicineStore::class,
                    \App\Models\Inspection\Ohc\OccupationHealthInspection::class,
                    \App\Models\Inspection\Ohc\OHCHygieneCleaningChecklist::class,
                    \App\Models\Inspection\Ohc\SafetyPettyDetails::class,
                    \App\Models\Inspection\Ohc\WeeklyAmbulance::class,
                    \App\Models\Inspection\Ohc\WeeklyFirstAidBox::class,
                ];
                break;

            default:
                return []; // Return empty if no valid type is matched
        }

        $groupCounts = [];
        $grandTotal = 0;

        foreach ($group_wise_models as $groupName => $models) {
            $total = 0;

            foreach ($models as $modelClass) {
                $query = $modelClass::where('status', 1)->where('trash', 'NO');
                $count = $query->count();
                $total += $count;
            }

            $groupCounts[$groupName] = $total;
            $grandTotal += $total;
        }

        return $groupCounts;
    }
}


if (!function_exists('getohctotalCount')) {
    function getohctotalCount($type, $unit_id = null)
    {

        switch ($type) {

            case 'requisition':
                $count = UserMedicineRequisition::where('status', 1)
                    ->where('unit_id', $unit_id)
                    ->where('approve_status', STATUS_OHC_REQUISITION_EHS_HEAD_APPROVAL_PENDING);
                break;
            case 'medicine':
                $count = Medicine::where('status', 1);
                break;
            case 'medicineReceiving':
                $count = MedicineReceiving::whereDate('approved_date', Carbon::today());
                break;
            case 'usermedicineissuance':
                $count = UserMedicineIssuance::where('unit_id', $unit_id)->Where('status', 1);
                break;
            case 'prescribetopatient1':
                $count = PrescribetoPatient::where('unit_id', 1);
                break;
            case 'prescribetopatient2':
                $count = PrescribetoPatient::where('unit_id', 2);
                break;
            case 'prescribetopatient3':
                $count = PrescribetoPatient::where('unit_id', 3);
                break;
            case 'prescribetopatient4':
                $count = PrescribetoPatient::where('unit_id', 4);
                break;
            case 'prescribetopatient':
                $count = PrescribetoPatient::whereDate('created_at', Carbon::today())->where('unit_id', $unit_id);
                break;
            case 'medicineissuance':
                $count = UserMedicineIssuance::whereDate('created_at', Carbon::today())->where('unit_id', $unit_id)->Where('status', 1);
                break;
            case 'certifiedFirstAider':
                $count = User::where('role', ROLE_CERTIFIED_FIRST_AIDER)->where('status', 1)->where('unit_id', $unit_id);
                break;
            default:
                return 0;
        }

        return $count->count();
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
