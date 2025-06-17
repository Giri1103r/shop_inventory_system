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
                                <b>Date of Inspection</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ displaydateformat($details['data']->inspection_date) }}</td>
                        </tr>


                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Next Due Date</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ displaydateformat($details['data']->next_due) }}</td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Created By</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ getUsername($details['data']->created_by) }}</td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Created Date</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ displaydateformat($details['data']->created_at) }}</td>
                        </tr>
                    </tbody>
                </table>

                @if (!empty($details['data']->inspection_data))
                    @php
                        $inspectionData = json_decode($details['data']->inspection_data, true);
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
                                    <td style="font-family: sans-serif; font-size: 14px;"><b>Available Quantity</b></td>
                                    <td style="font-family: sans-serif; font-size: 14px;"><b>Expiry Date</b></td>
                                    <td style="font-family: sans-serif; font-size: 14px;"><b>Inspected By</b></td>
                                    <td style="font-family: sans-serif; font-size: 14px;"><b>Remarks</b></td>
                                </tr>

                                @foreach ($inspectionData as $entry)
                                    <tr>
                                        <td>{{ getmedicinename($entry['medicine_id']) }}</td>
                                        <td>{{ $entry['available_quantity'] }}</td>
                                        <td>{{ displaydateformat($entry['expired_date']) }}</td>
                                        <td>{{ ($entry['emp_id']) }}</td>
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
                @if (!empty($details['data']->approval_remarks))
                    <table role="presentation" border="1" cellpadding="0" cellspacing="0"
                        style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; margin-top: 7%;"
                        width="100%">
                        <tbody style="font-family:Nakheel Headline">
                            <tr>
                                <td colspan="4" align="center"
                                    style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Inspection Approval</b>
                                </td>
                            </tr>

                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Status</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top">

                                    @if ($details['data']->inspection_status == 3)
                                        Approved
                                    @elseif ($details['data']->inspection_status == 2)
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
                                    valign="top"> {{ getUsername($details['data']->updated_by) }}</td>
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
                                    valign="top"> {{ $details['data']->approval_remarks }}</td>
                            </tr>
                        </tbody>
                    </table>
                @endif


            </td>
        </tr>
    </table>
@stop
