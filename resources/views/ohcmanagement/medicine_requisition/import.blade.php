@extends('admin.layouts.admin')
@section('title', 'Medicine Requisition')
@section('pageurl', admin_url('ohc/medicine-requisition/'))


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
                                <div class="d-flex justify-content-end p-2 gap-2">
                                    <x-button-download
                                        href="{{ admin_url('ohc/medicine-requisition/sample-download') }}"></x-button-download>
                                    <x-button-back href="{{ admin_url('ohc/medicine-requisition/add') }}"></x-button-back>
                                </div>

                            </div>

                            <div class="card-body ">

                                <div class="basic-form">
                                    <form method="POST" id="medicineImport" enctype="multipart/form-data"
                                        action="{{ admin_url('ohc/medicine-requisition/import/submit') }}">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" id="unit_id_hidden" name="unit_id">
                                            <input type="hidden" id="department_id_hidden" name="department_id">
                                            <input type="hidden" id="request_date_hidden" name="request_date">
                                            <input type="hidden" id="req_id_hidden" name="req_id">
                                            <div class="mb-3 col-md-6 form-input">
                                                <label class="form-label required">Medicine File</label>
                                                <input type="file" name="medicine_upload" class="form-control"
                                                    placeholder="">
                                            </div>
                                        </div>
                                        <hr>

                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/medicine-requisition/list') }}"></x-button-cancel>

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
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });
        $(document).ready(function() {
            // Function to get URL parameters
            function getUrlParameter(name) {
                let urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(name);
            }

            // Get values from URL
            let unit = getUrlParameter('unit_id');
            let department = getUrlParameter('department_id');
            let requestDate = getUrlParameter('request_date');
            let req_id = getUrlParameter('req_id');
            // Set values in hidden fields
            if (unit) {
                $('#unit_id_hidden').val(unit);
            }
            if (department) {
                $('#department_id_hidden').val(department);
            }
            if (requestDate) {
                $('#request_date_hidden').val(requestDate);
            }
            if (req_id) {
                $('#req_id_hidden').val(req_id);
            }
        });

        $(function() {
            $('#medicineImport').validate({
                rules: {

                    medicine_upload: {
                        required: true,
                        extension: "xlsx",
                        filesize: 5242880,
                    },
                },
                messages: {
                    medicine_upload: {
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
