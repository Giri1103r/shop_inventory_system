@extends('admin.layouts.pdf')
@section('title', 'RRAA Pdf')
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
                            {{ getCategoryname($value->category) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ isset($value->ohs_compliance_index) ? $value->ohs_compliance_index : '' }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ getFrequencyname($value->frequency) }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ isset($value->scope) ? $value->scope : '' }}
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
