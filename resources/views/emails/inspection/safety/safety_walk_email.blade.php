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

                <table role="presentation" border="1" cellpadding="0" cellspacing="0"
                    style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;"
                    width="100%">
                    <tbody style="font-family:Nakheel Headline">
                        <tr>
                            <td colspan="4" align="center"
                                style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>{{ $details['title'] }}</b>
                            </td>
                        </tr>

                        <tr>
                            @if (isset($details['data']->date))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Date of Inspection</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ displaydateformat($details['data']->date) }}</td>
                            @endif
                        </tr>


                        <tr>
                            @if (isset($details['data']->shift_id))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Shift</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ getShift($details['data']->shift_id) }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if (isset($details['data']->location_id))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Location Name</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ getLocationname($details['data']->location_id) }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if (isset($details['data']->excat_location))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Excat Location</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ $details['data']->excat_location }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if (isset($details['data']->observation_date))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Observation Date</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ displayDateformat($details['data']->observation_date) }}</td>
                            @endif
                        </tr>

                        <tr>
                            @if (isset($details['data']->observation))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Observation</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ $details['data']->observation }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if (isset($details['data']->recomended_action))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Recomended Action</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ $details['data']->recomended_action }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if (isset($details['data']->responsible_persion))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Responsible Person</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top">
                                    @php
                                        $person = explode(',', $details['data']->responsible_persion);
                                    @endphp

                                    @foreach ($person as $personId)
                                        {{ getUsername($personId) }}@if (!$loop->last)
                                            ,
                                        @endif
                                </td>
                            @endif
                        </tr>

                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Safety Walk Taken By</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ getUsername($details['data']->created_by) }}</td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Created Date</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ DisplayDateformat($details['data']->created_at) }}</td>
                        </tr>

                    </tbody>
                </table>

                @if (!empty($details['data']->observer_person))
                    <table role="presentation" border="1" cellpadding="0" cellspacing="0"
                        style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; margin-top: 7%;"
                        width="100%">
                        <tbody style="font-family:Nakheel Headline">
                            <tr>
                                <td colspan="4" align="center"
                                    style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Action Required</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px;"><b>Responsible Person</b>
                                </td>
                                <td colspan="3">{{ getUsername($details['data']->observer_person) }}</td>
                            </tr>
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px;"><b>Date of Compilance</b>
                                </td>
                                <td colspan="3">{{ now()->format('d-m-y H:i:s') }}</td>
                            </tr>
                            @if (!empty($details['data']->observer_remarks))
                                <tr>
                                    <td style="font-family: sans-serif; font-size: 14px;"><b>Remarks</b></td>
                                    <td colspan="3">{{ $details['data']->observer_remarks }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                @endif

                @if (!empty($details['data']->approver_id))
                    <table role="presentation" border="1" cellpadding="0" cellspacing="0"
                        style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; margin-top: 7%;"
                        width="100%">
                        <tbody style="font-family:Nakheel Headline">
                            <tr>
                                <td colspan="4" align="center"
                                    style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>EHS Officer Approval</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px;"><b>Status</b>
                                </td>
                                <td colspan="3">
                                    @if ($details['data']->observation_status == 5)
                                        Waiting for Re-submit of the Responsible Person
                                    @elseif ($details['data']->observation_status == 4)
                                        Rejected
                                    @elseif ($details['data']->observation_status == 3)
                                        Approved
                                    @else
                                        Unknown
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px;"><b>Inspection Approve / Rejected
                                        by</b>
                                </td>
                                <td colspan="3">{{ getUsername($details['data']->approver_id) }}</td>
                            </tr>
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px;"><b>Inspection Approve / Rejected
                                        at</b>
                                </td>
                                <td colspan="3">{{ now()->format('d-m-y H:i:s') }}</td>
                            </tr>
                            @if (!empty($details['data']->approval_remarks))
                                <tr>
                                    <td style="font-family: sans-serif; font-size: 14px;"><b>Remarks</b></td>
                                    <td colspan="3">{{ $details['data']->approval_remarks }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                @endif
            </td>
        </tr>
    </table>
@stop
