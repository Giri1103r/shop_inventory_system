@extends('admin.layouts.pdf')
@section('title', 'Floor Stretcher Inspection Pdf')
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
                @foreach ($content as $key => $data)
                    <tr>
                        <td style='padding: 7px;border: 0.5px solid;text-align:center'>
                            {{ $i }}
                        </td>

                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ Displaydateformat($data->issue_date)}}
                        </td>

                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getUnitname($data->unit)}}
                        </td>

                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getFrequencyname($data->frequency) }}
                        </td>

                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getShiftname($data->shift) }}
                        </td>
                        
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getusername($data->created_by) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ Displaydateformat($data->created_at) }}
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
