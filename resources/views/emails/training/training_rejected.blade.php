@extends('emails.layouts.email')
@section('content')
    <table role="presentation" border="0" cellpadding="0" cellspacing="0"
        style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
        <tr>
            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                <p style="font-family: sans-serif; font-size: 16px; font-weight: bold; margin: 0; margin-bottom: 15px;">
                    Dear {{ $details['name'] }},
                </p>
                <p style="font-family: sans-serif; font-size: 14px; margin: 0; margin-bottom: 15px;">
                    We regret to inform you that the following training session has been rejected by the Vice
                    President.Please find the details below:

                </p>

                <table role="presentation" border="1" cellpadding="8" cellspacing="0"
                    style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; border: 1px solid #ddd;"
                    width="100%">
                    <tbody>
                        <tr style="background-color: #f2f2f2;">
                            <td colspan="2" align="center"
                                style="font-family: sans-serif; font-size: 14px; font-weight: bold; vertical-align: top;"
                                valign="top">
                                Training Details
                            </td>
                        </tr>

                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; font-weight: bold; vertical-align: top;"
                                valign="top">
                                From Date:
                            </td>

                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                {{ Displaydateformat($details['from_date']) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; font-weight: bold; vertical-align: top;"
                                valign="top">
                                To Date:
                            </td>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                {{ Displaydateformat($details['to_date']) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; font-weight: bold; vertical-align: top;"
                                valign="top">
                                Start Time:
                            </td>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                {{ Displaytimeformat($details['start_time']) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; font-weight: bold; vertical-align: top;"
                                valign="top">
                                End Time:
                            </td>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                {{ Displaytimeformat($details['end_time']) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; font-weight: bold; vertical-align: top;"
                                valign="top">
                                Training Topic:
                            </td>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                {{ $details['topic_name'] }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; font-weight: bold; vertical-align: top;"
                                valign="top">
                                Unit:
                            </td>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                {{ $details['unit'] }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; font-weight: bold; vertical-align: top;"
                                valign="top">
                                Department:
                            </td>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                {{ $details['department'] }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; font-weight: bold; vertical-align: top;"
                                valign="top">
                                Venue:
                            </td>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                {{ $details['venue'] }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; font-weight: bold; vertical-align: top;"
                                valign="top">
                                Rejection Remarks:
                            </td>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                {{ $details['remark'] }}
                            </td>
                        </tr>

                    </tbody>
                </table>
            </td>
        </tr>
    </table>
@stop
