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
                            <b>Safety Permit</b>
                        </td>
                    </tr>

                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Permit ID</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ $details['permit_id'] }}</td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Unit</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ getUnitname($details['unit_id']) }}</td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Date</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ $details['date'] }}</td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Time(From)</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ $details['time_from'] }}</td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Time(To)</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ $details['time_to'] }}</td>
                    </tr>

                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Exact location of job</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ $details['exact_location_job'] }}</td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Job Location & Area</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ $details['job_location_area'] }}</td>
                    </tr>

                    
                </tbody>
            </table>
             <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 20px;">
                <tr>
                    <td align="center" style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                        <a href="{{ $details['extension_link'] }}" style="background-color: green; border: 1px solid green; border-radius: 5px; box-sizing: border-box; color: #ffffff; display: inline-block; font-size: 14px; font-weight: bold; margin: 0; padding: 10px 20px; text-align: center; text-decoration: none; text-transform: capitalize;">Permit Extension</a>
                       
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
@stop
