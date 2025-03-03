@extends('admin.layouts.pdf')
@section('title', 'Medicine Receiving Pdf')
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
                            {{ getmedicinename($value->medicine_id) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ gethsn($value->hsn_id )}}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->pack }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->quantity }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->batch_number }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->rate }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ displaydateformat($value->expire_date) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->vendor_name }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            @if ($value->approve_status ==STATUS_OHC_EHS_VERIFICATION_PENDING)
                            <p>{{ 'Stock Requested' }}</p>
                        @elseif($value->approve_status == STATUS_OHC_L1_EHS_VERIFICATION_PENDING)
                            <p>{{ 'EHS Officer Verification Pending' }}</p>
                        @elseif($value->approve_status == STATUS_OHC_AGM_APPROVAL_PENDING)
                            <p>{{ 'L1 EHS Officer Verification Pending' }}</p>
                        @elseif($value->approve_status == STATUS_OHC_OPEN)
                            <p>{{ 'EHS Head Approval Pending' }}</p>
                        @elseif($value->approve_status == STATUS_OHC_CLOSE)
                            <p>{{ 'Open' }}</p>

                        @endif
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            @if ($value->approve_status ==STATUS_OHC_EHS_VERIFICATION_PENDING)
                            <p>{{ 'EHS Officer Verification Pending' }}</p>
                        @elseif($value->approve_status == STATUS_OHC_L1_EHS_VERIFICATION_PENDING)
                            <p>{{ 'L1 EHS Officer Verification Pending' }}</p>
                        @elseif($value->approve_status == STATUS_OHC_AGM_APPROVAL_PENDING)
                            <p>{{ 'EHS Head Approval Pending' }}</p>
                        @elseif($value->approve_status == STATUS_OHC_OPEN)
                            <p>{{ 'Open' }}</p>
                        @elseif($value->approve_status == STATUS_OHC_CLOSE)
                            <p>{{ 'Close' }}</p>

                        @endif
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
