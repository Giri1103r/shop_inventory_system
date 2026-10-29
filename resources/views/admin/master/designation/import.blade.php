@extends('admin.layouts.admin')
@section('title', 'Designation Master')
@section('pageurl', admin_url('master/designation/list'))


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
                                <div class="d-flex justify-content-end p-2 gap-2">
                                    <x-button-download
                                        href="{{ admin_url('master/designation/sampledownload') }}"></x-button-download>
                                    <x-button-back href="{{ admin_url('master/designation/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">

                                <div class="basic-form">
                                    <form method="POST" id="designationimport" enctype="multipart/form-data"
                                        action="{{ admin_url('master/designation/import/submit') }}">
                                        @csrf
                                        <div class="row">
                                            <div class="mb-3 col-md-6 form-input">
                                                <label class="form-label required">{{ __('common.file') }}</label>
                                                <input type="file" name="designation_upload" class="form-control"
                                                    placeholder="">
                                            </div>
                                        </div>
                                        <hr>

                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>


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

        $(function() {
            $('#designationimport').validate({
                rules: {

                    designation_upload: {
                        required: true,
                        extension: "xlsx",
                        filesize: 5242880,
                    },
                },
                messages: {
                    designation_upload: {
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
