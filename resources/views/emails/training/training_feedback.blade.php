@extends('emails.layouts.email')
@section('content')
    <table role="presentation" border="0" cellpadding="0" cellspacing="0"
        style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
        <tr>
            <td style="font-family: Arial, sans-serif; font-size: 14px; vertical-align: top; padding: 20px;" valign="top">
                <p
                    style="font-family: Arial, sans-serif; font-size: 16px; font-weight: bold; margin: 0; margin-bottom: 15px;">
                    Dear {{ $details['emp_name'] }},
                </p>
                <p style="font-family: Arial, sans-serif; font-size: 14px; margin: 0; margin-bottom: 15px;">
                    We value your feedback for the training session you recently attended. Please click on the link below to
                    provide your feedback:
                </p>

                <a href="{{ $details['link'] }}" target="_blank">
                    <button type="button"
                        style="background-color: #0986e0; color: white; border: none; padding: 10px 20px; font-size: 16px; border-radius: 5px; cursor: pointer;">
                        Give Feedback
                    </button>
                </a>

                <p style="font-family: Arial, sans-serif; font-size: 14px; margin: 0; margin-top: 20px;">
                    Your feedback is valuable to us and will help improve future training sessions.
                </p>
                <p style="font-family: Arial, sans-serif; font-size: 14px; margin: 0; margin-top: 20px;">
                    Thank you for your time and effort!
                </p>
                <p style="font-family: Arial, sans-serif; font-size: 14px; margin: 0; margin-top: 20px; font-weight: bold;">
                    Regards,<br>
                    {{ config('app.name') }} Team
                </p>
            </td>
        </tr>
    </table>

@stop
