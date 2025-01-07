@extends('emails.layouts.email')

@section('content')

@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0"
    style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
            <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">
                Dear {{ $details['name'] }},
            </p>
            <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">
                This is to inform you that your password has been reset by the admin. Please find the updated login details below:
            </p>

            <!-- Employee Details Table -->
            <table role="presentation" border="1" cellpadding="5" cellspacing="0"
                style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
                <tbody>
                    <tr>
                        <td colspan="4" align="center" style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Password Reset Details</b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            Your updated login credentials are as follows:
                        </td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><b>Employee No</b></td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;">{{ $details['employee_id'] }}</td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><b>Date</b></td>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">{{ Displaydateformat($details['created_at']) }}</td>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><b>Time</b></td>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">{{ date('g:i A', strtotime($details['created_at'])) }}</td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><b>Login URL</b></td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                            <a href="{{ admin_url('login') }}">{{ admin_url('login') }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><b>Email ID</b></td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;">{{ $details['email'] }}</td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><b>New Password</b></td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                            {{ $newpass }}
                        </td>
                    </tr>
                </tbody>
            </table>

        </td>
    </tr>
</table>
@endsection

@endsection
