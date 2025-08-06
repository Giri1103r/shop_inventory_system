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
                            @if (isset($details['data']->date_of_inspection))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Date of Inspection</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ displaydateformat($details['data']->date_of_inspection) }}</td>
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
                            @if (isset($details['data']->location))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Location Name</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ getLocationname($details['data']->location) }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if (isset($details['data']->resource_code))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Resourse Code</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ $details['data']->resource_code }}</td>
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
                                valign="top"> {{ DisplayDateformat($details['data']->created_at) }}</td>
                        </tr>
                        <tr>
                            @if (isset($details['data']->verified_by))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Verified By</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ getUsername($details['data']->verified_by) }}</td>
                            @endif
                        </tr>
                       
                        <tr>
                            @if (isset($details['data']->verified_date))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Level Two Manager Verified at</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ displaydateformat($details['data']->verified_date) }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if (isset($details['data']->remarks))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Remarks By Level Two Manager</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ $details['data']->remarks }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if (isset($details['data']->l2_manager_verified_by))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>EHS Head</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ getUsername($details['data']->l2_manager_verified_by) }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if (isset($details['data']->approved_date))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>EHS Head Approved at</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ displaydateformat($details['data']->approved_date) }}</td>
                            @endif
                        </tr>

                        <tr>
                            @if (isset($details['data']->level_two_manager_remarks))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Remarks By EHS Head</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ ($details['data']->level_two_manager_remarks) }}</td>
                            @endif
                        </tr>
                    </tbody>
                </table>

            </td>
        </tr>
    </table>
@stop
