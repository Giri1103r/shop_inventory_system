@extends('admin.layouts.admin')
@section('title', 'Safety Permit Edit')
@section('pageurl', admin_url('safetypermit/list'))


@section('content')
    <div class="clearfix">
    </div>
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
                                    <x-button-back href="{{ admin_url('safetypermit/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="safetyPermitEdit"
                                        action="{{ admin_url('safetypermit/edit/submit') }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($safetypermit->id) }}">
                                        <div class="row ">
                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Date</label>
                                                    <input type="text" name="date" id="date_picker"
                                                        class="form-control" value="{{ $safetypermit->date }}">
                                                    <div class="text-danger"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Time(From)</label>
                                                    <input type="text" name="time_from" id="time_from_picker"
                                                        class="form-control" placeholder="Time(From)"
                                                        value="{{ $safetypermit->time_from }}">
                                                    <div class="text-danger"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Time(To)</label>
                                                    <input type="text" name="time_to" id="time_to_picker"
                                                        class="form-control" placeholder="Time(To)"
                                                        value="{{ $safetypermit->time_to }}">
                                                    <div class="text-danger"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($unitList as $unit)
                                                            <option value="{{ encryptId($unit->id) }}"
                                                                @if ($unit->id == $safetypermit->unit_id) selected @endif>
                                                                {{ $unit->unit_name }}
                                                        @endforeach
                                                    </select>
                                                    <div class="text-danger"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Exact location of job</label>
                                                    <input type="text" name="exact_location_job" id="exact_location_job"
                                                        class="form-control" placeholder="Exact location of job"
                                                        value="{{ $safetypermit->exact_location_job }}">
                                                    <div class="text-danger"></div>

                                                </div>
                                            </div>


                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Job Location & Area</label>
                                                    <input type="text" name="job_location_area" id="job_location_area"
                                                        class="form-control" placeholder="Job Location & Area"
                                                        value="{{ $safetypermit->job_location_area }}">
                                                    <div class="text-danger"></div>

                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Work Permit No</label>
                                                    <input type="text" name="permit_id" id="permit_id"
                                                        class="form-control" value="{{ $safetypermit->permit_id }}"
                                                        readonly>
                                                    <div class="text-danger"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="fw-bold fs-5 mt-3">Type of Job: Please Tick Mark (<i
                                                class="fas fa-check text-primary"></i>)
                                            <span class="text-danger">*</span>
                                        </p>
                                        <div class="row" style="background: #d6f5e0b0;">
                                            <div class="col-12">
                                                <div class="card p-3 rounded m-3 work-type">
                                                    <div class="row g-3">
                                                        @php

                                                            $subPermitArray = explode(',', $safetypermit->sub_permit);
                                                        @endphp

                                                        @foreach ($typeofwork as $work)
                                                            <div class="col-12 col-md-4 d-flex align-items-center gap-2">
                                                                <input type="checkbox" class="work-type-checkbox"
                                                                    data-id="{{ $work->id }}" name="sub_permit[]"
                                                                    value="{{ $work->id }}"
                                                                    @if (in_array($work->id, $subPermitArray)) checked @endif>
                                                                <a href="{{ asset($work->file_path) }}" target="_blank">
                                                                    <img src="{{ asset($work->file_path) }}"
                                                                        alt="Image" class="img-fluid"
                                                                        style="max-width: 50px; object-fit: cover;">
                                                                </a>
                                                                <span>{{ $work->work_name }}</span>
                                                            </div>
                                                        @endforeach

                                                        <div class="text-danger"></div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3 ">
                                            <div class="col-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Job Description</label>
                                                    <textarea name="job_description" class="form-control" placeholder="Job Description">{{ $safetypermit->job_description }}</textarea>
                                                    <div class="text-danger"></div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="row border p-3 mx-1">
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <img src="{{ url('public/assets/images/safetypermit/power-off.png') }}"
                                                        class="img-fluid"
                                                        style="width: 50px; height: 50px; margin-right:30px">
                                                    <label for = "shutdown-checkbox"
                                                        class="form-label mb-0"style="margin-right: 58px;">Shut Down
                                                        Required (Yes/No)</label>
                                                    <input type="checkbox" id="shutdown-checkbox"
                                                        class="shutdown-checkbox" name="shutdown_req" value="1"
                                                        {{ $safetypermit->shutdown_req == 1 ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <img src="{{ url('public/assets/images/safetypermit/profile.png') }}"
                                                        class="img-fluid" style="width: 50px; height: 50px;">
                                                    <label class="form-label mb-0">Taken By (Name & Department)</label>
                                                    <select name="shut_down_takenby" id="employeenameshutdown"
                                                        class="form-control shutdowncheckbox"
                                                        {{ $safetypermit->shutdown_req == 1 ? '' : 'disabled' }}>
                                                        <option value="">{{ $safetypermit->shut_down_takenby }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row border p-3 mx-1">
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <img src="{{ url('public/assets/images/safetypermit/process.png') }}"
                                                        class="img-fluid"
                                                        style="width: 50px; height: 50px; margin-right:30px">
                                                    <label for = "loto-checkbox" class="form-label mb-0 "
                                                        style="margin-right: 34px;">Isolation/LOTO Required
                                                        (Yes/No)</label>
                                                    <input type="checkbox" id="loto-checkbox" class="loto-checkbox"
                                                        name= "loto_req" value="1"
                                                        {{ $safetypermit->loto_req == 1 ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <img src="{{ url('public/assets/images/safetypermit/profile.png') }}"
                                                        class="img-fluid" style="width: 50px; height: 50px;">
                                                    <label class="form-label mb-0 ">Taken By (Name & Department)</label>
                                                    {{-- <input type="text" name="description"
                                                        class="form-control lotocheckbox"
                                                        placeholder="Search by Employee Name" disabled> --}}

                                                    <select name="loto_takenby" id="employeenameloto"
                                                        class="form-control lotocheckbox"
                                                        {{ $safetypermit->loto_req == 1 ? '' : 'disabled' }}>
                                                        <option value="">{{ $safetypermit->loto_takenby }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row border p-3 mx-1">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-group d-flex align-items-center">
                                                    <label class="form-label mb-0" style="margin-right: 50px">Loto
                                                        No</label>
                                                    <div class="col-sm-6 p-0">
                                                        <input type="text" name="loto_no" class="form-control"
                                                            placeholder="Loto No"
                                                            value="{{ $safetypermit->tagfield == 1 ? $safetypermit->loto_no : '' }}"
                                                            {{ $safetypermit->tagfield == 1 ? '' : 'disabled' }}>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="col-md-4 mb-3 "style="margin-right: 50px">
                                                <div class="form-group d-flex align-items-center ps-5 ">
                                                    <label for = "shutdowncheckbox" class="form-label mb-0"
                                                        style="margin-right:30px;">Tag Field
                                                        properly (Yes/No)</label>
                                                    <input type="checkbox" class="shutdowncheckbox"
                                                        id = "shutdowncheckbox" name="tagfield" value="1"
                                                        {{ $safetypermit->tagfield == 1 ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row col-md-12 d-flex mt-3">
                                            <!-- Left Side: Scrollable on X-Axis -->
                                            <div class="col-md-4">
                                                <p class="fw-bold fs-5 mt-3">State of Isolation & LOTO</p>
                                                <div class="scroll-container border p-3"
                                                    style="overflow-x: auto; white-space: nowrap; width: 100%;">
                                                    <!-- First Row -->
                                                    <div class="row mb-3 d-flex flex-nowrap align-items-center">
                                                        <div class="d-flex flex-shrink-0 align-items-center gap-1"
                                                            style="min-width: 200px;">
                                                            <img src="{{ url('public/assets/images/safetypermit/person.png') }}"
                                                                class="img-fluid"
                                                                style="width: 50px; height: 50px; margin-right: 20px">
                                                            <label for= "air_checkbox" class="form-label mb-0 "
                                                                style="margin-right: 100px;">Air</label>
                                                            <input type="checkbox" class="shutdowncheckbox"
                                                                id="air_checkbox" name="state_isolation_loto[]"
                                                                value="Air"
                                                                {{ in_array('Air', $stateIsolationLoto ?? []) ? 'checked' : '' }}
                                                                disabled>
                                                        </div>
                                                        <div class="d-flex flex-shrink-0 align-items-center gap-1"
                                                            style="min-width: 200px;">
                                                            <img src="{{ url('public/assets/images/safetypermit/natural-gas.png') }}"
                                                                class="img-fluid"
                                                                style="width: 50px; height: 50px; margin-right: 20px">
                                                            <label for= "gas_checkbox" class="form-label mb-0"
                                                                style="margin-right: 115px;">Gas</label>
                                                            <input type="checkbox" class="shutdowncheckbox"
                                                                id="gas_checkbox" name="state_isolation_loto[]"
                                                                value="Gas"
                                                                {{ in_array('Gas', $stateIsolationLoto ?? []) ? 'checked' : '' }}
                                                                disabled>
                                                        </div>
                                                        <div class="d-flex flex-shrink-0 align-items-center gap-1"
                                                            style="min-width: 200px;">
                                                            <label class="form-label mb-0">Others if any please
                                                                specify</label>
                                                        </div>
                                                    </div>

                                                    <!-- Second Row -->
                                                    <div class="row mb-3 d-flex flex-nowrap align-items-center">
                                                        <div class="d-flex flex-shrink-0 align-items-center gap-1"
                                                            style="min-width: 200px;">
                                                            <img src="{{ url('public/assets/images/safetypermit/electrician.png') }}"
                                                                class="img-fluid"
                                                                style="width: 50px; height: 50px; margin-right: 20px;">
                                                            <label for= "electrical_checkbox" class="form-label mb-0"
                                                                style="margin-right: 58px;">Electrical</label>
                                                            <input type="checkbox" class="shutdowncheckbox"
                                                                id="electrical_checkbox" name="state_isolation_loto[]"
                                                                value="Electrical"
                                                                {{ in_array('Electrical', $stateIsolationLoto ?? []) ? 'checked' : '' }}
                                                                disabled>
                                                        </div>
                                                        <div class="d-flex flex-shrink-0 align-items-center gap-1"
                                                            style="min-width: 200px;">
                                                            <img src="{{ url('public/assets/images/safetypermit/leak.png') }}"
                                                                class="img-fluid"
                                                                style="width: 50px; height: 50px; margin-right: 20px">
                                                            <label for= "water_checkbox" class="form-label mb-0"
                                                                style="margin-right: 58px;">Water/Liquid</label>
                                                            <input type="checkbox" class="shutdowncheckbox"
                                                                id="water_checkbox" name="state_isolation_loto[]"
                                                                value="Water/Liquid"
                                                                {{ in_array('Water/Liquid', $stateIsolationLoto ?? []) ? 'checked' : '' }}
                                                                disabled>
                                                        </div>
                                                        <div class="d-flex flex-shrink-0 align-items-center gap-1"
                                                            style="min-width: 200px;">
                                                            <textarea class="form-control shutdowncheckbox" name="state_isolation_loto[]" placeholder="Specify others" disabled>
                                                                    @if ($stateIsolationLoto)
@foreach ($stateIsolationLoto as $item)
@if (!in_array($item, ['Air', 'Gas', 'Electrical', 'Water/Liquid']))
{{ $item }}
@endif
@endforeach
@endif
                                                            </textarea>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3 d-flex flex-nowrap align-items-center">
                                                        <div class="d-flex flex-shrink-0 align-items-center gap-1"
                                                            style="min-width: 200px;">
                                                            <img src="{{ url('public/assets/images/safetypermit/fire.png') }}"
                                                                class="img-fluid"
                                                                style="width: 50px; height: 50px; margin-right: 20px;">
                                                            <label for= "isolationpanel_checkbox" class="form-label mb-0"
                                                                style="margin-right: 58px;">Isolation fire panel</label>
                                                            <input type="hidden" name="isolationpanel_checkbox"
                                                                value="0">

                                                            <input type="checkbox" class="shutdowncheckbox"
                                                                id="isolationpanel_checkbox"
                                                                name="isolationpanel_checkbox" value="1"
                                                                {{ $safetypermit->isolationpanel_checkbox == 1 ? 'checked' : '' }}>
                                                        </div>

                                                        <div class="d-flex flex-shrink-0 align-items-center gap-1"
                                                            style="min-width: 200px;">
                                                            <input type = "text"
                                                                class="form-control isolationpanel_description"
                                                                name="isolationpanel_description"
                                                                placeholder="Isolation fire panel"
                                                                value = "{{ $safetypermit->isolationpanel_description }}"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="section-1" class="col-md-8">
                                                <p class="fw-bold fs-5 mt-3">Applicable for Confined Space Entry</p>

                                                <div class="row border rounded p-2 mx-1">
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div class="form-group d-flex align-items-center">
                                                            <label class="form-label mb-0"
                                                                style="margin-right: 20px">O2%</label>
                                                            <input type="text"
                                                                name="confined_space_entry[o2_percentage]"
                                                                class="form-control"
                                                                value="{{ $confinedSpaceEntry['o2_percentage'] ?? '' }}"
                                                                disabled>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div class="form-group d-flex align-items-center">
                                                            <label for= "system_isolated" class="form-label mb-0"
                                                                style="margin-right: 20px">System Isolated</label>
                                                            <input type="hidden"
                                                                name="confined_space_entry[system_isolated]"
                                                                value="0">
                                                            <input type="checkbox" id = "system_isolated"
                                                                name="confined_space_entry[system_isolated]"
                                                                value="1"
                                                                {{ $confinedSpaceEntry['system_isolated'] ?? '' == 1 ? 'checked' : '' }}>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div
                                                            class="form-group d-flex align-items-center justify-content-between">
                                                            <label for= "rescue_system" class="form-label mb-0">Rescue
                                                                System Available</label>
                                                            <input type="hidden"
                                                                name="confined_space_entry[rescue_system]" value="0">
                                                            <input type="checkbox" id= "rescue_system"
                                                                name="confined_space_entry[rescue_system]" value="1"
                                                                {{ $confinedSpaceEntry['rescue_system'] ?? '' == 1 ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row border rounded p-2 mx-1">
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div class="form-group d-flex align-items-center">
                                                            <label for= "confined_attendant"
                                                                class="form-label col-md-5 mb-0 mr-2 pe-2">Confined
                                                                Space Attendant</label>
                                                            <input type="checkbox" id= "confined_attendant"
                                                                name="confined_space_entry[confined_attendant]"
                                                                value="1"
                                                                {{ $confinedSpaceEntry['confined_attendant'] ?? '' == 1 ? 'checked' : '' }}>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div
                                                            class="form-group d-flex align-items-center justify-content-between">
                                                            <label class="form-label col-md-6 mb-0 "
                                                                style="margin-right: 10px;">Attendant Name</label>
                                                            <input type="text"
                                                                name="confined_space_entry[attendant_name]"
                                                                class="form-control" placeholder=""
                                                                value="{{ $confinedSpaceEntry['attendant_name'] ?? '' }}"
                                                                disabled>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div
                                                            class="form-group d-flex align-items-center justify-content-between">
                                                            <label for= "register_entry_exits" class="form-label mb-0"
                                                                style="margin-right: 20px">Register for entry &
                                                                exits</label>
                                                            <input type="checkbox" id= "register_entry_exits"
                                                                name="confined_space_entry[register_entry_exits]"
                                                                {{ $confinedSpaceEntry['register_entry_exits'] ?? '' == 1 ? 'checked' : '' }}
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row border rounded p-2 mx-1 mb-3">
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div class="form-group d-flex align-items-center">
                                                            <label class="form-label mb-0">Any Other Gas / PPM</label>
                                                            <input type="text" name="confined_space_entry[other_gas]"
                                                                class="form-control" placeholder="Any Other Gas / PPM"
                                                                value="{{ $confinedSpaceEntry['other_gas'] ?? '' }}"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div class="form-group d-flex align-items-center">
                                                            <label class="form-label mb-0">PPM and is therefore safe to
                                                                enter from</label>
                                                            <input type="text"
                                                                name="confined_space_entry[ppm_entry_date]"
                                                                class="form-control" id="from_PPMTime"
                                                                value="{{ $confinedSpaceEntry['ppm_entry_date'] ?? '' }}"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div class="form-group d-flex align-items-center">
                                                            <label class="form-label mb-0">PPM and is therefore safe to
                                                                enter to</label>
                                                            <input type="text"
                                                                name="confined_space_entry[ppm_entry_to]"
                                                                class="form-control" id="to_PPMTime"
                                                                value="{{ $confinedSpaceEntry['ppm_entry_to'] ?? '' }}"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>


                                        <p class="fw-bold fs-5 mt-3">Protective Equipment's to be Worn (<i
                                                class="fas fa-check text-primary"></i>)
                                            <span class="text-danger">*</span>
                                        </p>
                                        <div class="row" style="background: #d6f5e0b0;">
                                            <div class="col-12 col-lg-8 mt-2 p-2">
                                                <div class="card p-3 rounded m-2">
                                                    <div class="row g-2 mb-3">
                                                        <div class="d-flex flex-wrap justify-content-start">
                                                            <img src="{{ url('public/assets/images/safetypermit/gloves.png') }}"
                                                                class="img-fluid m-1"
                                                                style="width:70px; border:1px solid gray; padding: 11px;">
                                                            <img src="{{ url('public/assets/images/safetypermit/helmet.png') }}"
                                                                class="img-fluid m-1"
                                                                style="width:70px; border:1px solid gray; padding: 11px;">
                                                            <img src="{{ url('public/assets/images/safetypermit/shoes.png') }}"
                                                                class="img-fluid m-1"
                                                                style="width:70px; border:1px solid gray; padding: 11px;">
                                                            <img src="{{ url('public/assets/images/safetypermit/gloves (1).png') }}"
                                                                class="img-fluid m-1"
                                                                style="width:70px; border:1px solid gray; padding: 11px;">
                                                            <img src="{{ url('public/assets/images/safetypermit/boots (1).png') }}"
                                                                class="img-fluid m-1"
                                                                style="width:70px; border:1px solid gray; padding: 11px;">
                                                            <img src="{{ url('public/assets/images/safetypermit/boots.png') }}"
                                                                class="img-fluid m-1"
                                                                style="width:70px; border:1px solid gray; padding: 11px;">
                                                            <img src="{{ url('public/assets/images/safetypermit/safety-goggles.png') }}"
                                                                class="img-fluid m-1"
                                                                style="width:70px; border:1px solid gray; padding: 11px;">
                                                            <img src="{{ url('public/assets/images/safetypermit/shoes.png') }}"
                                                                class="img-fluid m-1"
                                                                style="width:70px; border:1px solid gray; padding: 11px;">
                                                            <img src="{{ url('public/assets/images/safetypermit/icons8-cap-64.png') }}"
                                                                class="img-fluid m-1"
                                                                style="width:70px; border:1px solid gray; padding: 11px;">
                                                            <img src="{{ url('public/assets/images/safetypermit/icons8-headphone-48.png') }}"
                                                                class="img-fluid m-1"
                                                                style="width:70px; border:1px solid gray; padding: 11px;">
                                                            <img src="{{ url('public/assets/images/safetypermit/icons8-mask-64.png') }}"
                                                                class="img-fluid m-1"
                                                                style="width:70px; border:1px solid gray; padding: 11px;">
                                                        </div>
                                                    </div>



                                                    <div id="getprotectivechecklist-container" class="row g-3 mt-3">
                                                        @foreach ($safetypermit->mapped_protective_equip as $job => $details)
                                                            @foreach ($details['checkpoint_names'] as $index => $checkpoint_name)
                                                                <div
                                                                    style="flex: 1 1 calc(33% - 10px); align-items: center; gap: 5px;">
                                                                    <input type="checkbox" class="protective-checkbox"
                                                                        name="protective_equip[{{ $job }}][]"
                                                                        value="{{ $details['checkpoints'][$index] }}"
                                                                        id="checkpoint-{{ $job }}-{{ $details['checkpoints'][$index] }}"
                                                                        @if (isset($details['checkpoints'][$index]) && $details['checkpoints'][$index]) checked @endif>

                                                                    <label class="form-label"
                                                                        for="checkpoint-{{ $job }}-{{ $details['checkpoints'][$index] }}">
                                                                        {{ $checkpoint_name }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        @endforeach
                                                    </div>

                                                </div>
                                            </div>


                                            <div class="col-12 col-lg-4">
                                                <div class="card p-3 rounded mt-4">
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <p class="fw-bold">Mandatory Notes for PPEs</p>
                                                            <ol class="mb-0" style="line-height: 1.8;">
                                                                <li>PPEs must be of national/international standard.</li>
                                                                <li>Damaged/defective PPEs shall not be used.</li>
                                                                <li>Non-standard PPEs shall not be used.</li>
                                                                <li>PPEs must be inspected before use.</li>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <p class="fw-bold fs-5 mt-3">Name of Equipment's involved in Job
                                            <span class="text-danger">*</span>
                                        </p>
                                        <div class="row" style="background: #d6f5e0b0;">
                                            <div class="col-12 p-2">
                                                <div class="card p-4 rounded m-2">
                                                    <div class="row g-4">

                                                        <div class="col-md-4">
                                                            <div
                                                                class="d-flex flex-wrap justify-content-center align-items-center gap-3">

                                                                <div style="width: 80px; height: 80px;"
                                                                    class="d-flex justify-content-center align-items-center border rounded bg-white shadow-sm">
                                                                    <img src="{{ url('public/assets/images/safetypermit/flash.png') }}"
                                                                        class="img-fluid"
                                                                        style="max-width: 70%; max-height: 70%;">
                                                                </div>
                                                                <div style="width: 80px; height: 80px;"
                                                                    class="d-flex justify-content-center align-items-center border rounded bg-white shadow-sm">
                                                                    <img src="{{ url('public/assets/images/safetypermit/gloves.png') }}"
                                                                        class="img-fluid"
                                                                        style="max-width: 70%; max-height: 70%;">
                                                                </div>
                                                                <div style="width: 80px; height: 80px;"
                                                                    class="d-flex justify-content-center align-items-center border rounded bg-white shadow-sm">
                                                                    <img src="{{ url('public/assets/images/safetypermit/shoes.png') }}"
                                                                        class="img-fluid"
                                                                        style="max-width: 70%; max-height: 70%;">
                                                                </div>
                                                                <div style="width: 80px; height: 80px;"
                                                                    class="d-flex justify-content-center align-items-center border rounded bg-white shadow-sm">
                                                                    <img src="{{ url('public/assets/images/safetypermit/gloves (1).png') }}"
                                                                        class="img-fluid"
                                                                        style="max-width: 70%; max-height: 70%;">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div id="getequipmentinvolved-container" class="row g-3 mt-3">
                                                                @if ($safetypermit->mapped_equiment_involved)

                                                                            @foreach ($safetypermit->mapped_equiment_involved as $job => $details)

                                                                                    @foreach ($details['checkpoint_names'] as $index => $checkpoint_name)
                                                                                        <div
                                                                                            style="flex: 1 1 calc(33% - 10px); display: flex; align-items: center; gap: 5px;">
                                                                                            <input type="checkbox"
                                                                                                class="equiment_involved"
                                                                                                name="equiment_involved[{{ $job }}][]"
                                                                                                value="{{ $details['checkpoints'][$index] }}"
                                                                                                id="checkpoint-{{ $job }}-{{ $details['checkpoints'][$index] }}"
                                                                                                @if (isset($details['checkpoints'][$index]) && $details['checkpoints'][$index]) checked @endif>
                                                                                            <label class="form-label"
                                                                                                for="checkpoint-{{ $job }}-{{ $details['checkpoints'][$index] }}">
                                                                                                {{ $checkpoint_name }}
                                                                                            </label>
                                                                                        </div>
                                                                                    @endforeach

                                                                            @endforeach

                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="description" class="form-label fw-bold">Other If
                                                                any</label>
                                                            <textarea id="description" name="equiment_involved_others" class="form-control shadow-sm" placeholder="">{{ $safetypermit['equiment_involved_others'] }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <p class="fw-bold fs-5 mt-3">Precaution To be Taken</span>
                                        </p>

                                        <div class="row" style="background: #d6f5e0b0;">
                                            <div class="col-12 p-2">
                                                <div class="card p-3 rounded m-2">
                                                    <div id="getprecaution-container" class="row g-3 mt-3">
                                                        @foreach ($safetypermit->mapped_precaution_taken as $job => $details)
                                                            <div style="flex-wrap: wrap; gap: 10px;">
                                                                @foreach ($details['checkpoint_names'] as $index => $checkpoint_name)
                                                                    <!-- Only display label if checkbox is checked and checkpoint exists -->
                                                                    @if (isset($details['checkpoints'][$index]) && $details['checkpoints'][$index])
                                                                        <div
                                                                            style="flex: 1 1 calc(33% - 10px); align-items: center; gap: 5px;">
                                                                            <input type="checkbox" class=""
                                                                                name="precaution_taken[{{ $job }}][]"
                                                                                value="{{ $details['checkpoints'][$index] ?? '' }}"
                                                                                id="checkpoint-{{ $job }}-{{ $details['checkpoints'][$index] ?? '' }}"
                                                                                checked>
                                                                            <label class="form-label"
                                                                                for="checkpoint-{{ $job }}-{{ $details['checkpoints'][$index] ?? '' }}">
                                                                                {{ $checkpoint_name }}
                                                                            </label>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                        </div>


                                        <p class="fw-bold fs-5 mt-3">Equipment's Check List</span>
                                        </p>
                                        <div class="row" style="background: #d6f5e0b0;">
                                            <div class="col-12 p-2">
                                                <div class="card p-3 rounded m-2">
                                                    <div id="getchecklist-container" class="row g-3 mt-3">
                                                        @foreach ($safetypermit->mapped_equipment_checklist as $job => $details)
                                                            <div style="flex-wrap: wrap; gap: 10px;">
                                                                @foreach ($details['checkpoint_names'] as $index => $checkpoint_name)
                                                                    <div
                                                                        style="flex: 1 1 calc(33% - 10px);align-items: center; gap: 5px;">
                                                                        @php
                                                                            $checkpointValue =
                                                                                $details['checkpoints'][$index] ?? null;
                                                                        @endphp
                                                                        <input type="checkbox" class="equipment_checklist"
                                                                            name="equipment_checklist[{{ $job }}][]"
                                                                            value="{{ $checkpointValue }}"
                                                                            id="checkpoint-{{ $job }}-{{ $checkpointValue }}"
                                                                            @if ($checkpointValue) checked @endif>
                                                                        <label class="form-label"
                                                                            for="checkpoint-{{ $job }}-{{ $checkpointValue }}">
                                                                            {{ $checkpoint_name }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <div class="p-3 mb-1">
                                                    <label class="fw-bold fs-5 mt-1" for="assigned_job">
                                                        All Involved Equipment's have been inspected as per the inspection
                                                        checklist prior to start work (Yes/No)
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="checkbox" id="checklist_inspection" class=""
                                                        name="equipment_checklist_inspection" value="1"
                                                        {{ isset($safetypermit['equipment_checklist_inspection']) && $safetypermit['equipment_checklist_inspection'] == 1 ? 'checked' : '' }}>

                                                    <div class="text-danger"></div>
                                                </div>
                                            </div>
                                        </div>


                                        <p class="fw-bold fs-5 mt-3">Safe Work Instructions</span>
                                        </p>
                                        <div class="row" style="background: #d6f5e0b0;">
                                            <div class="col-12 p-2">
                                                <div class="card p-3  rounded m-2">
                                                    <div id="getinstruction-container" class="row g-3 mt-3">
                                                        @foreach ($safetypermit->mapped_safework_instruction as $job => $details)
                                                            <div style="flex-wrap: wrap; gap: 10px;">
                                                                @foreach ($details['checkpoint_names'] as $index => $checkpoint_name)
                                                                    <div
                                                                        style="flex: 1 1 calc(33% - 10px); align-items: center; gap: 5px;">
                                                                        <input type="checkbox" class=""
                                                                            name="precaution_taken[{{ $job }}][]"
                                                                            value="{{ $details['checkpoints'][$index] }}"
                                                                            id="checkpoint-{{ $job }}-{{ $details['checkpoints'][$index] }}"
                                                                            @if (isset($details['checkpoints'][$index]) && $details['checkpoints'][$index]) checked @endif>
                                                                        <label class="form-label"
                                                                            for="checkpoint-{{ $job }}-{{ $details['checkpoints'][$index] }}">
                                                                            {{ $checkpoint_name }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-12 col-md-6">
                                                <div class="p-3 mb-1">
                                                    <label class="fw-bold fs-5 mt-1" for="tool-box">
                                                        Safe Work Procedure discussed in tool box talk before start the work
                                                        (Yes/No)
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="checkbox" id="tool-box" name="toolbox_talk"
                                                        value="1"
                                                        {{ isset($safetypermit['toolbox_talk']) && $safetypermit['toolbox_talk'] == 1 ? 'checked' : '' }}>
                                                    <div class="text-danger"></div>
                                                </div>
                                            </div>



                                            <div class="col-12 col-md-6">
                                                <div class="form-group form-input mt-3">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <label for="description"
                                                            class="form-label require me-2 mb-2 mb-md-0"
                                                            style="flex-shrink: 0;">
                                                            Tool box Talk Given By (Name)
                                                        </label>
                                                        <input type="text" name="talk_givenby" id="talk_givenby"
                                                            class="form-control" style="flex-grow: 1; max-width: 300px;"
                                                            value="{{ $safetypermit['talk_givenby'] }}">
                                                    </div>
                                                    <div class="text-danger"></div>

                                                </div>
                                            </div>

                                        </div>
                                        <p class="fw-bold fs-5 mt-3">Mandatory Notes for PPEs</span>
                                        </p>
                                        <div class="row" style="background: #d6f5e0b0;">
                                            <div class="col-12 col-md-12 p-2">
                                                <div class="card p-3 rounded m-2">
                                                    <div class="row g-3">
                                                        <div class="col-12">

                                                            <ol class="mb-0" style="line-height: 1.8;">
                                                                <li>Equipemnt must be of national/international standard.
                                                                </li>
                                                                <li>Damaged/Defective equipment shall not be used.</li>
                                                                <li> Equipment should be in good working condition.</li>
                                                                <li>Non standard equipment shall not be used.</li>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="fw-bold fs-5 mt-3">List of Workman involved in Job</span>
                                        </p>
                                        <div class="row">
                                            <div class="col-md-4 col-sm-6 col-12 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Employee Code / Visitor ID</label>
                                                    <div class="col-sm-6" style="width: 100%">
                                                        <select name="employee_code" id="employee_code"
                                                            style="width: 100%" class="single-select form-control">
                                                            <option value="">Select Employee ID</option>
                                                        </select>
                                                    </div>

                                                    <div class="text-danger"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-12 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Name of Workman</label>
                                                    <input type="text" name="workman_name" id="workman_name"
                                                        class="form-control" readonly>
                                                    <div class="text-danger"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-12 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Designation</label>
                                                    <input type="text" name="workman_desig" id="workman_desig"
                                                        class="form-control" readonly>
                                                    <div class="text-danger"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 col-sm-6 col-12 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Department / Company</label>
                                                    <div class="col-sm-6" style="width: 100%">
                                                        <select name="workman_dept" id="workman_dept" style="width: 100%"
                                                            class="single-select form-control">
                                                            <option value="">Select Department</option>
                                                        </select>
                                                    </div>

                                                    <div class="text-danger"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-12 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Nature of Job</label>
                                                    <input type="text" name="nature_of_job" id="nature_of_job"
                                                        class="form-control">
                                                    <div class="text-danger"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-12 mb-3 d-flex align-items-end">
                                                <x-button-add dataId="" class="add btn btn-primary"
                                                    href="{{ admin_url('ptw/typeofworkmaster/add') }}">Add</x-button-add>
                                            </div>
                                        </div>


                                        <div class="table-responsive mt-3">
                                            <table class="table table-bordered text-center">
                                                <thead class="text-white" style="background-color:#5b626b">
                                                    <tr>
                                                        <th>Employee Code / Visitor ID</th>
                                                        <th>Name of Workman</th>
                                                        <th>Designation</th>
                                                        <th>Department / Company</th>
                                                        <th>Nature of Job</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="workman-list-entries">
                                                    @foreach ($workman as $item)
                                                        <tr>
                                                            <td>{{ getEmployeeId($item->emp_id) }}</td>
                                                            <td>{{ $item->workman_name }}</td>
                                                            <td>{{ $item->workman_desig }}</td>
                                                            <td>{{ getDepartment($item->workman_dept) }}</td>
                                                            <td>{{ $item->nature_of_job }}</td>
                                                            <td>
                                                                <button
                                                                    class="btn btn-danger btn-sm remove-entry">Remove</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>



                                        <div class="row mt-2">


                                            <div class="col-12 col-md-6">
                                                <div class="p-3 mb-1">
                                                    <label class="fw-bold fs-5 mt-1" for="assigned_job">
                                                        Are all above employee competent for assigned job & physically fit
                                                        for duty (Yes/No)
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="checkbox" id="assigned_job" name="assigned_job"
                                                        value="1"
                                                        {{ isset($safetypermit['assigned_job']) && $safetypermit['assigned_job'] == 1 ? 'checked' : '' }}>
                                                    <div class="text-danger"></div>
                                                </div>
                                            </div>


                                            <div class="col-12 col-md-6">
                                                <div class="form-group form-input mt-3">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <label for="description"
                                                            class="form-label require me-2 mb-2 mb-md-0"
                                                            style="flex-shrink: 0;">
                                                            Total number of attendance in Tool box Talk
                                                        </label>
                                                        <input type="text" name="attendance_toolbox_talk"
                                                            id="attendance_toolbox_talk" class="form-control"
                                                            style="flex-grow: 1; max-width: 300px;"
                                                            value="{{ $safetypermit['attendance_toolbox_talk'] }}">
                                                    </div>
                                                    <div class="text-danger"></div>
                                                </div>
                                            </div>


                                        </div>

                                        <p class="fw-bold fs-5 mt-3">Note</span>
                                        </p>
                                        <div class="row" style="background: #d6f5e0b0;">
                                            <div class="col-12 col-md-12 p-2">
                                                <div class="card p-3 rounded m-2">
                                                    <div class="row g-3">
                                                        <div class="col-12">

                                                            <ol class="mb-0" style="line-height: 1.8;">
                                                                <li>Work Permit is mandatory for non routine work, third
                                                                    party working agency & high risk Job.
                                                                </li>
                                                                <li>Work Permit is valid for 8 hours / Renewal may be
                                                                    extended as per unit head approval.</li>
                                                                <li>SWP to be discussed in tool box talk before start job.
                                                                </li>
                                                                <li>Work Permit will be canceled in case of emergency i.e
                                                                    Fire, weather condition, disaster etc.</li>
                                                                <li>Work permit is not valid without signature of Requestor,
                                                                    Verifier & Approver.</li>
                                                                <li> Safe Work procedure & method of statement must be
                                                                    discussed in the tool box talk.</li>
                                                                <li>Permit to be signed by (Requestor, Verifier & Approver)
                                                                    people not less than Site Engineer / Floor Manager</li>
                                                                <li>Permit Safety compliance shall be discussed to all
                                                                    involved person in local language.</li>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('safetypermit/list') }}"></x-button-cancel>
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


            $(document).ready(function() {
                const today = new Date();
                const currentTime = today.getHours() + ":" + today.getMinutes().toString().padStart(2, "0");

                flatpickr("#date_picker", {
                    minDate: today,
                    dateFormat: "d-m-Y",
                    defaultDate: "{{ $safetypermit->date }}",
                    onChange: function(selectedDates) {
                        const selectedDate = selectedDates[0];

                        if (selectedDate) {
                            startTimePicker.setDate(null);
                            endTimePicker.setDate(null);

                            if (selectedDate.toDateString() === today.toDateString()) {
                                startTimePicker.set({
                                    minTime: currentTime,
                                    maxTime: "18:00",
                                });
                                endTimePicker.set({
                                    minTime: currentTime,
                                    maxTime: "18:00",
                                });
                            } else {
                                startTimePicker.set({
                                    minTime: "09:00",
                                    maxTime: "18:00",
                                });
                                endTimePicker.set({
                                    minTime: "09:00",
                                    maxTime: "18:00",
                                });
                            }
                        }
                    },
                });

                const startTimePicker = flatpickr("#time_from_picker", {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    defaultDate: "{{ $safetypermit->time_from }}",
                    minTime: "{{ $safetypermit->date && $safetypermit->date === now()->format('d-m-Y') ? currentTime : '09:00' }}",
                    maxTime: "18:00",
                    onChange: function(selectedDates, dateStr) {
                        if (selectedDates.length > 0) {
                            endTimePicker.set("minTime",
                            dateStr);
                        }
                    },
                });

                const endTimePicker = flatpickr("#time_to_picker", {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    defaultDate: "{{ $safetypermit->time_to }}",
                    minTime: "{{ $safetypermit->time_from ?? currentTime }}",
                    maxTime: "18:00",
                });
            });


            $('#employee_code').select2({
                ajax: {
                    url: '{{ admin_url('safetypermit/employeeid') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.text
                                };
                            })
                        };
                    }
                },
                minimumInputLength: 1,
                dropdownCssClass: 'form-control',
                selectionCssClass: 'form-control'
            });


            $(document).on("change", "#employee_code", function() {
                var emp_id = $(this).val();
                var currentRow = $(this).closest(".row");
                var departmentDropdown = currentRow.find(
                    'select[name="workman_dept"]');

                if (emp_id) {
                    $.ajax({
                        url: "{{ url('safetypermit/fetchEmployeeDetails') }}/" + emp_id,
                        type: "GET",
                        success: function(data) {

                            if (data.employee) {

                                currentRow.find('input[name="workman_name"]').val(data.employee
                                    .emp_name);
                                currentRow.find('input[name="workman_desig"]').val(data.employee
                                    .designation);

                                departmentDropdown.empty();
                                departmentDropdown.append(
                                    '<option value="">Select Department</option>'
                                );

                                if (data.departments && data.departments.length > 0) {
                                    data.departments.forEach(function(department) {
                                        var selected = data.employee.department ==
                                            department.id ? "selected" : "";
                                        departmentDropdown.append(
                                            `<option value="${department.id}" ${selected}>${department.department_name}</option>`
                                        );
                                    });
                                } else {
                                    departmentDropdown.append(
                                        '<option value="">No departments available</option>'
                                    );
                                }
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: "Employee data could not be fetched.",
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "An error occurred while fetching employee details.",
                            });
                        },
                    });
                } else {

                    currentRow.find('input[name="workman_name"]').val("");
                    currentRow.find('input[name="workman_desig"]').val("");
                    departmentDropdown.empty();
                    departmentDropdown.append('<option value="">Select Department</option>');
                }
            });


            $(document).on("click", ".add", function(e) {
                e.preventDefault();

                var parentRow = $(this).closest(".row");

                var employeeCode = parentRow.find('select[name="employee_code"] option:selected').val();
                var employeeName = parentRow.find('select[name="employee_code"] option:selected').text();
                var workmanName = parentRow.find('input[name="workman_name"]').val();
                var designation = parentRow.find('input[name="workman_desig"]').val();
                var department = parentRow.find('select[name="workman_dept"] option:selected').val();
                var departmentName = parentRow.find('select[name="workman_dept"] option:selected').text();
                var natureOfJob = parentRow.find('input[name="nature_of_job"]').val();

                var isValid = true;

                parentRow.find('.text-danger').text("");

                if (!employeeCode) {
                    parentRow.find('select[name="employee_code"]').closest('.form-group').find(
                        '.text-danger').text("Employee Code is required.");
                    isValid = false;
                }
                if (!workmanName) {
                    parentRow.find('input[name="workman_name"]').closest('.form-group').find('.text-danger')
                        .text("Workman Name is required.");
                    isValid = false;
                }
                if (!department) {
                    parentRow.find('select[name="workman_dept"]').closest('.form-group').find(
                        '.text-danger').text("Department is required.");
                    isValid = false;
                }
                if (!natureOfJob || !/^[a-zA-Z\s]{3,30}$/.test(natureOfJob)) {
                    parentRow.find('input[name="nature_of_job"]').closest('.form-group').find(
                        '.text-danger').text(
                        "Nature of Job should only contain letters and spaces (3 to 30 characters).");
                    isValid = false;
                }

                if (isValid) {
                    var newRow = `
                        <tr>
                            <td><input type="hidden" name="emp_id[]" value="${employeeCode}">${employeeName}</td>
                            <td><input type="hidden" name="workman_name[]" value="${workmanName}">${workmanName}</td>
                            <td><input type="hidden" name="workman_desig[]" value="${designation}">${designation}</td>
                            <td><input type="hidden" name="workman_dept[]" value="${department}">${departmentName}</td>
                            <td><input type="hidden" name="nature_of_job[]" value="${natureOfJob}">${natureOfJob}</td>
                            <td>
                                <button class="btn btn-danger btn-sm remove-entry">Remove</button>
                            </td>
                        </tr>
                    `;

                    $("#workman-list-entries").append(newRow);

                    parentRow.find('select[name="employee_code"]').val("").trigger("change");
                    parentRow.find('input[name="workman_name"]').val("");
                    parentRow.find('input[name="workman_desig"]').val("");
                    parentRow.find('select[name="workman_dept"]').val("").trigger("change");
                    parentRow.find('input[name="nature_of_job"]').val("");

                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: "Workman has been added to the table.",
                    });
                }
            });



            $(document).on("click", ".remove-entry", function() {
                $(this).closest("tr").remove();
            });

            $('#employeenameshutdown,#employeenameloto').select2({
                ajax: {
                    url: '{{ admin_url('safetypermit/employeename') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.text
                                };
                            })
                        };
                    }
                },
                minimumInputLength: 1,
                dropdownCssClass: 'form-control',
                selectionCssClass: 'form-control'
            });


            $('#shutdown-checkbox').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#employeenameshutdown').prop('disabled', false);
                } else {
                    $('#employeenameshutdown').prop('disabled', true);
                }
            });
            $('#lotocheckbox').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#employeenameloto').prop('disabled', false);
                } else {
                    $('#employeenameloto').prop('disabled', true);
                }
            });

            $('#isolationpanel_checkbox').change(function() {
                if ($(this).prop('checked')) {
                    $('.isolationpanel_description').prop('disabled', false);
                } else {
                    $('.isolationpanel_description').prop('disabled', true);
                }
            });
        });

        // flatpickr("#date", {
        //     minDate: new Date(),
        //     dateFormat: "d-m-Y",
        //     minuteIncrement: 5,
        // });

        const fromTimePicker = flatpickr("#from_PPMTime", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            onChange: function(selectedDates, dateStr, instance) {

                const fromTimeValue = selectedDates[0];
                if (fromTimeValue) {
                    endTimePicker.set('disable', [
                        function(date) {
                            return date.getHours() === fromTimeValue.getHours() && date.getMinutes() ===
                                fromTimeValue.getMinutes();
                        }
                    ]);
                }
            }
        });

        const endTimePicker = flatpickr("#to_PPMTime", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            minTime: "00:00",
            onChange: function(selectedDates, dateStr, instance) {

                const fromTimeValue = fromTimePicker.selectedDates[0];
                if (fromTimeValue && selectedDates[0] <= fromTimeValue) {

                    endTimePicker.setDate(fromTimeValue, true);
                }
            }
        });



        fromTimePicker.config.onChange.push(function(selectedDates, dateStr, instance) {
            const fromTimeValue = selectedDates[0];
            if (fromTimeValue) {

                endTimePicker.set("minTime", dateStr);
            }
        });

        const fromPicker = flatpickr("#time_from", {
            enableTime: true,
            noCalendar: true,
            time_24hr: true,
            minuteIncrement: 5,
            dateFormat: "H:i",
            minDate: new Date(),
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    let fromTime = selectedDates[0];


                    let toTime = new Date(fromTime.getTime() + 8 * 60 * 60 * 1000);


                    let hours = String(toTime.getHours()).padStart(2, '0');
                    let minutes = String(toTime.getMinutes()).padStart(2, '0');
                    let formattedTime = `${hours}:${minutes}`;


                    document.getElementById("time_to").value = formattedTime;
                }
            }
        });


        document.getElementById("time_to").readOnly = true;
    </script>
    <script type="text/javascript">
        $(document).ready(function() {

            var firstTh = $('.datatable-list thead th:first');
            firstTh.removeClass('sorting_asc');
        });

        $(function() {
            /* Datatable */
            var table = $('.datatable-list').DataTable({
                autoWidth: false,
                responsive: true,
                processing: false,
                serverSide: true,
                searching: false,
                ordering: false,
                lengthChange: false, // Disables "Show entries"
                // dom: 'Bfrtip',
                layout: {
                    top2Start: '',
                    top2End: {
                        search: {
                            placeholder: ''
                        }
                    },
                    topStart: '',
                    topEnd: '',
                    bottomStart: '',
                    bottomEnd: '',
                    bottom2Start: 'info',
                    bottom2End: 'paging'
                },

                ajax: {
                    url: "{{ admin_url('uploadlog/list') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {}
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'line_no',
                        name: 'line_no'
                    },
                    {
                        data: 'error',
                        name: 'error'
                    },
                ],

                language: {
                    paginate: {
                        first: '<i title="{{ __('common.first') }}" class="fa fa-angle-double-left" aria-hidden="true"></i>',
                        last: '<i title="{{ __('common.last') }}" title="Next" class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        next: '<i title="{{ __('common.next') }}" class="fa fa-angle-right" aria-hidden="true"></i>',
                        previous: '<i title="{{ __('common.previous') }}" class="fa fa-angle-left" aria-hidden="true"></i>',
                    },
                    "info": "{{ __('common.dt_info') }}",
                    "infoEmpty": "{{ __('common.dt_infoEmpty') }}",
                    "infoFiltered": "{{ __('common.dt_infoFiltered') }}",
                },
                aLengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                buttons: [{
                        extend: 'collection',
                        text: '{{ __('common.export') }}',
                        buttons: [{
                            extend: 'excel',
                            text: '{{ __('common.excel') }}',
                            action: function(e, dt, button, config) {
                                var searchValue = $('#datatable-list_filter input').val();
                                $(".dt-button").removeClass('processing');
                                $('body').click();
                                window.location.href =
                                    "{{ admin_url('uploadlog/export/excel/' . request()->logid) }}" +
                                    '?search=' + searchValue;
                            }
                        }, ]
                    },

                    {
                        "extend": 'pageLength',
                        "text": '{{ __('common.show') }} 10 {{ __('common.records') }}'
                    }
                ],

            });

            table.on('length.dt', function(e, settings, len) {
                var text = '{{ __('common.show') }} ' + len + ' {{ __('common.records') }}';
                $('.buttons-page-length').find('span').text(text);
            });

            $(document).on('click', '#searchform', function() {
                table.draw();
            });

            $(document).on('click', '#resetform', function() {
                setTimeout(function() {
                    table.draw();
                }, 150);
            });

        });

        $(document).ready(function() {
            function toggleProtectiveChecklistContainer() {
                const container = $('#getprotectivechecklist-container');
                const anyWorkTypeChecked = $('.work-type-checkbox:checked').length > 0;


                if (anyWorkTypeChecked) {
                    container.show();
                } else {
                    container.hide();
                }
            }

            toggleProtectiveChecklistContainer();

            $('.work-type-checkbox').on('change', function() {
                toggleProtectiveChecklistContainer();
            });
        });


        $(document).ready(function() {
            const protectiveEquipmentMap = new Map();
            const selectedEquipmentsInvolved =
                @json($safetypermit->mapped_protective_equip);


            const addedWorkIds = new Set();


            function addEquipmentToContainer(workId, data) {
                const container = $('#getprotectivechecklist-container');
                data.forEach(function(item) {
                    const equipmentName = item.protective_equip;
                    const isDefaultChecked = item.default_enable == 1;

                    let isAlreadyInvolved = false;
                    if (Array.isArray(selectedEquipmentsInvolved)) {
                        isAlreadyInvolved = selectedEquipmentsInvolved
                            .some(function(existingItem) {
                                return existingItem.checkpoint_names.includes(equipmentName);
                            });
                    } else if (typeof selectedEquipmentsInvolved === 'object') {
                        Object.values(selectedEquipmentsInvolved)
                            .forEach(function(value) {
                                if (value.checkpoint_names && value.checkpoint_names.includes(
                                        equipmentName)) {
                                    isAlreadyInvolved = true;
                                }
                            });
                    }


                    if (!isAlreadyInvolved && !protectiveEquipmentMap.has(equipmentName)) {
                        const isChecked = isDefaultChecked ? 'checked' : '';
                        const checkpointHtml = `
                <div class="col-12 col-md-4 col-lg-4 d-flex align-items-center gap-2 checkpoint"
                    data-work-id="${workId}"
                    data-name="${equipmentName}">
                    <input type="checkbox"
                        class="protective-checkbox"
                        name="protective_equip[${workId}][]"
                        value="${item.id}"
                        id="checkpoint-${workId}-${item.id}"
                        ${isChecked}>
                    <label for="checkpoint-${workId}-${item.id}">${equipmentName}</label>
                </div>`;
                        container.append(checkpointHtml);


                        protectiveEquipmentMap.set(equipmentName, workId);
                    }
                });
            }


            $('.work-type-checkbox').on('change', function() {
                const workId = $(this).data('id');
                const checkboxState = $(this).prop('checked');

                if (checkboxState) {

                    $.ajax({
                        url: `{{ admin_url('safetypermit/getprotectivechecklist') }}/${workId}`,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {

                            addEquipmentToContainer(workId, data);

                            addedWorkIds.add(workId);
                        },
                        error: function(xhr, status, error) {
                            console.error(`Error fetching protective checklist: ${error}`);
                        }
                    });
                } else {

                    $('#getprotectivechecklist-container')
                        .find(`.checkpoint[data-work-id="${workId}"]`)
                        .each(function() {
                            const equipmentName = $(this).data('name');
                            protectiveEquipmentMap.delete(equipmentName);
                            $(this).remove();
                        });
                }
            });


            $('.work-type-checkbox').each(function() {
                if ($(this).prop('checked')) {
                    const workId = $(this).data('id');
                    if (!addedWorkIds.has(workId)) {
                        $.ajax({
                            url: `{{ admin_url('safetypermit/getprotectivechecklist') }}/${workId}`,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                addEquipmentToContainer(workId, data);
                                addedWorkIds.add(workId);
                            },
                            error: function(xhr, status, error) {
                                console.error(`Error fetching protective checklist: ${error}`);
                            }
                        });
                    }
                }
            });


            $('#safetyPermitadd').on('submit', function(e) {
                const container = $('#getprotectivechecklist-container');
                const errorDiv = $(
                    '<div class="text-danger">Please select at least one Protective Equipment.</div>');
                container.find('.text-danger').remove();

                let hasSelection = false;
                $('.protective-checkbox').each(function() {
                    if ($(this).is(':checked')) {
                        hasSelection = true;
                    }
                });

                if (!hasSelection) {
                    container.append(errorDiv);
                    e.preventDefault();
                }
            });


            $('#getprotectivechecklist-container').on('change', '.protective-checkbox', function() {
                const container = $('#getprotectivechecklist-container');
                const hasSelection = container.find('.protective-checkbox:checked').length > 0;

                if (hasSelection) {
                    container.find('.text-danger').remove();
                }
            });
        });


        $(document).ready(function() {
            function toggleEquipmentInvolve() {
                const container = $('#getequipmentinvolved-container');
                const anyWorkTypeChecked = $('.work-type-checkbox:checked').length > 0;


                if (anyWorkTypeChecked) {
                    container.show();
                } else {
                    container.hide();
                }
            }

            toggleEquipmentInvolve();

            $('.work-type-checkbox').on('change', function() {
                toggleEquipmentInvolve();
            });
        });



        // equipement involved

        $(document).ready(function() {
            const EquipmentInvolveMap = new Map();
            const selectedEquipmentsInvolved = @json($safetypermit->mapped_equiment_involved);
            const addedWorkIds = new Set();


            function addEquipmentToContainer(workId, data) {
                const container = $('#getequipmentinvolved-container');
                data.forEach(function(item) {
                    const equipmentName = item.equip_involve;
                    const isDefaultChecked = item.default_enable == 1;

                    let isAlreadyInvolved = false;
                    if (Array.isArray(selectedEquipmentsInvolved)) {
                        isAlreadyInvolved = selectedEquipmentsInvolved
                            .some(function(existingItem) {
                                return existingItem.checkpoint_names.includes(equipmentName);
                            });
                    } else if (typeof selectedEquipmentsInvolved === 'object') {
                        Object.values(selectedEquipmentsInvolved)
                            .forEach(function(value) {
                                if (value.checkpoint_names && value.checkpoint_names.includes(
                                        equipmentName)) {
                                    isAlreadyInvolved = true;
                                }
                            });
                    }


                    if (!isAlreadyInvolved && !EquipmentInvolveMap.has(equipmentName)) {
                        const isChecked = isDefaultChecked ? 'checked' : '';
                        const checkpointHtml = `
                <div class="col-12 col-md-4 col-lg-4 d-flex align-items-center gap-2 checkpoint"
                    data-work-id="${workId}"
                    data-name="${equipmentName}">
                    <input type="checkbox"
                        class="equiment_involved"
                        name="equiment_involved[${workId}][]"
                        value="${item.id}"
                        id="checkpoint-${workId}-${item.id}"
                        ${isChecked}>
                    <label for="checkpoint-${workId}-${item.id}">${equipmentName}</label>
                </div>`;
                        container.append(checkpointHtml);


                        EquipmentInvolveMap.set(equipmentName, workId);
                    }
                });
            }


            $('.work-type-checkbox').on('change', function() {
                const workId = $(this).data('id');
                const checkboxState = $(this).prop('checked');

                if (checkboxState) {

                    $.ajax({
                        url: `{{ admin_url('safetypermit/getequipmentinvolved') }}/${workId}`,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {

                            addEquipmentToContainer(workId, data);

                            addedWorkIds.add(workId);
                        },
                        error: function(xhr, status, error) {
                            console.error(`Error fetching protective checklist: ${error}`);
                        }
                    });
                } else {

                    $('#getequipmentinvolved-container')
                        .find(`.checkpoint[data-work-id="${workId}"]`)
                        .each(function() {
                            const equipmentName = $(this).data('name');
                            EquipmentInvolveMap.delete(equipmentName);
                            $(this).remove();
                        });
                }
            });


            $('.work-type-checkbox').each(function() {
                if ($(this).prop('checked')) {
                    const workId = $(this).data('id');
                    if (!addedWorkIds.has(workId)) {
                        $.ajax({
                            url: `{{ admin_url('safetypermit/getequipmentinvolved') }}/${workId}`,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                addEquipmentToContainer(workId, data);
                                addedWorkIds.add(workId);
                            },
                            error: function(xhr, status, error) {
                                console.error(`Error fetching Equipinvolve: ${error}`);
                            }
                        });
                    }
                }
            });


            $('#safetyPermitadd').on('submit', function(e) {
                const container = $('#getequipmentinvolved-container');
                const errorDiv = $(
                    '<div class="text-danger">Please select at least one Protective Equipment.</div>');
                container.find('.text-danger').remove();

                let hasSelection = false;
                $('.equiment_involved').each(function() {
                    if ($(this).is(':checked')) {
                        hasSelection = true;
                    }
                });

                if (!hasSelection) {
                    container.append(errorDiv);
                    e.preventDefault();
                }
            });


            $('#getequipmentinvolved-container').on('change', '.equiment_involved', function() {
                const container = $('#getequipmentinvolved-container');
                const hasSelection = container.find('.equiment_involved:checked').length > 0;

                if (hasSelection) {
                    container.find('.text-danger').remove();
                }
            });
        });

        $(document).ready(function() {
            function togglePrecaution() {
                const container = $('#getprecaution-container');
                const anyWorkTypeChecked = $('.work-type-checkbox:checked').length > 0;


                if (anyWorkTypeChecked) {
                    container.show();
                } else {
                    container.hide();
                }
            }

            togglePrecaution();

            $('.work-type-checkbox').on('change', function() {
                togglePrecaution();
            });
        });
        // Precaution
        $(document).ready(function() {
            const displayedPrecautions = new Map();
            const selectedEquipmentsInvolved = @json($safetypermit->mapped_precaution_taken);

            const addedWorkIds = new Set();


            function addEquipmentToContainer(workId, data) {
                const container = $('#getprecaution-container');
                data.forEach(function(item) {
                    const equipmentName = item.precaution;
                    const isDefaultChecked = item.default_enable == 1;

                    let isAlreadyInvolved = false;
                    if (Array.isArray(selectedEquipmentsInvolved)) {
                        isAlreadyInvolved = selectedEquipmentsInvolved
                            .some(function(existingItem) {
                                return existingItem.checkpoint_names.includes(equipmentName);
                            });
                    } else if (typeof selectedEquipmentsInvolved === 'object') {
                        Object.values(selectedEquipmentsInvolved)
                            .forEach(function(value) {
                                if (value.checkpoint_names && value.checkpoint_names.includes(
                                        equipmentName)) {
                                    isAlreadyInvolved = true;
                                }
                            });
                    }


                    if (!isAlreadyInvolved && !displayedPrecautions.has(equipmentName)) {
                        const isChecked = isDefaultChecked ? 'checked' : '';
                        const checkpointHtml = `
                <div class="col-12 col-md-12  d-flex align-items-center gap-2 checkpoint"
                    data-work-id="${workId}"
                    data-name="${equipmentName}">
                    <input type="checkbox"
                        class="precaution_taken"
                        name="precaution_taken[${workId}][]"
                        value="${item.id}"
                        id="checkpoint-${workId}-${item.id}"
                        ${isChecked}>
                    <label for="checkpoint-${workId}-${item.id}">${equipmentName}</label>
                </div>`;
                        container.append(checkpointHtml);


                        displayedPrecautions.set(equipmentName, workId);
                    }
                });
            }


            $('.work-type-checkbox').on('change', function() {
                const workId = $(this).data('id');
                const checkboxState = $(this).prop('checked');

                if (checkboxState) {

                    $.ajax({
                        url: `{{ admin_url('safetypermit/getprecaution') }}/${workId}`,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {

                            addEquipmentToContainer(workId, data);

                            addedWorkIds.add(workId);
                        },
                        error: function(xhr, status, error) {
                            console.error(`Error fetching Precaution: ${error}`);
                        }
                    });
                } else {

                    $('#getprecaution-container')
                        .find(`.checkpoint[data-work-id="${workId}"]`)
                        .each(function() {
                            const equipmentName = $(this).data('name');
                            displayedPrecautions.delete(equipmentName);
                            $(this).remove();
                        });
                }
            });


            $('.work-type-checkbox').each(function() {
                if ($(this).prop('checked')) {
                    const workId = $(this).data('id');
                    if (!addedWorkIds.has(workId)) {
                        $.ajax({
                            url: `{{ admin_url('safetypermit/getprecaution') }}/${workId}`,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                addEquipmentToContainer(workId, data);
                                addedWorkIds.add(workId);
                            },
                            error: function(xhr, status, error) {
                                console.error(`Error fetching protective checklist: ${error}`);
                            }
                        });
                    }
                }
            });
        });


        $(document).ready(function() {
            function togglechecklist() {
                const container = $('#getchecklist-container');
                const anyWorkTypeChecked = $('.work-type-checkbox:checked').length > 0;


                if (anyWorkTypeChecked) {
                    container.show();
                } else {
                    container.hide();
                }
            }

            togglechecklist();

            $('.work-type-checkbox').on('change', function() {
                togglechecklist();
            });
        });

        // Equipment Check list

        $(document).ready(function() {
            const displayedEquipment = new Map();
            const selectedEquipmentsInvolved = @json($safetypermit->mapped_equipment_checklist);

            const addedWorkIds = new Set();


            function addEquipmentToContainer(workId, data) {
                const container = $('#getchecklist-container');
                data.forEach(function(item) {
                    const equipmentName = item.checklist;
                    const isDefaultChecked = item.default_enable == 1;

                    let isAlreadyInvolved = false;
                    if (Array.isArray(selectedEquipmentsInvolved)) {
                        isAlreadyInvolved = selectedEquipmentsInvolved
                            .some(function(existingItem) {
                                return existingItem.checkpoint_names.includes(equipmentName);
                            });
                    } else if (typeof selectedEquipmentsInvolved === 'object') {
                        Object.values(selectedEquipmentsInvolved)
                            .forEach(function(value) {
                                if (value.checkpoint_names && value.checkpoint_names.includes(
                                        equipmentName)) {
                                    isAlreadyInvolved = true;
                                }
                            });
                    }

                    if (!isAlreadyInvolved && !displayedEquipment.has(equipmentName)) {
                        const isChecked = isDefaultChecked ? 'checked' : '';
                        const checkpointHtml = `
                <div class="col-12 col-md-12  d-flex align-items-center gap-2 checkpoint"
                    data-work-id="${workId}"
                    data-name="${equipmentName}">
                    <input type="checkbox"
                        class="equipment_checklist"
                        name="equipment_checklist[${workId}][]"
                        value="${item.id}"
                        id="checkpoint-${workId}-${item.id}"
                        ${isChecked}>
                    <label for="checkpoint-${workId}-${item.id}">${equipmentName}</label>
                </div>`;
                        container.append(checkpointHtml);


                        displayedEquipment.set(equipmentName, workId);
                    }
                });
            }


            $('.work-type-checkbox').on('change', function() {
                const workId = $(this).data('id');
                const checkboxState = $(this).prop('checked');

                if (checkboxState) {

                    $.ajax({
                        url: `{{ admin_url('safetypermit/getchecklist') }}/${workId}`,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {

                            addEquipmentToContainer(workId, data);

                            addedWorkIds.add(workId);
                        },
                        error: function(xhr, status, error) {
                            console.error(`Error fetching protective checklist: ${error}`);
                        }
                    });
                } else {

                    $('#getchecklist-container')
                        .find(`.checkpoint[data-work-id="${workId}"]`)
                        .each(function() {
                            const equipmentName = $(this).data('name');
                            displayedEquipment.delete(equipmentName);
                            $(this).remove();
                        });
                }
            });


            $('.work-type-checkbox').each(function() {
                if ($(this).prop('checked')) {
                    const workId = $(this).data('id');
                    if (!addedWorkIds.has(workId)) {
                        $.ajax({
                            url: `{{ admin_url('safetypermit/getchecklist') }}/${workId}`,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                addEquipmentToContainer(workId, data);
                                addedWorkIds.add(workId);
                            },
                            error: function(xhr, status, error) {
                                console.error(`Error fetching Checklist: ${error}`);
                            }
                        });
                    }
                }
            });
        });

        $(document).ready(function() {
            function togglesafework() {
                const container = $('#getinstruction-container');
                const anyWorkTypeChecked = $('.work-type-checkbox:checked').length > 0;


                if (anyWorkTypeChecked) {
                    container.show();
                } else {
                    container.hide();
                }
            }

            togglesafework();

            $('.work-type-checkbox').on('change', function() {
                togglesafework();
            });
        });


        // safe work instruction
        $(document).ready(function() {
            const displayedEquipment = new Map();
            const selectedEquipmentsInvolved = @json($safetypermit->mapped_safework_instruction);

            const addedWorkIds = new Set();


            function addEquipmentToContainer(workId, data) {
                const container = $('#getinstruction-container');
                data.forEach(function(item) {
                    const equipmentName = item.safe_work;
                    const isDefaultChecked = item.default_enable == 1;

                    let isAlreadyInvolved = false;
                    if (Array.isArray(selectedEquipmentsInvolved)) {
                        isAlreadyInvolved = selectedEquipmentsInvolved
                            .some(function(existingItem) {
                                return existingItem.checkpoint_names.includes(equipmentName);
                            });
                    } else if (typeof selectedEquipmentsInvolved === 'object') {
                        Object.values(selectedEquipmentsInvolved)
                            .forEach(function(value) {
                                if (value.checkpoint_names && value.checkpoint_names.includes(
                                        equipmentName)) {
                                    isAlreadyInvolved = true;
                                }
                            });
                    }


                    if (!isAlreadyInvolved && !displayedEquipment.has(equipmentName)) {
                        const isChecked = isDefaultChecked ? 'checked' : '';
                        const checkpointHtml = `
                <div class="col-12 col-md-12  d-flex align-items-center gap-2 checkpoint"
                    data-work-id="${workId}"
                    data-name="${equipmentName}">
                    <input type="checkbox"
                        class="safework_instruction"
                        name="safework_instruction[${workId}][]"
                        value="${item.id}"
                        id="checkpoint-${workId}-${item.id}"
                        ${isChecked}>
                    <label for="checkpoint-${workId}-${item.id}">${equipmentName}</label>
                </div>`;
                        container.append(checkpointHtml);


                        displayedEquipment.set(equipmentName, workId);
                    }
                });
            }


            $('.work-type-checkbox').on('change', function() {
                const workId = $(this).data('id');
                const checkboxState = $(this).prop('checked');

                if (checkboxState) {

                    $.ajax({
                        url: `{{ admin_url('safetypermit/getinstruction') }}/${workId}`,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {

                            addEquipmentToContainer(workId, data);

                            addedWorkIds.add(workId);
                        },
                        error: function(xhr, status, error) {
                            console.error(`Error fetching protective safe_work: ${error}`);
                        }
                    });
                } else {

                    $('#getinstruction-container')
                        .find(`.checkpoint[data-work-id="${workId}"]`)
                        .each(function() {
                            const equipmentName = $(this).data('name');
                            displayedEquipment.delete(equipmentName);
                            $(this).remove();
                        });
                }
            });


            $('.work-type-checkbox').each(function() {
                if ($(this).prop('checked')) {
                    const workId = $(this).data('id');
                    if (!addedWorkIds.has(workId)) {
                        $.ajax({
                            url: `{{ admin_url('safetypermit/getinstruction') }}/${workId}`,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                addEquipmentToContainer(workId, data);
                                addedWorkIds.add(workId);
                            },
                            error: function(xhr, status, error) {
                                console.error(`Error fetching protective safe_work: ${error}`);
                            }
                        });
                    }
                }
            });
        });




        $(document).ready(function() {
            const section1Inputs = $('#section-1 input');
            const checkboxWithValue8 = $('.work-type-checkbox[data-id="8"]');

            section1Inputs.prop('disabled', true);

            checkboxWithValue8.on('change', function() {
                if ($(this).is(':checked')) {

                    section1Inputs.prop('disabled', false);
                } else {

                    section1Inputs.prop('disabled', true);
                }
            });

        });

        $(document).ready(function() {
            const shutdownCheckbox = $('#shutdown-checkbox');
            const targetInputs = $('.shutdowncheckbox').not('#shutdown-checkbox');


            targetInputs.prop('disabled', true);


            shutdownCheckbox.on('change', function() {
                if ($(this).is(':checked')) {
                    targetInputs.prop('disabled', false);
                } else {

                    targetInputs.prop('disabled', true);
                }
            });
        });

        $(document).ready(function() {
            const lotoCheckbox = $('#loto-checkbox');
            const targetInputs = $('.lotocheckbox').not('#loto-checkbox');


            targetInputs.prop('disabled', true);


            lotoCheckbox.on('change', function() {
                if ($(this).is(':checked')) {
                    targetInputs.prop('disabled', false);
                } else {

                    targetInputs.prop('disabled', true);
                }
            });
        });

        // validation

        $(document).ready(function() {
            $.validator.addMethod("regex", function(value, element, regexp) {
                return this.optional(element) || regexp.test(value);
            }, "Please check your input.");

            $('#safetyPermitadd').validate({
                rules: {
                    date: {
                        required: true,
                    },
                    time_from: {
                        required: true,
                    },
                    time_to: {
                        required: true,
                    },
                    unit_id: {
                        required: true,
                    },
                    exact_location_job: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        regex: /^[a-zA-Z, ]{3,30}$/
                    },
                    job_location_area: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        regex: /^[a-zA-Z, ]{3,50}$/
                    },

                    'sub_permit[]': {
                        required: true,
                        minlength: 1
                    },

                    job_description: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                    },


                    equipment_checklist_inspection: {
                        required: true,
                    },
                    toolbox_talk: {
                        required: true,
                    },
                    talk_givenby: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,

                    },


                    assigned_job: {
                        required: true,
                    },
                    attendance_toolbox_talk: {
                        required: true,
                        digits: true,
                    }

                },
                messages: {

                    date: {
                        required: "Date cannot be empty.",
                    },
                    time_from: {
                        required: "From Time cannot be empty.",
                    },
                    time_to: {
                        required: "To Time cannot be empty.",
                    },
                    unit_id: {
                        required: "Please Select the unit.",
                    },
                    exact_location_job: {
                        required: "Exact Job Location cannot be empty.",
                        minlength: "Exact Job Location between 3 and 30 characters.",
                        maxlength: "Exact Job Location between 3 and 30 characters.",
                        regex: "Exact Job Location contains only the letters",
                    },
                    job_location_area: {
                        required: "Job Location Area cannot be empty.",
                        minlength: "Job Location Area between 3 and 30 characters.",
                        maxlength: "Job Location Area between 3 and 30 characters.",
                        regex: "Job Location Area contains only the letters",
                    },
                    'sub_permit[]': {
                        required: "At least one work type should be selected."
                    },
                    job_description: {
                        required: "Job Description cannot be empty.",
                        minlength: "Job Description between 3 and 600 characters.",
                        maxlength: "Job Description between 3 and 600 characters.",
                    },




                    equipment_checklist_inspection: {
                        required: "Equipment Checklist Inspection is required.",
                    },
                    toolbox_talk: {
                        required: "Toolbox Talk is required.",
                    },
                    talk_givenby: {
                        required: "Talk Given By is required",
                        minlength: "Name is between 3 to 30 characters",
                        maxlength: "Name is between 3 to 30 characters",

                    },

                    assigned_job: {
                        required: "Please check the Assigned job",
                    },
                    attendance_toolbox_talk: {
                        required: "Attendance Tool box talk is required ",
                        regex: "Attendance Tool box talk Numeric only Accepted ",

                    }

                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    if (element.hasClass('work-type-checkbox')) {

                        var errorDiv = element.closest('.work-type').find(
                            '.text-danger');
                        errorDiv.html(error);
                    } else if (element.is(':checkbox')) {

                        var errorDiv = element.siblings('.text-danger');
                        errorDiv.html(error);
                    } else {

                        var errorDiv = element.closest('.form-group').find(
                            '.text-danger');
                        if (errorDiv.length === 0) {
                            errorDiv = element.siblings('.text-danger');
                        }
                        errorDiv.html(error);
                    }
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
                    console.log(errors + " field(s) are invalid");
                    validator.errorList.forEach(function(error) {
                        console.log("Field: " + error.element.name +
                            ", Error: " + error
                            .message);
                    });
                }
            });

            $('#shutdown-checkbox').change(function() {
                $(this).valid();
            });

            $('#loto-checkbox').change(function() {
                $(this).valid();
            });

            $.validator.addMethod("regex", function(value, element, regexp) {
                return this.optional(element) || regexp.test(value);
            }, "Please check your input.");
        });
    </script>
@endpush
