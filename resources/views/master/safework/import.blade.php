@extends('admin.layouts.admin')
@section('title', 'Safe Work Instruction Import')
@section('pageurl', admin_url('safeworkmaster/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h2 class="text-black">{{ __('administration.employee') }}</h2> --}}

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
                                {{-- <h4 class="card-title">{{ __('administration.employee_import') }}</h4> --}}
                                <div class="align-back-import-btc">
                                    <x-button-download href="{{ admin_url('ptw/safeworkmaster/sampledownload') }}"></x-button-download>
                                    <x-button-back href="{{ admin_url('ptw/safeworkmaster/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">

                                <div class="basic-form">
                                    <form method="POST" id="companyimport" enctype="multipart/form-data"
                                        action="{{ admin_url('ptw/safeworkmaster/import/submit') }}">
                                        @csrf
                                        <div class="row">
                                            <div class="mb-3 col-md-6 form-input">
                                                <label class="form-label required">File</label>
                                                <input type="file"  name="safe_work_upload" class="form-control"  placeholder="">
                                            </div>
                                        </div>
                                        <hr>

                                        <div class="">
                                            <x-button-submit></x-button-submit>
                                            <x-button-cancel></x-button-cancel>

                                        </div>

                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@push('script')
    <script type="text/javascript">
        $(function() {
            $('#companyimport').validate({
                rules: {

                    company_upload: {
                        required: true,
                        extension: "xlsx",
                        filesize: 5242880,
                    },
                },
                messages: {
                    company_upload: {
                        required: "Please upload a file",
                        extension: "Please upload an Excel file (.xlsx)",
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
            });
        });
    </script>
@endpush
