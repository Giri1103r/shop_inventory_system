@extends('emails.layouts.email')
@section('content')
    <table role="presentation" border="0" cellpadding="0" cellspacing="0"
        style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
        <tr>
            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">
                    Dear {{ $details['name'] }},</p>
                <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">
                            We wanted to let you know that the password for your account associated with Indialand has been Successfully changed !!!.<br>
                            If you did not initiate this change, it is important to secure your account immediately.  please contact our Admin.<br><br>
                            Thank you for your attention to this matter.
                </p>
            </td>
        </tr>
    </table>
@stop
