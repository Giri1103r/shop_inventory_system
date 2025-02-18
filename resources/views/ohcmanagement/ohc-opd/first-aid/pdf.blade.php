@extends('admin.layouts.pdf')
@section('title', 'First Aid')
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
                            {{ displaydateformat($value->date_of_incident )}}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->time_of_incident }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->treatment_provided }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ ($value->treatment_start_time) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ ($value->treatment_end_time) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ ($value->first_aider_name) }}
                        </td>


                        <td style='padding: 7px;border: 0.5px solid'>
                            @php
                                $follow_up_required = $value->follow_up_required == 1 ? 'Yes' : 'NO';
                            @endphp
                            {{ $follow_up_required }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ ($value->referred_to) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->remarks }}
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
                            {{ displaydateformat($value->created_at) }}
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
