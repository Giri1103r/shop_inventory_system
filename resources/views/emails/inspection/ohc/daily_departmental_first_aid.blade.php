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
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Document Number</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ $details['document_no']->doc_no }}</td>
                        </tr>


                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Issue date</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ displaydateformat($details['document_no']->issue_date) }}</td>
                        </tr>


                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Review Date</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ $details['document_no']->rev_dt }}</td>
                        </tr>
                        <tr>
                            @if (isset($details['data']->date))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Date</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ displayDateformat($details['data']->date) }}</td>
                            @endif
                        </tr>

                        <tr>
                            @if (isset($details['data']->unit))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Unit</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ getUnitname($details['data']->unit) }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if (isset($details['data']->department))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Department</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ getDepartment($details['data']->department) }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if (isset($details['data']->shift))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Shift</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ getShift($details['data']->shift) }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if (isset($details['data']->first_aider))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>First Aider</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ getFirstAider($details['data']->first_aider) }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if (isset($details['data']->first_aid_box_no))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>First Aid Box Number</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ $details['data']->first_aid_box_no }}</td>
                            @endif
                        </tr>

                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Created By</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ getUsername($details['data']->created_by) }}</td>
                        </tr>

                    </tbody>
                </table>

                @if (!empty($details['data']->checklist))
                    @php
                        $inspectionData = json_decode($details['data']->checklist, true);
                    @endphp

                    @if (!empty($inspectionData) && is_array($inspectionData))
                        <table role="presentation" border="1" cellpadding="0" cellspacing="0"
                            style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; margin-top: 7%;"
                            width="100%">
                            <tbody style="font-family:Nakheel Headline">
                                <tr>
                                    <td colspan="5" align="center"
                                        style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                        valign="top">
                                        <b>Medicine Details</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-family: sans-serif; font-size: 14px;"><b>Medicine Name</b></td>
                                    <td style="font-family: sans-serif; font-size: 14px;"><b>Freeze Quantity</b></td>
                                    <td style="font-family: sans-serif; font-size: 14px;"><b>Available Quantity</b></td>
                                    <td style="font-family: sans-serif; font-size: 14px;"><b>Expiry Date</b></td>
                                    <td style="font-family: sans-serif; font-size: 14px;"><b>Remarks</b></td>
                                </tr>

                                @foreach ($inspectionData as $entry)
                                    <tr>
                                        <td>{{ getmedicinename($entry['medicine_id']) }}</td>
                                        <td>{{ $entry['freeze_quantity'] }}</td>
                                        <td>{{ $entry['available_quantity'] }}</td>
                                        <td>{{ displaydateformat($entry['expired_date']) }}</td>
                                        <td>{{ $entry['remarks'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No medicine inspection data available.</p>
                    @endif
                @else
                    <p>No inspection data found.</p>
                @endif

                @if (!empty($details['data']->verified_by))
                    <table role="presentation" border="1" cellpadding="0" cellspacing="0"
                        style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; margin-top: 7%;"
                        width="100%">
                        <tbody style="font-family:Nakheel Headline">
                            <tr>
                                <td colspan="4" align="center"
                                    style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Floor Manager / Medical Assistant Approval</b>
                                </td>
                            </tr>

                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Status</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top">

                                    @if ($details['data']->approve_status == 9)
                                        Approved
                                    @elseif ($details['data']->approve_status == 10)
                                        Rejected
                                    @else
                                        Unknown
                                    @endif
                                </td>
                            </tr>


                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b> Inspection Approve / Rejected By</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ getUsername($details['data']->verified_by) }}</td>
                            </tr>
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b> Inspection Approve / Rejected at </b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ now()->format('d-m-y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Remarks</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ $details['data']->floor_manager_remarks }}</td>
                            </tr>
                        </tbody>
                    </table>
                @endif

            </td>
        </tr>
    </table>
@stop
