@extends('admin.layouts.pdf')
@section('title', 'Medical Requisition Slip Fdo Security gate')
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
                            {{ ($value->doc_no) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ ($value->rev_dt) }}
                        </td>

                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ displaydateformat($value->issue_date) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ ($value->unit_name) }}

                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ ($value->department_name) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getohcrequisitionfloorstatus($value->approve_status) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getusername($value->inspection_created_by) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ Displaydateformat($value->inspection_created_at) }}
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
