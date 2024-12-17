@extends('admin.layouts.admin')
@section('title', 'Topic Edit')
@section('pageurl', admin_url('topic/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Company Edit') }}</h4> --}}

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
                                {{-- <h4 class="card-title">{{ __('master.company_add') }}</h4> --}}
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('topic/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="topicedit" action="{{ admin_url('topic/edit/submit') }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($topic->id) }}">

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Topic ID</label>
                                                    <input type="text" name ="topic_id" class="form-control"
                                                        placeholder="Topic ID" value="{{ $topic->topic_id }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Topic Name</label>
                                                    <input type="text" name="topic_name" class="form-control"
                                                        placeholder="Topic Name" value="{{ $topic->topic_name }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Upload Questionnaire</label>
                                                    <input type="file" name="questionnaire" id="questionnaire"
                                                        class="form-control">
                                                    <small class="text-muted">Allowed file types: .xlsx, .pdf.</small>
                                                    @if ($training_Files_questionnaire)
                                                        <a href="{{ asset($training_Files_questionnaire->file_path) }}"
                                                            target="_blank" class="d-block mt-2">
                                                            <i class="fa-solid fa-eye text-danger"></i> View
                                                        </a>
                                                    @else
                                                        <small class="text-muted">No file uploaded yet.</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('topic/list') }}"></x-button-cancel>
                                        </div>
                                    </form>
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

@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });
        $(function() {
            $('#topicedit').validate({
                rules: {
                    topic_name: {
                        required: true,
                        minlength: 3,
                        maxlength: 100,
                        pattern: /^[a-zA-Z0-9\s\-_'"(),&]*$/,
                        remote: {
                            url: '{{ admin_url('topic/unique') }}',
                            type: 'post',
                            data: {
                                location_type_name: function() {
                                    return $('#topic_name').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                }
                            }
                        }
                    },
                    questionnaire: {
                        extension: "xlsx|pdf",
                    },
                },
                messages: {
                    topic_name: {
                        required: "{{ __('Topic Name is Required') }}",
                        minlength: "{{ __('common.validate_min_length') }}",
                        maxlength: "Maximum Characters should not exceed 100",
                        pattern: "Only alphanumeric characters and -, _, ', \", (), ,, and & are allowed",
                        remote: "{{ __('Topic Name should be unique') }}"
                    },
                    questionnaire: {
                        extension: "Only .xlsx and .pdf file formats are allowed.",
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
