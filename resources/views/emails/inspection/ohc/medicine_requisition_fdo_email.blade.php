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
                                <b>Unit Name</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ getUnitname($details['data']->unit) }}</td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Department Name</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ getDepartment($details['data']->department) }}</td>
                        </tr>
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                <b>Created By</b>
                            </td>
                            <td colspan="3" style="font-family: sans-serif; font-size: 14px; vertical-align: top;"
                                valign="top"> {{ getUsername($details['data']->created_by) }}</td>
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

                <table role="presentation" border="1" cellpadding="0" cellspacing="0"
                style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; margin-top:10%"
                width="100%">

                <thead>
                    <tr>
                        <th style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Medicine Name</b>
                        </th>
                        <th style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Quantity</b>
                        </th>
                        <th style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                            <b>Remarks</b>
                        </th>
                    </tr>
                </thead>
                <tbody style="font-family:Nakheel Headline">
                    @if (!empty($details['checklist']) && is_iterable($details['checklist']))
                        @foreach ($details['checklist'] as $checklistItem)
                            <tr>
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    {{ getMedicinename($checklistItem->medicine_id) }}
                                </td>
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    {{ $checklistItem->quantity }}
                                </td>
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                    {{ $checklistItem->remarks }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" align="center" style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                                No medicine details available
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>



            </td>
        </tr>
    </table>
@stop
