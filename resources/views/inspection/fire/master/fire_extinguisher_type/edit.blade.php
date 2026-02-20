@extends('admin.layouts.admin')
@section('title', 'Fire Extinguisher Type')
@section('pageurl', admin_url('fire/master/fire_extinguisher-type/list'))


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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('fire/master/fire_extinguisher-type/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="fireExtinguisher" action="{{ admin_url('fire/master/fire_extinguisher-type/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($fireExtinguisherType->id) }}">

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Fire Extinguisher Type ID</label>
                                                    <input type="text" name ="fire_extinguisher_id" id="fire_extinguisher_id"
                                                        class="form-control" placeholder="Company ID"
                                                        value="{{ $fireExtinguisherType->fire_extinguisher_id }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Fire Extinguisher Type</label>
                                                    <input type="text" name="fire_extinguisher_name" class="form-control"
                                                        placeholder="Fire Extinguisher Type" value="{{ $fireExtinguisherType->fire_extinguisher_name }}">
                                                </div>
                                            </div>

                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('fire/master/fire_extinguisher-type/list') }}"></x-button-cancel>
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
            $('#fireExtinguisher').validate({
                rules: {
                    fire_extinguisher_name: {
                        required: true,
                        minlength: 3,
                        maxlength: 100,
                        remote: {
                            url: '{{ admin_url('fire/master/fire_extinguisher-type/unique') }}',
                            type: 'post',
                            data: {
                                location_type_name: function() {
                                    return $('#fire_extinguisher_name').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                }
                            }
                        }
                    },

                },
                messages: {
                    fire_extinguisher_name: {
                        required: "{{ __('Fire Extinguisher Type is Required') }}",
                        minlength: "{{ __('common.validate_min_length') }}",
                        maxlength: "Maximum Characters should not exceed 100",
                        remote: "{{ __('Fire Extinguisher Type should be unique') }}"
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
                    form.submit();

                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {

                    });
                }
            });
        });
    </script>
@endpush
