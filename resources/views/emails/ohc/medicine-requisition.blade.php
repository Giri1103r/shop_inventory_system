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
                                <b>Medicine Request</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Unit</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ getUnitname($emailDetails['unit_id']) }}</td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Department</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ getDepartment($emailDetails['department_id']) }}</td>
                        </tr>


                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Date</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ date('d-m-Y ') }}</td>
                        </tr>

                    </tbody>
                </table>
                <table role="presentation" border="1" cellpadding="0" cellspacing="0"
                    style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; margin-top:10%"
                    width="100%">
                    <thead>
                        <tr>
                            <th style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Medicine Name</b>
                            </th>
                            <th style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Available Quantity</b>
                            </th>
                            <th style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Quantity</b>
                            </th>

                        </tr>
                    </thead>
                    <tbody style="font-family:Nakheel Headline">
                        @foreach ($medicineDetails as $data)
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    {{ (getMedicinename($data['medicine_id'])) }}
                                </td>
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    {{ $data['available_quantity'] }}
                                </td>
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    {{ $data['quantity'] }}
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>


            </td>
        </tr>
    </table>
@stop
