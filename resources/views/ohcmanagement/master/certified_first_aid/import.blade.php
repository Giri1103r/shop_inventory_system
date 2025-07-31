@extends('admin.layouts.admin')
@section('title', 'Certified First Aider')
@section('pageurl', admin_url('ohc/certified-first-aider/list'))


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
                                    <x-button-download href="{{ admin_url('ohc/certified-first-aider/sampledownload') }}"></x-button-download>
                                    <x-button-back href="{{ admin_url('ohc/certified-first-aider/list') }}"></x-button-back>
                                </div>

                            </div>

                            <div class="card-body ">

                                <div class="basic-form">
                                    <form method="POST" id="certifiedfirstaiderimport" enctype="multipart/form-data"
                                        action="{{ admin_url('ohc/certified-first-aider/import/submit') }}">
                                        @csrf
                                        <div class="row">
                                            <div class="mb-3 col-md-6 form-input">
                                                <label class="form-label required">Certified First Aider File</label>
                                                <input type="file" name="certified_first_aider_upload" class="form-control"
                                                    placeholder="">
                                            </div>
                                        </div>
                                        <hr>

                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('ohc/certified-first-aider/list') }}"></x-button-cancel>

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
            $('#certifiedfirstaiderimport').validate({
                rules: {

                    certified_first_aider_upload: {
                        required: true,
                        extension: "xlsx",
                        filesize: 5242880,
                    },
                },
                messages: {
                    certified_first_aider_upload: {
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
