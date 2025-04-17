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
                                valign="top"> {{ $details['data']->doc_no }}</td>
                        </tr>


                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Issue Date</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ displaydateformat($details['data']->issue_date) }}</td>
                        </tr>

                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Revision & Date</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ $details['data']->rev_dt }}</td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Created By</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ getUsername($details['data']->created_by) }}</td>
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
                            @if (isset($details['data']->l1_manager_verified_by))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Level One Manager</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ getUsername($details['data']->l1_manager_verified_by) }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if (isset($details['data']->l2_manager_verified_by))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Level Two Manager</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ getUsername($details['data']->l2_manager_verified_by) }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if (isset($details['data']->approved_by))
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    <b>Approved By</b>
                                </td>
                                <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                    valign="top"> {{ getUsername($details['data']->approved_by) }}</td>
                            @endif
                        </tr>
                    </tbody>
                </table>

            </td>
        </tr>
    </table>
@stop
