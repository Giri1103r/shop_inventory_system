@extends('admin.layouts.pdf')
@section('title', 'Fire Equipment Monthly Phsyical Inspection Pdf')
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
                            {{ Displaydateformat($data->inspection_date)}}
                        </td>

                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ Displaydateformat($data->next_due)}}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getObservationStatus($data->inspection_status) }}
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
