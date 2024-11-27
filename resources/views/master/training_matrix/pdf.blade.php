@extends('admin.layouts.pdf')
@section('title', 'Training Matri PDF')
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
                            {{ $value->topic_name }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->emp_name }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->training_offered_for == 1 ? 'Worker' : 'Executive' }}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->unit_name}}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->department_name}}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->mode_of_training == 1 ? 'Online' : 'Offline'}}
                        </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                            {{ $value->training_evaluation == 1 ? 'Yes' : 'No' }}
                        </td>
                      
                        <td style='padding: 7px;border: 0.5px solid'>
                            @php
                                $status = $value->status == 1 ? 'Active' : 'In-Active';
                            @endphp
                            {{ $status }}
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
