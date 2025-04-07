@extends('admin.layouts.pdf')
@section('title', 'Medicine PDF')
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
                            {{ $value->medicine }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->pack }}
                        </td>

                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->threshold_limit}}
                        </td>
                       
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->remarks}}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            @if ($value->approve_status ==STATUS_OHC_EHS_HEAD_APPROVAL_PENDING)
                            <p>{{ 'Stock Requested' }}</p>
                        @elseif($value->approve_status == STATUS_OHC_EHS_HEAD_APPROVED)
                            <p>{{ 'EHS Head Approval Pending' }}</p>

                        @endif
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            @if ($value->approve_status ==STATUS_OHC_EHS_HEAD_APPROVAL_PENDING)
                            <p>{{ 'EHS Head Approval Pending' }}</p>
                        @elseif($value->approve_status == STATUS_OHC_EHS_HEAD_APPROVED)
                            <p>{{ 'EHS Head Approved' }}</p>


                        @endif
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            @php
                                $status = $value->status == 1 ? 'Active' : 'In-Active';
                            @endphp
                            {{ $status }}
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
