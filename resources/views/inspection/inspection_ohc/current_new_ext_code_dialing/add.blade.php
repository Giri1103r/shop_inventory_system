@extends('admin.layouts.admin')
@section('title', 'Current New Ext Code Dailing')
@section('pageurl', admin_url('ohc/current-new-ext-code-dialing/list'))


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
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ohc/current-new-ext-code-dialing/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="firstAidEquipmentAdd" action="{{ admin_url('ohc/current-new-ext-code-dialing/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="unit_id" class="form-label require">
                                                        Unit</label>
                                                    <select name="monthly_audit[1][unit_id]" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($unitList as $unit)
                                                            <option value="{{ encryptId($unit->id) }}">
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department</label>
                                                    <select name="department_id[0]" id="department_id"
                                                        class="form-control department-select select2 single-select"
                                                        style="width:100%">
                                                        <option value="">Select Department Name</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Name</label>
                                                    <select name="emp_name[0]"
                                                        class="form-control emp-select single-select select2"
                                                        style="width:100%">
                                                        <option value="">Select Employee</option>
                                                    </select>
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
        </form>
    </div>

@stop

@push('script')
    <script type="text/javascript" nonce="projectcab">

         $(document).on('change', '#unit_id', function() {
            var unitId = $(this).val();
            if (unitId) {
                $.ajax({
                    url: "{{ admin_url('department/ajax-list') }}/" + unitId + "/0",
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#department_id').empty().append('<option value="">Select Department Name</option>');
                        $.each(data, function(key, value) {
                            $('#department_id').append('<option value="' + value.id + '">' + value
                                .name + '</option>');
                        });
                        $('#department_id').trigger('change.');
                    },
                    error: function(xhr) {
                        alert('Error fetching locations. Please try again.');
                    }
                });
            } else {
                $('#department_id').empty().append('<option value="">Select Department Name</option>');
                $('#department_id').trigger('change.');
            }
        });
    </script>
@endpush
