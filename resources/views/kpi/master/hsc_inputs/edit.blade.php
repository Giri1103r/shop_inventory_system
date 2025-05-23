@extends('admin.layouts.admin')
@section('title', 'HSE Inputs')
@section('pageurl', admin_url('kpi/hse-inputs/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Location Add') }}</h4> --}}

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
                                <h4 class="card-title"></h4>
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('kpi/hse-inputs/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="hscinputsadd"
                                        action="{{ admin_url('kpi/hse-inputs/edit/submit') }}">
                                        @csrf

                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($hsc_inputs->id) }}">

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label">{{ __('common.company') }}</label>
                                                    <select name="company_id" id="company_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Company Name</option>
                                                        @foreach ($companies as $company)
                                                            <option value="{{ encryptId($company->id) }}"
                                                                @if ($hsc_inputs->company_id == $company->id) selected @endif>
                                                                {{ $company->company_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label">{{ __('common.location') }}</label>
                                                    <select name="location_id" id="location_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Location</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label">{{ __('common.unit') }}</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label">{{ __('common.department') }}</label>
                                                    <select name="department_id" id="department_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Department</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('common.year') }}</label>
                                                    <input type="text" name="year" id="year" class="form-control"
                                                        placeholder="Enter Year" value="{{ $hsc_inputs->calendar_year }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('common.month') }}</label>
                                                    <input type="text" name="month" id="month" class="form-control"
                                                        placeholder="Enter Month" value="{{ $hsc_inputs->month }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('common.financial_year') }}</label>
                                                    <input type="text" name="financial_year" id="financial_year"
                                                        class="form-control" placeholder="Enter financial year"
                                                        value="{{ $hsc_inputs->financial_year }}">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="card">
                                            <div class="card-header-inner">
                                                <h4 class="text-white mt-0 ms-2 ">{{ __('common.leading') }}</h4>
                                            </div>
                                            <div class="col-md-12 container">
                                                <div class="form-group row">
                                                    @foreach ($leadings as $leading)
                                                        <div class="col-md-6 mb-3 form-input">
                                                            <label
                                                                class="form-label">{{ getLeadingName($leading->leading_id) }}</label>
                                                            <input type="text" name="leading_input[{{ $leading->id }}]"
                                                                class="form-control leading" placeholder="Enter value"
                                                                value="{{ $leading->value }}">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="card">
                                            <div class="card-header-inner">
                                                <h4 class="text-white mt-0 ms-2 ">{{ __('common.lagging') }}</h4>
                                            </div>
                                            <div class="col-md-12 container">
                                                <div class="form-group row">
                                                    @foreach ($laggings as $lagging)
                                                        <div class="col-md-6 mb-3 form-input">
                                                            <label
                                                                class="form-label">{{ getLaggingName($lagging->lagging_id) }}</label>
                                                            <input type="text"
                                                                name="lagging_input[{{ $lagging->id }}]"
                                                                class="form-control lagging" placeholder="Enter value"
                                                                value="{{ $lagging->value }}">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('kpi/hse-inputs/list') }}"></x-button-cancel>
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

            var initialCompanyId = $('#company_id').val();
            var preselectedLocationId = "{{ encryptId($hsc_inputs->location_id) ?? '0' }}";
            var preselectedUnitId = "{{ encryptId($hsc_inputs->unit_id) ?? '0' }}";
            var preselectedDepartmentId = "{{ encryptId($hsc_inputs->unit_id) ?? '0' }}";

            if (initialCompanyId) {
                fetchLocations(initialCompanyId, preselectedLocationId, function() {
                    var location_id = preselectedLocationId;
                    fetchUnits(location_id, preselectedUnitId, function() {
                        var unit_id = preselectedUnitId;
                        fetchDepartments(unit_id, preselectedDepartmentId);
                    });
                });
            }

            $('#company_id').on('change', function() {
                var company_id = $(this).val();
                fetchLocations(company_id, preselectedLocationId, function() {
                    $('#location_id').trigger('change');
                });
            });

            $('#location_id').on('change', function() {
                var location_id = $(this).val();
                fetchUnits(location_id, preselectedUnitId, function() {
                    $('#unit_id').trigger('change');
                });
            });

            $('#unit_id').on('change', function() {
                var unit_id = $(this).val();
                fetchDepartments(unit_id, preselectedDepartmentId, function() {
                    $('#department_id').trigger('change');
                });
            });

            function fetchLocations(company_id, preselectedLocationId, callback) {
                if (company_id) {
                    $.ajax({
                        url: "{{ admin_url('location/ajax-list/') }}" + company_id + '/' +
                            preselectedLocationId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#location_id').empty().append(
                                '<option value="">Select Location</option>');
                            $.each(data, function(key, value) {
                                var selected = (value.id == preselectedLocationId) ?
                                    'selected' : '';
                                $('#location_id').append('<option value="' + value.id + '" ' +
                                    selected + '>' + value.name + '</option>');
                            });
                            if (callback) callback();
                        }
                    });
                } else {
                    $('#location_id').empty().append('<option value="">Select Location</option>');
                }
            }

            function fetchUnits(location_id, preselectedUnitId, callback) {
                if (location_id) {
                    $.ajax({
                        url: "{{ admin_url('unit/ajax-list/') }}" + location_id + '/' + preselectedUnitId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#unit_id').empty().append('<option value="">Select Unit</option>');
                            $.each(data, function(key, value) {
                                var selected = (value.id == preselectedUnitId) ? 'selected' :
                                    '';
                                $('#unit_id').append('<option value="' + value.id + '" ' +
                                    selected + '>' + value.name + '</option>');
                            });
                            if (callback) callback();
                        }
                    });
                } else {
                    $('#unit_id').empty().append('<option value="">Select Unit</option>');
                }
            }

            function fetchDepartments(unit_id, preselectedDepartmentId, callback) {
                if (unit_id) {
                    $.ajax({
                        url: "{{ admin_url('department/ajax-list/') }}" + unit_id + '/' +
                            preselectedDepartmentId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#department_id').empty().append(
                                '<option value="">Select Department</option>');
                            $.each(data, function(key, value) {
                                var selected = (value.id == preselectedDepartmentId) ?
                                    'selected' : '';
                                $('#department_id').append('<option value="' + value.id + '" ' +
                                    selected + '>' + value.name + '</option>');
                            });
                            if (callback) callback();
                        }
                    });
                } else {
                    $('#department_id').empty().append('<option value="">Select Department</option>');
                }
            }

            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });

            $(document).on('change', '#company_id', function() {
                var companyId = $(this).val();
                if (companyId) {
                    $.ajax({
                        url: "{{ admin_url('location/ajax-list') }}/" + companyId +
                            "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#location_id').empty().append(
                                '<option value="">Select Location</option>');
                            $.each(data, function(key, value) {
                                $('#location_id').append('<option value="' +
                                    value.id +
                                    '">' + value
                                    .name + '</option>');
                            });
                            $('#location_id').trigger('change.');
                        },
                        error: function(xhr) {
                            alert('Error fetching locations. Please try again.');
                        }
                    });
                } else {
                    $('#location_id').empty().append(
                        '<option value="">Select Location</option>');
                    $('#location_id').trigger('change.');
                }
            });

            $(document).on('change', '#location_id', function() {
                var locationId = $(this).val();
                if (locationId) {
                    $.ajax({
                        url: "{{ admin_url('unit/ajax-list') }}/" + locationId + "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#unit_id').empty().append(
                                '<option value="">Select Unit</option>');
                            $.each(data, function(key, value) {
                                $('#unit_id').append('<option value="' +
                                    value.id +
                                    '">' + value
                                    .name + '</option>');
                            });
                            $('#unit_id').trigger('change.');
                        },
                        error: function(xhr) {
                            alert('Error fetching unit. Please try again.');
                        }
                    });
                } else {
                    $('#unit_id').empty().append('<option value="">Select Unit</option>');
                    $('#unit_id').trigger('change.');
                }
            });

            $(document).on('change', '#unit_id', function() {
                var unitId = $(this).val();
                if (unitId) {
                    $.ajax({
                        url: "{{ admin_url('department/ajax-list') }}/" + unitId +
                            "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#department_id').empty().append(
                                '<option value="">Select Department</option>');
                            $.each(data, function(key, value) {
                                $('#department_id').append(
                                    '<option value="' + value
                                    .id + '">' + value.name +
                                    '</option>');
                            });
                            $('#department_id').trigger('change.');
                        },
                        error: function(xhr) {
                            alert('Error fetching unit. Please try again.');
                        }
                    });
                } else {
                    $('#department_id').empty().append(
                        '<option value="">Select Department</option>');
                    $('#department_id').trigger('change.');
                }
            });



            function updateFinancialYear() {
                const year = $('#year').val();
                const month = $('#month').val();

                if (year && month) {
                    const y = parseInt(year, 10);
                    const m = parseInt(month, 10);

                    let startYear, endYear;

                    if (m >= 4) {
                        startYear = y;
                        endYear = y + 1;
                    } else {
                        startYear = y - 1;
                        endYear = y;
                    }

                    $('#financial_year').val(`${startYear}-${endYear}`);
                }
            }

            $('#year').datepicker({
                format: 'yyyy',
                minViewMode: 2,
                autoclose: true
            }).on('changeDate', updateFinancialYear);

            $('#month').datepicker({
                format: 'mm',
                minViewMode: 1,
                autoclose: true
            }).on('changeDate', updateFinancialYear);

        });
        $(function() {

            $('#month').on('changeDate', function() {
                $(this).valid();
            });
            $(document).on('click', '#resetform', function() {
                $('#hscinputsadd .single-select').val('');
                $('#hscinputsadd .single-select').trigger('change');
                setTimeout(function() {
                    table.draw();
                }, 150);
            });
            $('#hscinputsadd').validate({
                rules: {
                    company_id: {
                        required: true,
                    },
                    location_id: {
                        required: true,
                    },
                    unit_id: {
                        required: true,
                    },
                    department_id: {
                        required: true,
                    },
                    year: {
                        required: true,
                    },
                    month: {
                        required: true,
                        remote: {
                            url: "{{ admin_url('kpi/master/hsc-inputs/unique') }}",
                            type: 'post',
                            data: {
                                company_id: function() {
                                    return $('#company_id').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                },
                                location_id: function() {
                                    return $('#location_id').val();
                                },
                                unit_id: function() {
                                    return $('#unit_id').val();
                                },
                                department_id: function() {
                                    return $('#department_id').val();
                                },
                                year: function() {
                                    return $('#year').val();
                                },
                                month: function() {
                                    return $('#month').val();
                                },
                            }
                        }
                    },
                    financial_year: {
                        required: true,

                    },

                },
                messages: {
                    company_id: {
                        required: "{{ __('Company is Required') }}",
                    },
                    location_id: {
                        required: "{{ __('Location is Required') }}",
                    },
                    unit_id: {
                        required: "{{ __('Unit is Required') }}",
                    },
                    department_id: {
                        required: "{{ __('Department is Required') }}",
                    },
                    year: {
                        required: "{{ __('Year is Required') }}",
                    },
                    month: {
                        required: "{{ __('Month is Required') }}",
                        remote: "{{ __('The above fields are already Exists') }}"
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
            $.validator.addClassRules("leading", {
                required: true,
            });

            $.validator.addClassRules("lagging", {
                required: true
            });
        });
    </script>
@endpush
