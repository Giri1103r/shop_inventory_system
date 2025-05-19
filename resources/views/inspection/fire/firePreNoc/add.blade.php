@extends('admin.layouts.admin')
@section('title', 'Fire Pre Noc Checklist')
@section('pageurl', admin_url('fire/pre-noc/checklist/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">

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
                                    <x-button-back href="{{ admin_url('fire/pre-noc/checklist/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="firepreNocadd"
                                        action="{{ admin_url('fire/pre-noc/checklist/add/submit') }}" autocomplete="off"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="document_reference_id"
                                            value="{{ encryptId($staticDocno->id) }}">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Inspection ID</label>
                                                    <input type="text" name="inspection_id" id = "inspection_id"
                                                        class="form-control" readonly
                                                        value="{{ getSequence('FirePreNoc') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 form-input">
                                                <label class="form-label">Doc. No</label>
                                                <input type="text" class="form-control" name="doc_no" id="doc_no"
                                                    readonly value="{{ $staticDocno->doc_no }}">
                                            </div>

                                            <div class="col-md-4 form-input">
                                                <label class="form-label">Issue Dt.</label>

                                                <div class="input-group date form-input custom-height">
                                                    <input type="text" class="form-control" name="issue_date"
                                                        id="issue_date" readonly
                                                        value="{{ Displaydateformat($staticDocno->issue_date) }}">
                                                    <div class="input-group-addon input-group-text">
                                                        <span class="fa fa-calendar"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 form-input mb-2">
                                                <label class="form-label">Rev. & Dt.</label>
                                                <input type="text" class="form-control" name="rev_dt" id="rev_dt"
                                                    readonly value="{{ $staticDocno->rev_dt }}">
                                            </div>
                                            <div class="col-md-4 form-input mb-2">
                                                <label class="form-label require">ब्लाक आधारित विवरण (Block based
                                                    statement)</label>
                                                <textarea type="text" class="form-control" name="block_based_statement" id="block_based_statement"></textarea>
                                            </div>
                                            <div class="col-md-4 form-input mb-2">
                                                <label class="form-label require">ब्लाक(Block)</label>
                                                <textarea type="text" class="form-control" name="block" id="block"></textarea>
                                            </div>

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
                                                        $i = 1;
                                                        $groupIndex = 0;

                                                    @endphp
                                                    @foreach ($checklist_details as $key => $details)
                                                        @php
                                                            $rowCount = count($details);
                                                        @endphp
                                                        @foreach ($details as $index => $checklist)
                                                            <tr>

                                                                @if ($index == 0 && $groupIndex > 0)
                                                                    <td colspan="6"
                                                                        style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold;text-align: center;">
                                                                        {{ $checklist->subcategory_name }}
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                            <tr>
                                                                <td
                                                                    style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold;">
                                                                    {{ $i }}
                                                                </td>

                                                                <td colspan="2"
                                                                    style="border: 1px solid black; padding: 8px;">
                                                                    {{ $checklist->checklist_name }}
                                                                </td>
                                                                <td
                                                                    style="border: 1px solid black; padding: 8px; text-align: center;">
                                                                    <textarea name="remarks[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}]" cols="5" rows="3"
                                                                        class="form-control"></textarea>
                                                                </td>
                                                                @php
                                                                    $i++;
                                                                @endphp
                                                            </tr>
                                                        @endforeach
                                                        @php $groupIndex++; @endphp
                                                    @endforeach
                                                </tbody>
                                            </table>

                                        </div>

                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('fire/pre-noc/checklist/list') }}"></x-button-cancel>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    @stop

    @push('script')
        <script type="text/javascript" nonce="projectcab">
            $(document).ready(function() {
                $('#resetform').on('click', function(e) {
                    e.preventDefault();
                    location.reload();
                });
                flatpickr("#date_of_inspection", {
                    dateFormat: "d-m-Y",
                });
                flatpickr("#date", {
                    dateFormat: "d-m-Y",
                });

            });
            $(function() {
                $('#firepreNocadd').validate({
                    rules: {

                        block_based_statement: {
                            required: true,
                        },
                        block: {
                            required: true,
                        },

                    },
                    messages: {
                        block_based_statement: {
                            required: "{{ __('ब्लाक आधारित विवरण (Block based statement) is Required') }}",
                        },
                        block: {
                            required: "{{ __('ब्लाक(Block) is Required') }}",
                        },

                    },
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-input').append(error);
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    },
                    submitHandler: function(form) {
                        console.log('test');
                        form.submit();

                    },
                    invalidHandler: function(event, validator) {
                        var errors = validator.numberOfInvalids();
                        console.log(errors + " field(s) are invalid");
                        validator.errorList.forEach(function(error) {
                            console.log("Field: " + error.element.name + ", Error: " + error
                                .message);
                        });
                    }
                });
            });
        </script>
    @endpush
