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
                            <b>Medicine Stock Request</b>
                        </td>
                    </tr>

                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Medicine Name</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ getMedicinename($details['medicine_id']) }}</td>
                    </tr>


                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>HSN Number</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ gethsn($details['hsn_id']) }}</td>
                    </tr>
                    {{-- <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Threshold Limit</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ $details['threshold_limit'] }}</td>
                    </tr> --}}

                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Expire Date</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{displaydateformat( $details['expire_date']) }}</td>
                    </tr>

                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Quantity</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ $details['quantity'] }}</td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Batch Number</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ $details['batch_number'] }}</td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Rate</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ $details['rate'] }}</td>
                    </tr>
                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Created By</b>
                        </td>
                        <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                            valign="top"> {{ getUsername($details['created_by']) }}</td>
                    </tr>
                </tbody>
            </table>

        </td>
    </tr>
</table>
@stop
