@extends('admin.layouts.pdf')
@section('title', 'Prescribe To Patient PDF')
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
                            {{ $value->emp_name }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->cheif_complaint }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->gender }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getUnitname($value->unit_id) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getDepartment($value->department_id) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ displaydateformat($value->date) }}
                        </td>

                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->time }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getSuggestedBy($value->suggested_by) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->treatment }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            @php
                                $vital_checkup = $value->vital_checkup == 1 ? 'Yes' : 'NO';
                            @endphp
                            {{ $vital_checkup }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getPatientStatus($value->patient_status) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            @php
                                $fitness_certificate = $value->fitness_certificate == 1 ? 'Required' : 'Not Required';
                            @endphp
                            {{ $fitness_certificate }}
                        </td>

                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getusername($value->created_by) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->cancel_remarks }}
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
