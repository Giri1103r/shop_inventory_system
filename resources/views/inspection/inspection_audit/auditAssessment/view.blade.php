@extends('admin.layouts.admin')
@section('title', '6S Audit Assessment Show')
@section('pageurl', admin_url('audit/assessment/list'))

@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Company Show') }}</h4> --}}

        </div>

    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <!-- row -->
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('audit/assessment/list') }}"></x-button-back>
                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">6S Audit Assessment</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Audit ID</label>
                                        <div class="view_data">
                                            {{ isset($audit_assessment->audit_id) ? $audit_assessment->audit_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Name Of The Shop Floor</label>
                                        <div class="view_data">
                                            {{ isset($audit_assessment->floor_name) ? $audit_assessment->floor_name : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Date Of Audit</label>
                                        <div class="view_data">
                                            {{ isset($audit_assessment->audit_date) ? $audit_assessment->audit_date : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Shift</label>
                                        <div class="view_data">
                                            {{ getShiftname(isset($audit_assessment->shift_id) ? $audit_assessment->shift_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Floor Executive on Duty</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($audit_assessment->floor_executive) ? $audit_assessment->floor_executive : '') }}

                                        </div>
                                    </div>
                                    @php
                                        $user_response = json_decode($audit_assessment->checklist, true);
                                    @endphp
                                    @if (!empty($user_response))
                                        <table class="container p-5">
                                            <thead>
                                                <tr>


                                                    <th colspan="5"
                                                        style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                        Check Points
                                                    </th>

                                                    <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;"
                                                        class="require">
                                                        YES/NO/NA
                                                    </th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $srNo = 1; @endphp

                                                @foreach ($user_response as $subcategory => $questions)
                                                    @php
                                                        $rowCount = count($questions);
                                                        $firstRow = true;
                                                    @endphp
                                                    @foreach ($questions as $questionId => $answer)
                                                        <tr>
                                                            @if ($firstRow)
                                                                <td rowspan="{{ $rowCount }}"
                                                                    style="border: 1px solid black; padding: 8px; font-weight: bold;">
                                                                    {{ GetSubChecklistTypeName($subcategory) }}
                                                                </td>
                                                                @php
                                                                    $srNo++;
                                                                    $firstRow = false;
                                                                @endphp
                                                            @endif
                                                            <td colspan="4"
                                                                style="border: 1px solid black; padding: 8px;">
                                                                {{ GetChecklistTypeDate($questionId) }}
                                                            </td>
                                                            <td
                                                                style="border: 1px solid black; padding: 8px; text-align: center;">
                                                                @if ($answer == 'YES')
                                                                    <span style="color: green; font-size: 20px;">✓</span>
                                                                @elseif ($answer == 'NO')
                                                                    <span style="color: red; font-size: 20px;">X</span>
                                                                @elseif ($answer == 'N/A')
                                                                    <span
                                                                        style="color: rgb(191, 212, 4); font-size: 20px;">X</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach


                                            </tbody>
                                        </table>
                                    @else
                                        <p>No Data is Available</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>

@stop
