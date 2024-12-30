@extends('admin.layouts.pdf')
@section('title', 'SafetyPermit')
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
                            {{ $value->permit_id }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getUnitname($value->unit_id) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ Displaydateformat($value->date) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->exact_location_job }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->to_status }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->status_name }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getUsername($value->verified_by) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getUsername($value->approved_by) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getUsername($value->created_by) }}
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
