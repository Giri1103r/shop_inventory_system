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
                                        <div class="row ">
                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Date</label>
                                                    <input type="text" name="date" id="date" class="form-control"
                                                        placeholder="Date">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Time(From)</label>
                                                    <input type="text" name="from_time" id="from_time"
                                                        class="form-control" placeholder="Time(From)">
                                                </div>
                                            </div>


                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Time(To)</label>
                                                    <input type="text" name="to_time" id="to_time" class="form-control"
                                                        placeholder="Time(To)">
                                                </div>
                                            </div>


                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($unitList as $unit)
                                                            <option value="{{ encryptId($unit->id) }}">
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Exact location of job</label>
                                                    <input type="text" name="exact_location" id="exact_location"
                                                        class="form-control" placeholder="Name">
                                                </div>
                                            </div>


                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Job Location & Area</label>
                                                    <input type="text" name="location_area" id="location_area"
                                                        class="form-control" placeholder="Name">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Work Permit No</label>
                                                    <input type="text" name="protective_equip" id="protective_equip"
                                                        class="form-control" placeholder="Name">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row ">
                                            <div class="col-12">
                                                <p class="fw-bold fs-5 mt-3">Type of Job: Please Tick Mark (<i class="fas fa-check text-primary"></i>)
                                                    <span class="text-danger">*</span>
                                                </p>
                                                <div class="card p-3 border border-success rounded mb-3">
                                                    <div class="row g-3">
                                                        @foreach ($typeofwork as $typeofwork)
                                                            <div class="col-12 col-md-4 d-flex align-items-center gap-2">
                                                                <input type="checkbox" id="select-all" class="validate-radio-required">
                                                                <a href="{{ asset($typeofwork->file_path) }}" target="_blank">
                                                                    <img src="{{ asset($typeofwork->file_path) }}" alt="Image" class="img-fluid" style="max-width: 50px; object-fit: cover;">
                                                                </a>
                                                                <span>{{ $typeofwork->work_name }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3 ">
                                            <div class="col-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Job Description</label>
                                                    <textarea name="job_description" class="form-control" placeholder="Job Description"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row border p-3 mx-1">
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <img src="{{ url('public/assets/images/safetypermit/power-off.png') }}" class="img-fluid" style="width: 50px; height: 50px;">
                                                    <label class="form-label mb-0">Shut Down Required (Yes/No)</label>
                                                    <input type="checkbox" class="validate-radio-required">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <img src="{{ url('public/assets/images/safetypermit/profile.png') }}" class="img-fluid" style="width: 50px; height: 50px;">
                                                    <label class="form-label mb-0 ">Taken By (Name & Department)</label>
                                                    <input type="text" name="description" class="form-control" placeholder="Search by Employee Name" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row border p-3 mx-1">
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <img src="{{ url('public/assets/images/safetypermit/process.png') }}" class="img-fluid" style="width: 50px; height: 50px;">
                                                    <label class="form-label mb-0 ">Isolation/LOTO Required (Yes/No)</label>
                                                    <input type="checkbox" class="validate-radio-required">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <img src="{{ url('public/assets/images/safetypermit/profile.png') }}" class="img-fluid" style="width: 50px; height: 50px;">
                                                    <label class="form-label mb-0 ">Taken By (Name & Department)</label>
                                                    <input type="text" name="description" class="form-control" placeholder="Search by Employee Name" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row border p-3 mx-1">
                                            <div class="col-12 col-md-4 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <label class="form-label mb-0">Loto No</label>
                                                    <input type="text" name="description" class="form-control" placeholder="Loto No" disabled>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-8 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <label class="form-label mb-0 ">Tag Field properly (Yes/No)</label>
                                                    <input type="checkbox" class="validate-radio-required">
                                                </div>
                                            </div>
                                        </div>
                                        <p class="fw-bold fs-5 mt-3">
                                            Type of Job: Please Tick Mark (<i class="fas fa-check text-primary"></i>)
                                            <span class="text-danger">*</span>
                                        </p>
                                        
                                     
                                        <div class="row border rounded p-3 mx-1 ">
                                            <div class="col-12 col-md-3 mb-3">
                                                <div class="form-group">
                                                    <label class="form-label">O2%</label>
                                                    <input type="text" name="description" class="form-control" placeholder="" disabled>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3 mb-3">
                                                <div class="form-group">
                                                    <label class="form-label">System Isolated</label>
                                                    <input type="checkbox" class="validate-radio-required">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3 mb-3">
                                                <div class="form-group">
                                                    <label class="form-label">Rescue System Available</label>
                                                    <input type="checkbox" class="validate-radio-required">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row border rounded p-3 mx-1 ">
                                            <div class="col-12 col-md-3 mb-3">
                                                <div class="form-group">
                                                    <label class="form-label">Confined Space Attendant</label>
                                                    <input type="checkbox" class="validate-radio-required">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3 mb-3">
                                                <div class="form-group">
                                                    <label class="form-label">Attendant Name</label>
                                                    <input type="text" name="description" class="form-control" placeholder="Search by Employee Name" disabled>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3 mb-3">
                                                <div class="form-group">
                                                    <label class="form-label">Register for entry & exits</label>
                                                    <input type="checkbox" class="validate-radio-required">
                                                </div>
                                            </div>
                                        </div>
                                        
                                      
                                        <div class="row border rounded p-3 mx-1 mb-3">
                                            <div class="col-12 col-md-3 mb-3">
                                                <div class="form-group">
                                                    <label class="form-label">Any Other Gas / PPM</label>
                                                    <input type="text" name="description" class="form-control" placeholder="Loto No" disabled>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3 mb-3">
                                                <div class="form-group">
                                                    <label class="form-label">PPM and is therefore safe to enter from</label>
                                                    <input type="text" name="description" class="form-control" placeholder="" disabled>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3 mb-3">
                                                <div class="form-group">
                                                    <label class="form-label">To</label>
                                                    <input type="text" name="description" class="form-control" placeholder="" disabled>
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
        flatpickr("#date", {
            // enableTime: true,
            dateFormat: "d-m-Y",
            // time_24hr: true,
            minuteIncrement: 5,
        });

        flatpickr("#from_time", {
            enableTime: true,
            noCalendar: true,
            time_24hr: true,
            minuteIncrement: 5,
            dateFormat: "H:i"
        });

        flatpickr("#to_time", {
            enableTime: true,
            noCalendar: true,
            time_24hr: true,
            minuteIncrement: 5,
            dateFormat: "H:i"
        });


        $(document).on('change', '#ppe_type', function() {
            let PPEtypeId = $(this).val();
            console.log(PPEtypeId);

            if (PPEtypeId) {
                $.ajax({
                    url: "{{ admin_url('ppe_ppetype_master/ajax-list') }}",
                    type: 'GET',
                    data: {
                        id: PPEtypeId
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

        $(document).on('change', '#ppe_name', function() {
            let PPEnameId = $(this).val();

            if (PPEnameId) {
                $.ajax({
                    url: "{{ admin_url('ppe_ppetype_master/ajax-image') }}",
                    type: 'GET',
                    data: {
                        id: PPEnameId
                    },
                    success: function(data) {
                        $('#ppe_image').empty();

                        if (data.image_url) {

                            $('#ppe_image').append('<img src="' + data.image_url +
                                '" alt="PPE Image" />');
                        } else {

                            $('#ppe_image').append('No image uploaded for this PPE name.');
                        }

                        $('#ppe_image').trigger('change');
                    },
                    error: function(xhr) {
                        alert('Error fetching PPE Images. Please try again.');
                    }
                });
            } else {
                $('#ppe_image').empty().append('No image uploaded for this PPE name.');
                $('#ppe_image').trigger('change');
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
                var regex = /^[a-zA-Z0-9\-_'"()\s]{3,30}$/;

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
