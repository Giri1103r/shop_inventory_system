@extends('admin.layouts.pdf')
@section('title', 'Worker PDF')
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
                            {{ $value->mobile_no }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->company_name }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->location_name }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->unit_name }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->department_name }}
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
