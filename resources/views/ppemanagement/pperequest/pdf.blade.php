@extends('admin.layouts.pdf')
@section('title', 'PPE Request')
@section('content')

    <div style="width:100%;">
        <table class="table" style="width:100%;border: 0.5px solid;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    @foreach ($header as $key => $value)
                        <td style='padding: 7px;border: 0.5px solid;font-weight:bold;text-align:center;'>
                            {{ $value }}
                        </td>
                    @endforeach
                </tr>
            </thead>

            <tbody>

                @php
                    $i = 1;
                @endphp

                @foreach ($content as $key => $value)
                    <tr>
                        <td style='padding: 7px; border: 0.5px solid; text-align:center'>
                            {{ $i }}
                        </td>

                        <td style='padding: 7px; border: 0.5px solid'>
                            {{ $value->emp_id }}
                        </td>
                        <td style='padding: 7px; border: 0.5px solid'>
                            {{ $value->emp_name }}
                        </td>

                        <td style='padding: 7px; border: 0.5px solid'>
                            {{ getItemCode($value->item_code) }}
                        </td>
                        <td style='padding: 7px; border: 0.5px solid'>
                            {{ ($value->ppe_name) }}
                        </td>
                        <td style='padding: 7px; border: 0.5px solid'>
                            {{ getCompanyname($value->company_id) }}
                        </td>
                        <td style='padding: 7px; border: 0.5px solid'>
                            {{ getLocationname($value->location_id) }}
                        </td>
                        <td style='padding: 7px; border: 0.5px solid'>
                            {{ getUnitname($value->unit_id) }}
                        </td>
                        <td style='padding: 7px; border: 0.5px solid'>
                            {{ getDepartment($value->department) }}
                        </td>

                        <td style='padding: 7px; border: 0.5px solid'>
                            @if ($value->approve_status == $hodstatus)
                                <p>{{ 'User Applied' }}</p>
                            @elseif($value->approve_status == STATUS_HOD_APPROVED)
                                <p>{{ 'HOD Approval Pending' }}</p>
                            @elseif($value->approve_status == STATUS_HOD_REJECTED)
                                <p>{{ 'HOD Approval Pending' }}</p>
                            @elseif($value->approve_status == STATUS_EHS_APPROVAL_PENDING)
                                <p>{{ 'HOD Approved' }}</p>
                            @elseif($value->approve_status == STATUS_EHS_APPROVED)
                                <p>{{ 'EHS Officer Approval Pending' }}</p>
                            @elseif($value->approve_status == STATUS_EHS_REJECTED)
                                <p>{{ 'EHS Officer Approval Pending' }}</p>
                                @elseif($value->approve_status == STATUS_ISSUED)
                                <p>{{ 'EHS Officer Approved' }}</p>
                            @endif
                        </td>

                        <td style='padding: 7px; border: 0.5px solid'>
                            @if ($value->approve_status == $hodstatus)
                                <p>{{ 'HOD Approval Pending' }}</p>
                            @elseif($value->approve_status == STATUS_HOD_APPROVED)
                                <p>{{ 'Hod Approved' }}</p>
                            @elseif($value->approve_status == STATUS_HOD_REJECTED)
                                <p>{{ 'Hod Rejected' }}</p>
                                @elseif($value->approve_status == STATUS_EHS_APPROVAL_PENDING)
                                <p>{{ 'EHS Officer Approval Pending' }}</p>
                            @elseif($value->approve_status == STATUS_EHS_APPROVED)
                                <p>{{ 'EHS Officer Approved' }}</p>
                            @elseif($value->approve_status == STATUS_EHS_REJECTED)
                                <p>{{ 'EHS Officer Rejected' }}</p>
                            @else
                                {{ removeUnderScore(getStatus($value->approve_status)) }}
                            @endif
                        </td>

                        <td style='padding: 7px; border: 0.5px solid'>
                            {{ getusername($value->created_by) }}
                        </td>
                        <td style='padding: 7px; border: 0.5px solid'>
                            {{ Displaydateformat($value->created_at) }}
                        </td>
                    </tr>
                    @php
                        $i++;
                    @endphp
                @endforeach

            </tbody>
        </table>
        <br>
    </div>

@stop
