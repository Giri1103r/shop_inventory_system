@extends('admin.layouts.admin')
@section('title', ' First Aid Equipment')
@section('pageurl', admin_url('ohc/master/first-aid-stock/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">Company Add</h4> --}}

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
                                    <x-button-back href="{{ admin_url('ohc/master/first-aid-stock/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="firstAidEquipmentAdd" action="{{ admin_url('ohc/master/first-aid-stock/add/submit') }}">
                                        @csrf

                                        <div class="row">

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Medicine Name</label>
                                                    <select name="medicine_id" id="medicine_id"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select the option</option>
                                                        @foreach ($medicine as $medicine)
                                                        <option value="{{ encryptId($medicine->id) }}">
                                                            {{ $medicine->medicine }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Freeze Quantity</label>
                                                    <input type="number" min="1" name="freeze_quantity" id = "freeze_quantity" class="form-control"
                                                        placeholder="freeze Quantity">
                                                </div>
                                            </div>


                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                            href="{{ admin_url('ohc/master/first-aid-stock/list') }}"></x-button-cancel>
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
    <script type="text/javascript" nonce="projectcab">
        $(function() {
            $('#firstAidEquipmentAdd').validate({
                rules: {
                    medicine_id: {
                        required: true,
                        remote: {
                            url: '{{ admin_url("ohc/master/first-aid-stock/unique") }}',
                            type: 'post',
                            data: {
                                medicine_id: function() {
                                    return $('#medicine_id').val();
                                }
                            }
                        }
                    },

                    freeze_quantity: {
                        required: true,

                    }

                },
                messages: {
                    medicine_id: {
                        required: "{{ __('Medicine Name is Required') }}",
                        remote: "{{ __('Medicine Name should be unique') }}",

                    },

                    freeze_quantity: {
                        required: "Freeze Quantity is Required",
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
