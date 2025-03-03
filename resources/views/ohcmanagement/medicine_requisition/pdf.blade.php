@extends('admin.layouts.pdf')
@section('title', 'Medicine Requisition Pdf')
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
                        <td style='padding: 7px;border: 0.5px solid;text-align:center'>
                            {{ $i }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->req_id }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getUnitname($value->unit_id) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getDepartment($value->department_id) }}
                        </td>

                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ displaydateformat($value->request_date) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            @if ($value->approve_status ==STATUS_OHC_PARAMEDICS_APPROVAL_PENDING)
                            <p>{{ 'Stock Requested' }}</p>
                        @elseif($value->approve_status == STATUS_OHC_PARAMEDICS_APPROVED)
                            <p>{{ 'Paramedics Approval Pending' }}</p>
                        @elseif($value->approve_status == STATUS_OHC_PARAMEDICS_REJECTED)
                            <p>{{ 'Paramedics Approval Pending' }}</p>
                        @elseif($value->approve_status == STATUS_OHC_OPEN)
                            <p>{{ 'Paramedics Approved' }}</p>
                            @elseif($value->approve_status == STATUS_OHC_CLOSE)
                            <p>{{ 'Open' }}</p>
                        @endif
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            @if ($value->approve_status ==STATUS_OHC_PARAMEDICS_APPROVAL_PENDING)
                            <p>{{ 'Paramedics Approval Pending' }}</p>
                        @elseif($value->approve_status == STATUS_OHC_PARAMEDICS_APPROVED)
                            <p>{{ 'Paramedics Approved' }}</p>
                        @elseif($value->approve_status == STATUS_OHC_PARAMEDICS_REJECTED)
                            <p>{{ 'Paramedics Rejected' }}</p>
                            @elseif($value->approve_status == STATUS_OHC_OPEN)
                            <p>{{ 'Open' }}</p>

                        @elseif($value->approve_status == STATUS_OHC_CLOSE)
                            <p>{{ 'Close' }}</p>

                        @endif
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getusername($value->created_by) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
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
