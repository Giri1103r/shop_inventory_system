@extends('admin.layouts.admin')
@section('title', 'Inter Unit Monthly Audit')
@section('pageurl', admin_url('audit/audit/inter-unit-audit/checklist/list'))

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
                                    <x-button-back
                                        href="{{ admin_url('audit/inter-unit-audit/checklist/list') }}"></x-button-back>
                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Inter Unit Monthly Audit</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Audit ID</label>
                                        <div class="view_data">
                                            {{ isset($inter_unit_audit->audit_id) ? $inter_unit_audit->audit_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Name of Safety Officer</label>
                                        <div class="view_data">
                                            {{ isset($inter_unit_audit->safety_officer) ? $inter_unit_audit->safety_officer : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Date Of Audit</label>
                                        <div class="view_data">
                                            {{ displayDateformat($inter_unit_audit->audit_date) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Unit</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($inter_unit_audit->unit_id) ? $inter_unit_audit->unit_id : '') }}
                                        </div>
                                    </div>

                                    @php
                                        $user_response = json_decode($inter_unit_audit->checklist, true);

                                    @endphp

                                    <table class="container p-5">
                                        <thead>
                                            <tr>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Sr. No</th>
                                                <th colspan="2"
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Check Points</th>

                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Ok/Not Ok</th>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $srNo = 1;
                                                $displayedSections = [];
                                            @endphp
                                            @foreach ($user_response as $checklistId => $data)
                                                @php
                                                    $sectionName = GetSubChecklistTypeName($data['sub_type_id']);
                                                @endphp

                                                @if (!in_array($sectionName, $displayedSections))
                                                    <tr>
                                                        <td colspan="5"
                                                            style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold; text-align: center;">
                                                            {{ $sectionName }}
                                                        </td>
                                                    </tr>
                                                    @php $displayedSections[] = $sectionName; @endphp
                                                @endif

                                                <tr>
                                                    <td
                                                        style="border: 1px solid black; padding: 8px; font-weight: bold; text-align: center;">
                                                        {{ $srNo }}
                                                    </td>
                                                    <td colspan="2" style="border: 1px solid black; padding: 8px;">
                                                        {{ GetChecklistTypeDate($checklistId) }}
                                                    </td>
                                                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                                        @if (($data['response'] ?? '') == 'Ok')
                                                            <span style="color: green; font-size: 20px;">Ok</span>
                                                        @elseif (($data['response'] ?? '') == 'Not Ok')
                                                            <span style="color: red; font-size: 20px;">Not Ok</span>
                                                        @else
                                                            <span style="color: red; font-size: 20px;">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                                        {{ $data['remarks'] ?? '-' }}
                                                    </td>
                                                </tr>

                                                @php $srNo++; @endphp
                                            @endforeach
                                        </tbody>
                                    </table>
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
