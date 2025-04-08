@extends('admin.layouts.admin')
@section('title', 'Fire Pre Noc Checklist Show')
@section('pageurl', admin_url('fire/pre-noc/checklist/list'))

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
                                        href="{{ admin_url('fire/pre-noc/checklist/list') }}"></x-button-back>
                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Fire Pre Noc Checklist</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Inspection ID</label>
                                        <div class="view_data">
                                            {{ isset($fireNoc->inspection_id) ? $fireNoc->inspection_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Doc. No</label>
                                        <div class="view_data">
                                            {{ isset($fireNoc->doc_no) ? $fireNoc->doc_no : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Issue Dt.</label>
                                        <div class="view_data">
                                            {{ displayDateformat($fireNoc->issue_date) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Rev. & Dt.</label>
                                        <div class="view_data">
                                            {{ $fireNoc->rev_dt }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">ब्लाक आधारित विवरण (Block based
                                            statement)</label>
                                        <div class="view_data">
                                            {{ $fireNoc->block_based_statement }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">ब्लाब्लाक(Block)</label>
                                        <div class="view_data">
                                            {{ $fireNoc->block }}
                                        </div>
                                    </div>
                                    @php
                                        $user_response = json_decode($fireNoc->checklist, true);
                                    @endphp

                                    <table class="container p-5">
                                        <thead>
                                            <tr>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    क्रमांक (Serial Number)
                                                </th>

                                                <th colspan="2"
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    जाँच बिंदु (Check Point)
                                                </th>

                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    विवरण (Detail)
                                                </th>
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
                                        
                                                <tr>
                                                    @if (!in_array($sectionName, $displayedSections))
                                                        <td colspan="5"
                                                            style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold; text-align: center;">
                                                            {{ $sectionName }}
                                                        </td>
                                                        @php $displayedSections[] = $sectionName; @endphp
                                                    @endif
                                                </tr>
                                        
                                                <tr>
                                                    <td style="border: 1px solid black; padding: 8px; font-weight: bold; text-align: center;">
                                                        {{ $srNo }}
                                                    </td>
                                        
                                                    <td colspan="2" style="border: 1px solid black; padding: 8px;">
                                                        {{ GetChecklistTypeDate($checklistId) }}
                                                    </td>
                                        
                                                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                                        {{ $data['remarks'] ?? '-' }}
                                                    </td>
                                                </tr>
                                        
                                                @php
                                                    $srNo++;
                                                @endphp
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
