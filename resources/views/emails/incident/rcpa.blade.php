@extends('emails.layouts.email')
@section('content')
    <table role="presentation" border="0" cellpadding="0" cellspacing="0"
        style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
        <tr>
            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">
                </p>
                <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">
                    <br>
                </p>

                {{-- resources/views/emails/incident/investigation.blade.php --}}

                <table role="presentation" border="1" cellpadding="0" cellspacing="0"
                    style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;"
                    width="100%">
                    <tbody style="font-family:Nakheel Headline">
                        <tr>
                            <td colspan="4" align="center"
                                style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Incident Report</b>
                            </td>
                        </tr>

                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px;"><b>Incident ID</b></td>
                            <td colspan="3">{{ $details['sr_no'] }}</td>
                        </tr>

                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px;"><b>Incident Date & Time</b></td>
                            <td colspan="3">{{ $details['incident_date_time'] }}</td>
                        </tr>

                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px;"><b>Company</b></td>
                            <td colspan="3">{{ getCompanyname($details['company_id']) }}</td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px;"><b>Unit</b></td>
                            <td colspan="3">{{ getUnitname($details['unit_id']) }}</td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px;"><b>Shift</b></td>
                            <td colspan="3">{{ $details['shift'] }}</td>
                        </tr>

                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px;"><b>Location</b></td>
                            <td colspan="3">{{ getLocationname($details['location_id']) }}</td>
                        </tr>

                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px;"><b>Exact location</b></td>
                            <td colspan="3">{{ $details['exact_location'] }}</td>
                        </tr>



                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Brief Description</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top">
                                {{ $details['brief_description'] }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Immediate Action Taken</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top">
                                {{ $details['immediate_action_taken'] }}
                            </td>
                        </tr>

                        @if (!empty($details['investigation_assigned']))
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px;"><b>IM Team Members</b></td>
                                <td colspan="3">
                                    @php
                                        $responsibilityIds = explode(',', $details['investigation_assigned']);
                                    @endphp
                                    @foreach ($responsibilityIds as $responsibilityId)
                                        {{ getUsername($responsibilityId) }}@if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                </td>
                            </tr>
                        @endif

                        @if (!empty($details['investigation_reported_by']))
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px;"><b>Investigation Report Prepared
                                        By</b></td>
                                <td colspan="3">{{ getUsername($details['investigation_reported_by']) }}</td>
                            </tr>
                        @endif

                        @if (!empty($details['investigation_remarks']))
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px;"><b>Remarks By EHS Head</b></td>
                                <td colspan="3">{{ $details['investigation_remarks'] }}</td>
                            </tr>
                        @endif

                        @if (!empty($investigationarray['main_root_cause']))
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px;"><b>Main Root Cause</b></td>
                                <td colspan="3">{{ $investigationarray['main_root_cause'] }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px;"><b>Incidence Reported by</b></td>
                            <td colspan="3">{{ $details['reported_name'] }}</td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px;"><b>Created By</b></td>
                            <td colspan="3">{{ getUsername($details['created_by']) }}</td>
                        </tr>
                    </tbody>
                </table>

                <table role="presentation" border="1" cellpadding="0" cellspacing="0"
                    style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; margin-top: 7%;"
                    width="100%">
                    <tbody style="font-family:Nakheel Headline">
                        <tr>
                            <td colspan="4" align="center"
                                style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Investigation Report</b>
                            </td>
                        </tr>
                        @if (!empty($investigationarray['root_cause_analysis']))
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px;"><b>Root Cause Anaysis</b></td>
                                <td colspan="3">{{ getRootcause($investigationarray['root_cause_analysis']) }}</td>
                            </tr>
                        @endif

                        @if (!empty($investigationarray['main_root_cause']))
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px;"><b>Main Root Cause</b></td>
                                <td colspan="3">{{ $investigationarray['main_root_cause'] }}</td>
                            </tr>
                        @endif

                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px;"><b>Created By</b></td>
                            <td colspan="3">{{ getUsername($investigationarray['created_by']) }}</td>
                        </tr>
                    </tbody>
                </table>

                <br><br>

                <table role="presentation" border="1" cellpadding="0" cellspacing="0"
                    style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; margin-top: 7%;"
                    width="100%">
                    <thead>
                        <tr>
                            <th style="font-family: sans-serif; font-size: 14px;"><b>Serial Number</b></th>
                            <th style="font-family: sans-serif; font-size: 14px;"><b>Recommended Corrective & Preventive
                                    Action</b></th>
                            <th style="font-family: sans-serif; font-size: 14px;"><b>Responsibility</b></th>
                            <th style="font-family: sans-serif; font-size: 14px;"><b>TimeLine</b></th>
                            <th style="font-family: sans-serif; font-size: 14px;"><b>Status</b></th>
                            <th style="font-family: sans-serif; font-size: 14px;"><b>Remark if any</b></th>
                        </tr>
                    </thead>
                    <tbody style="font-family:Nakheel Headline">
                        @if ($rcpaarray->isEmpty())
                            <tr>
                                <td colspan="5" align="center" style="font-family: sans-serif; font-size: 14px;">
                                    <b>No data is available</b>
                                </td>
                            </tr>
                        @else
                            @foreach ($rcpaarray as $data)
                                <tr>
                                    <td style="font-family: sans-serif; font-size: 14px;">{{ $data['rcpa_id'] }}</td>
                                    <td style="font-family: sans-serif; font-size: 14px;">{{ $data['rcpa'] }}</td>
                                    <td style="font-family: sans-serif; font-size: 14px;">
                                        {{ getUsername($data['responsibility']) }}</td>
                                    <td style="font-family: sans-serif; font-size: 14px;">
                                        {{ displaydateformat($data['timeline']) }}</td>
                                    <td style="font-family: sans-serif; font-size: 14px;">
                                        {{ getIncidentStatus($data['capa_status']) }}</td>
                                    <td style="font-family: sans-serif; font-size: 14px;">{{ $data['remark'] }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>

                <table role="presentation" border="1" cellpadding="0" cellspacing="0"
                    style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; margin-top: 7%;"
                    width="100%">
                    <tbody style="font-family:Nakheel Headline">
                        <tr>
                            <td colspan="4" align="center"
                                style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Incident Corrective Action</b>
                            </td>
                        </tr>
                        @if (!empty($rcpaActionTakenarray['rcpa_id']))
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px;"><b>Serial Number</b></td>
                                <td colspan="3">{{ $rcpaActionTakenarray['rcpa_id'] }}</td>
                            </tr>
                        @endif

                        @if (!empty($rcpaActionTakenarray['action_submission_by']))
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px;"><b>Action Taken By</b></td>
                                <td colspan="3">{{ getUsername($rcpaActionTakenarray['action_submission_by']) }}</td>
                            </tr>
                        @endif

                        @if (!empty($rcpaActionTakenarray['updated_at']))
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px;"><b>Action Taken at</b></td>
                                <td colspan="3">{{ now()->format('d-m-y H:i:s') }}</td>
                            </tr>
                        @endif

                        @if (!empty($rcpaActionTakenarray['updated_at']))
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px;"><b>Remarks</b></td>
                                <td colspan="3">{{ $rcpaActionTakenarray['action_submission_description'] }}</td>
                            </tr>
                        @endif

                    </tbody>
                </table>
                @if (!empty($rcpaActionTakenarray['updated_by']))
                    <table role="presentation" border="1" cellpadding="0" cellspacing="0"
                        style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; margin-top: 7%;"
                        width="100%">
                        <tbody style="font-family:Nakheel Headline">
                            <tr>
                                <td colspan="4" align="center"
                                    style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Incident Closed/Rejected</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px;"><b>Status</b></td>
                                <td colspan="3">
                                    @if ($rcpaActionTakenarray['incident_status'] == 9)
                                        Closed
                                    @elseif ($rcpaActionTakenarray['incident_status'] == 8)
                                        Rejected
                                    @else
                                        Unknown
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px;"><b>Incident Closed/Rejected At</b>
                                </td>
                                <td colspan="3">{{ now()->format('d-m-y H:i:s') }}</td>
                            </tr>

                            @if (!empty($rcpaActionTakenarray['updated_by']))
                                <tr>
                                    <td style="font-family: sans-serif; font-size: 14px;"><b>Incident Closed/Rejected
                                            By</b>
                                    </td>
                                    <td colspan="3">{{ getUsername($rcpaActionTakenarray['updated_by']) }}</td>
                                </tr>
                            @endif
                            @if (!empty($rcpaActionTakenarray['ehs_remarks']))
                                <tr>
                                    <td style="font-family: sans-serif; font-size: 14px;"><b>Remarks</b></td>
                                    <td colspan="3">{{ $rcpaActionTakenarray['ehs_remarks'] }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                @endif
            </td>
        </tr>
    </table>
@stop
