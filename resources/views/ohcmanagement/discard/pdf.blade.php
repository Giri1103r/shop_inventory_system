@extends('admin.layouts.pdf')
@section('title', 'Discard medicine Pdf')
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
                            {{ getMedicineName($value->medicine_id) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ ($value->quantity) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ ($value->remarks) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ displaydateformat($value->discard_date) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getDiscardStatus($value->approve_status) }}
                        </td>

                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getusername($value->created_by) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getusername($value->approved_by) }}
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
