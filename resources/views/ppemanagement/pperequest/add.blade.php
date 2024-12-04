@extends('admin.layouts.admin')
@section('title', 'PPE Request Add')
@section('pageurl', admin_url('ppe_request/list'))


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
                                    <x-button-back href="{{ admin_url('ppe_request/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="pperequestadd"
                                        action="{{ admin_url('ppe_request/add/submit') }}">
                                        @csrf
                                        <input type="hidden" name="emp_id" id="emp_id" value="{{ $employee->emp_id }}">
                                        <input type="hidden" name="emp_name" id="emp_name"
                                            value="{{ $employee->emp_name }}">
                                        <input type="hidden" name="department" id="department"
                                            value="{{ $employee->department }}">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Type</label>
                                                    <select name="ppe_type" id="ppe_type" style="width: 100%"
                                                        class="form-select form-select-sm single-select ">
                                                        <option value="">Select the PPE type</option>
                                                        @foreach ($ppetypedata as $ppetype)
                                                            <option value="{{ $ppetype->id }}">{{ $ppetype->ppe_type }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('ppe_type')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <div class="text-danger" id="ppe_type_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Name</label>
                                                    <select name="ppe_name" id="ppe_name" style="width: 100%"
                                                        class="form-select form-select-sm single-select ">
                                                        <option value="">Select the PPE name</option>
                                                    </select>
                                                    @error('ppe_name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <div class="text-danger" id="ppe_name_error"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input" id="ppe_image">

                                                </div>
                                            </div>


                                        </div>
                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('ppe_type/list') }}"></x-button-cancel>
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
    <script>
         $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });
        $(document).on('change', '#ppe_type', function() {
            let PPEtypeId = $(this).val();
            console.log(PPEtypeId);

            if (PPEtypeId) {
                $.ajax({
                    url: "{{ admin_url('ppe_ppetype_master/ajax-list') }}",
                    type: 'GET',
                    data: {
                        id: PPEtypeId,
                        _ts: new Date().getTime()
                    },
                    success: function(data) {
                        console.log(data);
                        $('#ppe_name').empty().append('<option value="">Select PPE Name</option>');
                        $.each(data, function(key, value) {
                            $('#ppe_name').append('<option value="' + value.id + '">' + value
                                .ppe_name + '</option>');
                        });
                        $('#ppe_name').trigger('change');
                    },
                    error: function(xhr) {
                        alert('Error fetching PPE names. Please try again.');
                    }
                });
            } else {
                $('#ppe_name').empty().append('<option value="">Select PPE Name</option>');
                $('#ppe_name').trigger('change');
            }
        });


        $(document).ready(function() {

            $('#pperequestadd').on('submit', function(e) {
                let valid = true;


                if (!validatePPEName()) valid = false;
                if (!validatePPEType()) valid = false;

                if (!valid) {
                    e.preventDefault();
                } else {
                    $('#submit').prop('disabled', true);
                }
            });


            function validatePPEType() {

                var name = $('#ppe_type').val();

                if (name === "") {
                    $('#ppe_type_error').text('PPE type cannot be empty.');
                    return false;
                }

                $('#ppe_type_error').text('');
                return true;
            }

            function validatePPEName() {
                var ppename = $('#ppe_name').val();
                if (ppename === "") {
                    $('#ppe_name_error').text('PPE Name cannot be empty');
                    return false;
                }
                $('#ppe_name_error').text('');
                return true;
            }

        });
    </script>
@endpush
