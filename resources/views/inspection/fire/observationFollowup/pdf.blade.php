@extends('admin.layouts.pdf')
@section('title', 'CHECKLIST OBSERVATION FOLLOW UP SHEET| PDF')
@section('content')

    <div style="width:100%;">
        <table class="table" style="width:100%;border: 0.5px solid;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    @foreach ($header as $key => $value)
                        <td style='padding: 7px;border: 0.5px solid;font-weight:bold;text-align:center;'>
                            {{ $value }}
                        </td>
                    @endforeach
                </tr>
            </thead>

            <tbody>

                @php
                    $i = 1;
                @endphp
                @foreach ($content as $key => $value)
                    @php
                        switch ($value->inspection_type) {
                            case HOOTER_INSPECTION:
                                $inspectionType = 'Hooter Inspection';
                                break;
                            case EMERGENCY_LIGHT_INSPECTION:
                                $inspectionType = 'Emergency Light Inspection';
                                break;
                            case MONTHLY_FIRE_PUMP:
                                $inspectionType = 'Monthly Fire Pump';
                                break;
                            case FIRE_MOCK_DRILL_INSPECION:
                                $inspectionType = 'Fire Mock Drill Inspection';
                                break;
                            case FIRE_EXTINGUISHER_INSPECTION:
                                $inspectionType = 'Fire Extinguisher Inspection';
                                break;
                            case ISOLATION_VALVE_INSPECTION:
                                $inspectionType = 'Isolation Valve Inspection';
                                break;
                            case FIRE_ALARM_INSPECTION:
                                $inspectionType = 'Fire Alarm Inspection';
                                break;
                            case SPRINKLAR_SYSTEM_INSPECTION:
                                $inspectionType = 'Sprinkler System Inspection';
                                break;
                            case SAND_BUCKET_INSPECTION:
                                $inspectionType = 'Sand Bucket Inspection';
                                break;
                            case DETECTOR_INSPECTION:
                                $inspectionType = 'Detector Inspection';
                                break;
                            case FIRE_PA_SYSTEM_INSPECTION:
                                $inspectionType = 'Fire PA System Inspection';
                                break;
                            case DAILY_FIRE_PUMP:
                                $inspectionType = 'Daily Fire Pump';
                                break;
                            case CO_TYPE_FIRE_EXTINGUISHER_INSPECTION:
                                $inspectionType = 'CO Type Fire Extinguisher Inspection';
                                break;
                            case HOSE_BOX_INSPECTION:
                                $inspectionType = 'Hose Box Inspection';
                                break;
                            case CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION:
                                $inspectionType = 'Cartridge Type Fire Extinguisher Inspection';
                                break;
                            case HOSE_REEL_INSPECTION:
                                $inspectionType = 'Hose Reel Inspection';
                                break;
                            case FIRE_MODULAR_INSPECTION:
                                $inspectionType = 'Fire Modular Inspection';
                                break;
                            case HYDRANT_RISER:
                                $inspectionType = 'Hydrant Riser';
                                break;
                            case OBSERVATION_FOLLOWUP:
                                $inspectionType = 'Observation Follow-up';
                                break;
                            case OBSERVATION_FOLLOWUP:
                                $inspectionType = 'Observation Follow-up';
                                break;
                            case GEMBA_WALK:
                                $inspectionType = 'Observation Follow-up';
                                break;
                            default:
                                $inspectionType = 'Unknown';
                        }
                    @endphp
                    <tr>
                        <td style='padding: 7px;border: 0.5px solid;text-align:center'>
                            {{ $i }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->observation_id }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $inspectionType }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->sr_no }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ Displaydateformat($value->date_of_inspection) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getInspectionstatus($value->observation_status) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            @php
                                $status = $value->status == 1 ? 'Active' : 'In-Active';
                            @endphp
                            {{ $status }}
                        </td>

                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getusername($value->created_by) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ Displaydateformat($value->created_at) }}
                        </td>
                    </tr>
                    @php
                        $i++;
                    @endphp
                @endforeach
            </tbody>
        </table>
        <br>
    </div>

@stop
