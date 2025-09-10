@extends('admin.layouts.pdf')
@section('title', 'Gemba Walk')
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
               @foreach ($content as $formsData)
    @foreach ($formsData as $value)
        <tr>
            <td style='padding: 7px;border: 0.5px solid;text-align:center'>
                {{ $i }}
            </td>
            <td style='padding: 7px;border: 0.5px solid'>
                {{ $value->gemba_walk_auto_id }}
            </td>
            <td style='padding: 7px;border: 0.5px solid'>
                {{ getLocationname($value->location_id) }}
            </td>
            <td style='padding: 7px;border: 0.5px solid'>
                {{ $value->unit_name }}
            </td>
            <td style='padding: 7px;border: 0.5px solid'>
                {{ getDepartment($value->department_id) }}
            </td>
              <td style='padding: 7px;border: 0.5px solid'>
                {{ ($value->exact_location) }}
            </td>
            <td style='padding: 7px;border: 0.5px solid'>
                {{ Displaydateformat($value->date_of_observation) }}
            </td>
            <td style='padding: 7px;border: 0.5px solid'>
                {{ $value->observation_type_id == 1 ? 'Unsafe Act' : 'Unsafe Condition' }}
            </td>
            <td style='padding: 7px;border: 0.5px solid'>
                {{ getRiskCategory($value->risk_category) }}
            </td>
            <td style='padding: 7px;border: 0.5px solid'>
                {{ $value->description }}
            </td>
            <td style='padding: 7px;border: 0.5px solid'>
                @php
                    $hazards = !empty($value->hazard) ? explode(',', $value->hazard) : [];
                @endphp
                @foreach ($hazards as $hazardId)
                    {{ getGembaWalkHazardName($hazardId) }}@if (!$loop->last), @endif
                @endforeach
            </td>
            <td style='padding: 7px;border: 0.5px solid'>
                {{ $value->capa }}
            </td>
            <td style='padding: 7px;border: 0.5px solid'>
                {{ getGembaStatus($value->gemba_walk_status) }}
            </td>
            <td style='padding: 7px;border: 0.5px solid'>
                @php
                    $responsibility_id = !empty($value->responsibility_id) ? explode(',', $value->responsibility_id) : [];
                @endphp
                @foreach ($responsibility_id as $responsibilityId)
                    {{ getUsername($responsibilityId) }}@if (!$loop->last), @endif
                @endforeach
            </td>
            <td style='padding: 7px;border: 0.5px solid'>
                {{ getUsername($value->inspection_created_by) }}
            </td>
            <td style='padding: 7px;border: 0.5px solid'>
                {{ Displaydateformat($value->inspection_created_date) }}
            </td>
            <td style='padding: 7px;border: 0.5px solid'>
                {{ getUsername($value->verified_by ?? null) }}
            </td>
        </tr>
        @php $i++; @endphp
    @endforeach
@endforeach


            </tbody>
        </table>
        <br>
    </div>

@stop
