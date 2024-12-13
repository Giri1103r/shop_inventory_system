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
                            <b>PPE Exemption</b>
                        </td>
                    </tr>

                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Employee Name</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ $details['emp_name'] }}</td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Employee Id</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ $details['emp_id'] }}</td>
                    </tr>

                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>From Date</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ $details['from_date'] }}</td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>To Date</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ $details['to_date'] }}</td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Department</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ getDepartment($details['department']) }}</td>
                    </tr>

                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Reason</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ $details['reason'] }}</td>
                    </tr>

                </tbody>
            </table>

            {{-- <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 20px;">
                <tr>
                    <td align="center" style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                        <a href="{{ $details['approve_link'] }}" style="background-color: green; border: 1px solid green; border-radius: 5px; box-sizing: border-box; color: #ffffff; display: inline-block; font-size: 14px; font-weight: bold; margin: 0; padding: 10px 20px; text-align: center; text-decoration: none; text-transform: capitalize;">Approve</a>
                        <a href="{{ $details['reject_link'] }}" style="background-color: red; border: 1px solid red; border-radius: 5px; box-sizing: border-box; color: #ffffff; display: inline-block; font-size: 14px; font-weight: bold; margin: 0; margin-left: 10px; padding: 10px 20px; text-align: center; text-decoration: none; text-transform: capitalize;">Reject</a>
                    </td>
                </tr>
            </table> --}}

        </td>
    </tr>
</table>
@stop
