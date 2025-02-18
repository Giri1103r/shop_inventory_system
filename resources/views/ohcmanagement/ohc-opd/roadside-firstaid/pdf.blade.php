@extends('admin.layouts.pdf')
@section('title', 'Road Side First Aid')
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
                            {{ $value->name }}
                        </td>

                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ displaydateformat($value->date_of_incident) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->time_of_incident }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->location_of_incident }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getPersonalCondition($value->person_condtion) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->first_aid_provided }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            @php
                                $transport_to_medical_facility =
                                    $value->transport_to_medical_facility == 1 ? 'Yes' : 'NO';
                            @endphp
                            {{ $transport_to_medical_facility }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->first_aider_name }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->transport_method }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            @php
                                $incident_report_filled = $value->incident_report_filled == 1 ? 'Yes' : 'NO';
                            @endphp
                            {{ $incident_report_filled }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->remarks }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            @php
                                $status = $value->status == 1 ? 'Active' : 'In-active';
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
