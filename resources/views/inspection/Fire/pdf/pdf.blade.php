@extends('admin.layouts.pdf')
@section('title', 'Hydrant And Riser Inspection Pdf')
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
                            {{ displaydateformat($value->date_of_inspection) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ displaydateformat($value->next_due) }}
                        </td>

                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getLocationname($value->location); }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->shift; }}
                        </td> <td style='padding: 7px;border: 0.5px solid'>
                            {{ getUnitname($value->unit); }}
                        </td> <td style='padding: 7px;border: 0.5px solid'>
                            {{ getFrequencyname($value->frequency); }}
                        </td>
                        <td style='padding: 7px; border: 0.5px solid; text-align: center;'>
                            {{ getInspectionStatus($value->inspection_status); }}
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
