@extends('admin.layouts.pdf')
@section('title', 'PPE Exemption')
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
                            {{ $value->emp_id }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->emp_name }}
                        </td>

                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getDepartment($value->department) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getUnitname($value->unit) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getCompanyname($value->company) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ Displaydateformat($value->from_date) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ Displaydateformat($value->to_date) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            @if($value->approve_status ==  $ehsstatus)
                                <p>{{ 'User Applied' }}</p>
                            @else
                                {{ removeUnderScore(getStatus($value->approve_status)) }}
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
