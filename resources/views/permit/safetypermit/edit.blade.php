@extends('admin.layouts.admin')
@section('title', 'Safety Permit Edit')
@section('pageurl', admin_url('safetypermit/list'))


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
                                    <x-button-back href="{{ admin_url('safetypermit/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="pperequestadd"
                                        action="{{ admin_url('safetypermit/add/submit') }}">
                                        @csrf
                                        <input type="hidden" id="id" name="id"
                                            value="{{ encryptId($safetypermit->id) }}">
                                        <div class="row ">
                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Date</label>
                                                    <input type="text" name="date" id="date" class="form-control"
                                                        value = "{{ $safetypermit->date }}">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Time(From)</label>
                                                    <input type="text" name="time_from" id="time_from"
                                                        class="form-control" placeholder="Time(From)"
                                                        value="{{ $safetypermit->time_from }}">
                                                </div>
                                            </div>


                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Time(To)</label>
                                                    <input type="text" name="time_to" id="time_to" class="form-control"
                                                        placeholder="Time(To)" value="{{ $safetypermit->time_to }}">
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
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Exact location of job</label>
                                                    <input type="text" name="exact_location_job" id="exact_location_job"
                                                        class="form-control" placeholder="Exact location of job"
                                                        value="{{ $safetypermit->exact_location_job }}">
                                                </div>
                                            </div>


                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Job Location & Area</label>
                                                    <input type="text" name="job_location_area" id="job_location_area"
                                                        class="form-control" placeholder="Job Location & Area"
                                                        value="{{ $safetypermit->job_location_area }}">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Work Permit No</label>
                                                    <input type="text" name="permit_id" id="permit_id"
                                                        class="form-control" value="{{ $safetypermit->permit_id }}">
                                                </div>
                                            </div>
                                        </div>
                                        <p class="fw-bold fs-5 mt-3">Type of Job: Please Tick Mark (<i
                                                class="fas fa-check text-primary"></i>)
                                            <span class="text-danger">*</span>
                                        </p>
                                        <div class="row" style="background: #d6f5e0b0;">
                                            <div class="col-12">
                                                <div class="card p-3 rounded m-3">
                                                    <div class="row g-3">
                                                        @php
                                                            $subPermitIds = explode(',', $safetypermit->sub_permit);
                                                        @endphp
                                                        @foreach ($typeofwork as $work)
                                                            <div class="col-12 col-md-4 d-flex align-items-center gap-2">
                                                                <input type="hidden" name=""
                                                                    value="{{ $work->id }}">
                                                                <input type="checkbox" class="work-type-checkbox"
                                                                    data-id="{{ $work->id }}" name="sub_permit[]"
                                                                    value="{{ $work->id }}"
                                                                    @if (in_array($work->id, $subPermitIds)) checked data-checked="true" @endif>
                                                                <a href="{{ asset($work->file_path) }}" target="_blank">
                                                                    <img src="{{ asset($work->file_path) }}"
                                                                        alt="Image" class="img-fluid"
                                                                        style="max-width: 50px; object-fit: cover;">
                                                                </a>
                                                                <span>{{ $work->work_name }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Job description --}}
                                        <div class="row mb-3 mt-2">
                                            <div class="col-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Job Description</label>
                                                    <textarea name="job_description" class="form-control" placeholder="Job Description">{{ $safetypermit->job_description }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row border p-3 mx-1">
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <img src="{{ url('public/assets/images/safetypermit/power-off.png') }}"
                                                        class="img-fluid" style="width: 50px; height: 50px;">
                                                    <label class="form-label mb-0">Shut Down Required (Yes/No)</label>
                                                    <input type="checkbox" id="shutdown-checkbox"
                                                        class="validate-radio-required" name="shutdown_req"
                                                        value="1" {{$safetypermit->shutdown_req == 1? 'checked':''}}>
                                                </div>

                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <img src="{{ url('public/assets/images/safetypermit/profile.png') }}"
                                                        class="img-fluid" style="width: 50px; height: 50px;">
                                                    <label class="form-label mb-0">Taken By (Name & Department)</label>
                                                    <select name="shut_down_takenby" id="employeenameshutdown"
                                                    class="form-control shutdowncheckbox" >
                                                    <option value="">Select Person</option>

                                                </select>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row border p-3 mx-1">
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <img src="{{ url('public/assets/images/safetypermit/process.png') }}"
                                                        class="img-fluid" style="width: 50px; height: 50px;">
                                                    <label class="form-label mb-0 ">Isolation/LOTO Required
                                                        (Yes/No)</label>
                                                    <input type="checkbox" id="loto-checkbox"
                                                        class="validate-radio-required" name= "loto_req" {{$safetypermit->loto_req == 1? 'checked':''}}>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <img src="{{ url('public/assets/images/safetypermit/profile.png') }}"
                                                        class="img-fluid" style="width: 50px; height: 50px;">
                                                    <label class="form-label mb-0 ">Taken By (Name & Department)</label>


                                                    <select name="loto_takenby" id="employeenameloto"
                                                        class="form-control lotocheckbox" disabled>
                                                        <option value="">Select Person</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row border p-3 mx-1">
                                            <div class="col-12 col-md-4 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <label class="form-label mb-0">Loto No</label>
                                                    <input type="text" name="loto_no"
                                                        class="form-control lotocheckbox" placeholder="Loto No" value="{{$safetypermit->loto_no}}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-8 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <label class="form-label mb-0 ">Tag Field properly (Yes/No)</label>
                                                    <input type="checkbox"
                                                        class="validate-radio-required shutdowncheckbox" name="tagfield"
                                                        {{$safetypermit->loto_takenby ? 'checked':'disabled'}}>
                                                </div>
                                            </div>
                                        </div>

                                       {{-- state of isolation --}}

                                       <div class="row col-md-12 d-flex mt-3">
                                        <!-- Left Side: Scrollable on X-Axis -->
                                        <div class="col-md-4">
                                            <p class="fw-bold fs-5 mt-3">State of Isolation & LOTO</p>
                                            <div class="scroll-container border p-3"
                                                style="overflow-x: auto; white-space: nowrap; width: 100%;">
                                                <!-- First Row -->
                                                <div class="row mb-3"
                                                    style="display: flex; flex-wrap: nowrap; justify-content: flex-start; align-items: center;">
                                                    <div class="d-inline-block"
                                                        style="margin: 0; padding: 0; flex-shrink: 0;">
                                                        <div class="form-group d-flex align-items-center gap-1">
                                                            <img src="{{ url('public/assets/images/safetypermit/person.png') }}"
                                                                class="img-fluid" style="width: 50px; height: 50px;">
                                                            <label class="form-label mb-0">Air</label>
                                                            <input type="checkbox"
                                                                class="validate-radio-required shutdowncheckbox"
                                                                name="state_isolation_loto[]" value="Air" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="d-inline-block"
                                                        style="margin-left: -200px; padding: 0; flex-shrink: 0;">
                                                        <div class="form-group d-flex align-items-center gap-1">
                                                            <img src="{{ url('public/assets/images/safetypermit/natural-gas.png') }}"
                                                                class="img-fluid" style="width: 50px; height: 50px;">
                                                            <label class="form-label mb-0">Gas</label>
                                                            <input type="checkbox"
                                                                class="validate-radio-required shutdowncheckbox"
                                                                name="state_isolation_loto[]" value="Gas" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="d-inline-block"
                                                        style="margin-left: -200px; padding: 0; flex-shrink: 0;">
                                                        <div class="form-group d-flex align-items-center gap-1">
                                                            <label class="form-label mb-0">Others if any please
                                                                specify</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Second Row -->
                                                <div class="row mb-3"
                                                    style="display: flex; flex-wrap: nowrap; justify-content: flex-start; align-items: center;">
                                                    <div class="d-inline-block"
                                                        style="margin: 0; padding: 0; flex-shrink: 0;">
                                                        <div class="form-group d-flex align-items-center gap-1">
                                                            <img src="{{ url('public/assets/images/safetypermit/electrician.png') }}"
                                                                class="img-fluid" style="width: 50px; height: 50px;">
                                                            <label class="form-label mb-0">Electrical</label>
                                                            <input type="checkbox"
                                                                class="validate-radio-required shutdowncheckbox"
                                                                name="state_isolation_loto[]" value="Electrical"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                    <div class="d-inline-block"
                                                        style="margin-left: -200px; padding: 0; flex-shrink: 0;">
                                                        <div class="form-group d-flex align-items-center gap-1">
                                                            <img src="{{ url('public/assets/images/safetypermit/leak.png') }}"
                                                                class="img-fluid" style="width: 50px; height: 50px;">
                                                            <label class="form-label mb-0">Water/Liquid</label>
                                                            <input type="checkbox"
                                                                class="validate-radio-required shutdowncheckbox"
                                                                name="state_isolation_loto[]" value="Water/Liquid"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                    <div class="d-inline-block"
                                                        style="margin-left: -200px; padding: 0; flex-shrink: 0;">
                                                        <div class="form-group d-flex align-items-center gap-1">
                                                            <textarea class="form-control shutdowncheckbox" name="state_isolation_loto[]" placeholder="Specify others" disabled></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="section-1" class="col-md-8">
                                            <p class="fw-bold fs-5 mt-3">Applicable for Confined Space Entry</p>

                                            <div class="row border rounded p-2 mx-1">
                                                <div class="col-12 col-md-4 mb-3">
                                                    <div class="form-group">
                                                        <label class="form-label">O2%</label>
                                                        <input type="text" name="confined_space_entry[o2_percentage]" class="form-control" placeholder="" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 mb-3">
                                                    <div class="form-group">
                                                        <label class="form-label">System Isolated</label>
                                                        <input type="hidden" name="confined_space_entry[system_isolated]" value="0">
                                                        <input type="checkbox" class="validate-radio-required" name="confined_space_entry[system_isolated]" value="1">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 mb-3">
                                                    <div class="form-group">
                                                        <label class="form-label">Rescue System Available</label>
                                                        <input type="hidden" name="confined_space_entry[rescue_system]" value="0">
                                                        <input type="checkbox" class="validate-radio-required" name="confined_space_entry[rescue_system]" value="1">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row border rounded p-2 mx-1">
                                                <div class="col-12 col-md-4 mb-3">
                                                    <div class="form-group">
                                                        <label class="form-label">Confined Space Attendant</label>
                                                        <input type="hidden" name="confined_space_entry[confined_attendant]" value="0">
                                                        <input type="checkbox" class="validate-radio-required" name="confined_space_entry[confined_attendant]" value="1">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 mb-3">
                                                    <div class="form-group">
                                                        <label class="form-label">Attendant Name</label>
                                                        <input type="text" name="confined_space_entry[attendant_name]" class="form-control" placeholder="Search by Employee Name" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 mb-3">
                                                    <div class="form-group">
                                                        <label class="form-label">Register for entry & exits</label>
                                                        <input type="checkbox" class="validate-radio-required" name="confined_space_entry[register_entry_exits]" disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row border rounded p-2 mx-1 mb-3">
                                                <div class="col-12 col-md-4 mb-3">
                                                    <div class="form-group">
                                                        <label class="form-label">Any Other Gas / PPM</label>
                                                        <input type="text" name="confined_space_entry[other_gas]" class="form-control" placeholder="Loto No" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 mb-3">
                                                    <div class="form-group">
                                                        <label class="form-label">PPM and is therefore safe to enter from</label>
                                                        <input type="text" name="confined_space_entry[ppm_safe_to_enter]" class="form-control" placeholder="" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 mb-3">
                                                    <div class="form-group">
                                                        <label class="form-label">To</label>
                                                        <input type="text" name="confined_space_entry[to]" class="form-control" placeholder="" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>


                                        {{-- Protective Equipment to be own --}}

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

                                        {{-- Name of the Equipement involved in Job --}}

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

                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="description" class="form-label fw-bold">Other If
                                                                any</label>
                                                            <textarea id="description" name="equiment_involved_others" class="form-control shadow-sm" placeholder=""></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Precaution --}}


                                        <p class="fw-bold fs-5 mt-3">Precaution To be Taken</span>
                                        </p>

                                        <div class="row" style="background: #d6f5e0b0;">
                                            <div class="col-12 p-2">

                                                <div class="card p-3  rounded m-2">
                                                    <div id="getprecaution-container" class="row g-3 mt-3">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Equipement Checklist --}}


                                        <p class="fw-bold fs-5 mt-3">Equipment's Check List</span>
                                        </p>
                                        <div class="row" style="background: #d6f5e0b0;">
                                            <div class="col-12 p-2">

                                                <div class="card p-3 rounded m-2">
                                                    <div id="getchecklist-container" class="row g-3 mt-3">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <div class=" p-3  mb-1">
                                                    <p class="fw-bold fs-5 mt-3">
                                                        All Involved Equipment's have been inspected as per the inspection
                                                        checklist prior to start work (Yes/No)
                                                        <span class="text-danger">*</span>
                                                        <input type="checkbox" id="select-all"
                                                            class="validate-radio-required"
                                                            name = "equipment_checklist_inspection"
                                                            {{ $safetypermit->equipment_checklist_inspection == 1 ? 'checked' : '' }}>
                                                    </p>

                                                </div>
                                            </div>
                                        </div>

                                        {{-- Safe work instruction --}}

                                        <p class="fw-bold fs-5 mt-3">Safe Work Instructions</span>
                                        </p>
                                        <div class="row" style="background: #d6f5e0b0;">
                                            <div class="col-12 p-2">
                                                <div class="card p-3  rounded m-2">
                                                    <div id="getinstruction-container" class="row g-3 mt-3">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Safe work procedure --}}

                                        <div class="row mt-2">
                                            <div class="col-12 col-md-6">
                                                <div class="p-3 mb-1">
                                                    <p class="fw-bold fs-5 mt-1">
                                                        Safe Work Procedure discussed in tool box talk before start the work
                                                        (Yes/No)
                                                        <span class="text-danger">*</span>
                                                        <input type="checkbox" id="select-all"
                                                            class="validate-radio-required" name = "toolbox_talk"
                                                            {{ $safetypermit->toolbox_talk == 1 ? 'checked' : '' }}>
                                                    </p>
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
                                                            value="{{ $safetypermit->talk_givenby }}">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        {{-- Mandetory Notes --}}

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


                                        {{-- List of work man involved in job --}}


                                        <p class="fw-bold fs-5 mt-3">List of Workman involved in Job</span>
                                        </p>
                                        <div>
                                            {{-- @foreach () --}}
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Employee Code / Visitor ID</label>
                                                        <select name="employee_code" id="employee_code"
                                                            class="single-select   form-control">
                                                            <option value="">Select Employee ID</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Name of Workman</label>
                                                        <input type="text" name="workman_name" id="workman_name"
                                                            class="form-control" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Designation</label>
                                                        <input type="text" name="workman_desig" id="workman_desig"
                                                            class="form-control" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Department / Company</label>
                                                        <select name="workman_dept" id="workman_dept"
                                                            class="form-control single-select">
                                                            <option value="">Select Department</option>
                                                            <!-- Dynamic department options here -->
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Nature of Job</label>
                                                        <input type="text" name="nature_of_job" id="nature_of_job"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-2 mt-3">
                                                    <x-button-add dataId="" class="add btn btn-primary ms-1"
                                                        href="{{ admin_url('ptw/typeofworkmaster/add') }}">Add</x-button-add>
                                                </div>
                                            </div>
                                            {{-- @endforeach --}}



                                        </div>



                                        <div class="table-responsive mt-3">
                                            <table class="table table-bordered text-center">
                                                <thead class=" text-white" style="background-color:#5b626b">
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

                                                </tbody>
                                            </table>
                                        </div>



                                        <div class="row mt-2">
                                            <div class="col-12 col-md-6">
                                                <div class="p-3 mb-1">
                                                    <p class="fw-bold fs-5 mt-1">
                                                        Are all above employee competent for assigned job & physically fit
                                                        for duty (Yes/No)
                                                        <span class="text-danger">*</span>
                                                        <input type="checkbox" id="select-all" name = "assigned_job"
                                                            class="validate-radio-required"
                                                            {{ $safetypermit->assigned_job == 1 ? 'checked' : '' }}>
                                                    </p>
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
                                                            value="{{ $safetypermit->attendance_toolbox_talk }}">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>


                                        {{-- Notes --}}

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
    </div>

@stop

@push('script')
    <script>
   $(document).ready(function() {
    const displayedEquipments = new Set();
    const container = $('#getprotectivechecklist-container');
    var id = $('#id').val();

    function fetchProtectiveChecklist(workId) {
        $.ajax({
            url: "{{ admin_url('safetypermit/getprotectivechecklist') }}/" + workId,
            type: 'GET',
            dataType: 'json',
            data: {
                id: id
            },
            success: function(data) {
                let checkpointsHtml = '';

                data.forEach(function(item) {
                    if (!displayedEquipments.has(item.protective_equip)) {
                        displayedEquipments.add(item.protective_equip);
                        checkpointsHtml += `
                            <div class="col-12 col-md-4 col-lg-4 d-flex align-items-center gap-2 checkpoint" data-work-id="${workId}" data-id="${item.id}">
                                <input type="checkbox" class="protective-checkbox" name="protective_equip[${workId}][]" value="${item.id}" id="checkpoint-${workId}-${item.id}" ${item.checked ? 'checked' : ''}>
                                <label for="checkpoint-${workId}-${item.id}">${item.protective_equip}</label>
                            </div>`;
                    }
                });

                if (checkpointsHtml) {
                    container.append(checkpointsHtml);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    function removeProtectiveChecklist(workId) {
        container.find(`.checkpoint[data-work-id="${workId}"]`).remove();
        displayedEquipments.forEach(equip => {
            if (!$(`.checkpoint[data-id="${equip}"]`).length) {
                displayedEquipments.delete(equip);
            }
        });
    }

    $('.work-type-checkbox[data-checked="true"]').each(function() {
        const workId = $(this).data('id');
        fetchProtectiveChecklist(workId);
    });

    $('.work-type-checkbox').on('change', function() {
        const workId = $(this).data('id');

        if ($(this).is(':checked')) {
            fetchProtectiveChecklist(workId);
        } else {
            removeProtectiveChecklist(workId);
        }
    });
});



        // for equipment involved
        $(document).ready(function() {
            const displayedEquipments = new Set();
            const container = $('#getequipmentinvolved-container');

            function fetchEquipmentInvolved(workId) {
                $.ajax({
                    url: "{{ admin_url('safetypermit/getequipmentinvolved') }}/" + workId,

                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let checkpointsHtml = '';

                        data.forEach(function(item) {
                            if (!displayedEquipments.has(item.equip_involve)) {
                                displayedEquipments.add(item.equip_involve);

                                checkpointsHtml += `
                                <div class="col-12 col-md-4 col-lg-4 d-flex align-items-center gap-2 checkpoint" data-work-id="${workId}" data-id="${item.id}">
                                    <input type="checkbox" class="" name="equiment_involved[${workId}][]" value="${item.id}" id="checkpoint-${workId}-${item.id}">
                                    <label for="checkpoint-${workId}-${item.id}">${item.equip_involve}</label>
                                </div>`;
                            }
                        });

                        if (checkpointsHtml) {
                            container.append(checkpointsHtml);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    },
                });
            }

            function removeEquipmentInvolved(workId) {
                container.find(`.checkpoint[data-work-id="${workId}"]`).remove();
                displayedEquipments.forEach(equip => {
                    if (!$(`.checkpoint[data-id="${equip}"]`).length) {
                        displayedEquipments.delete(equip);
                    }
                });
            }

            $('.work-type-checkbox[data-checked="true"]').each(function() {
                const workId = $(this).data('id');
                fetchEquipmentInvolved(workId);
            });

            $('.work-type-checkbox').on('change', function() {
                const workId = $(this).data('id');

                if ($(this).is(':checked')) {
                    fetchEquipmentInvolved(workId);
                } else {
                    removeEquipmentInvolved(workId);
                }
            });
        });

        // Precaution

        $(document).ready(function() {
            const displayedEquipments = new Set();
            const container = $('#getprecaution-container');


            function fetchPrecaution(workId) {
                $.ajax({
                    url: "{{ admin_url('safetypermit/getprecaution') }}/" + workId,

                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let checkpointsHtml = '';

                        data.forEach(function(item) {
                            if (!displayedEquipments.has(item.precaution)) {
                                displayedEquipments.add(item.precaution);

                                checkpointsHtml += `
                                <div class="col-12 col-md-12 d-flex align-items-center gap-2 checkpoint" data-work-id="${workId}" data-id="${item.id}">
                                    <input type="checkbox" class="" name="precaution_taken[${workId}][]" value="${item.id}" id="checkpoint-${workId}-${item.id}">
                                    <label for="checkpoint-${workId}-${item.id}">${item.precaution}</label>
                                </div>`;
                            }
                        });

                        if (checkpointsHtml) {
                            container.append(checkpointsHtml);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    },
                });
            }

            function removePrecaution(workId) {
                container.find(`.checkpoint[data-work-id="${workId}"]`).remove();
                displayedEquipments.forEach(equip => {
                    if (!$(`.checkpoint[data-id="${equip}"]`).length) {
                        displayedEquipments.delete(equip);
                    }
                });
            }

            $('.work-type-checkbox[data-checked="true"]').each(function() {
                const workId = $(this).data('id');
                fetchPrecaution(workId);
            });

            $('.work-type-checkbox').on('change', function() {
                const workId = $(this).data('id');

                if ($(this).is(':checked')) {
                    fetchPrecaution(workId);
                } else {
                    removePrecaution(workId);
                }
            });
        });

        // Equipment Checklist


        $(document).ready(function() {
            const displayedEquipments = new Set();
            const container = $('#getchecklist-container');


            function fetchEquimentchecklist(workId) {
                $.ajax({
                    url: "{{ admin_url('safetypermit/getchecklist') }}/" + workId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let checkpointsHtml = '';

                        data.forEach(function(item) {
                            if (!displayedEquipments.has(item.checklist)) {
                                displayedEquipments.add(item.checklist);

                                checkpointsHtml += `
                                <div class="col-12 col-md-12 d-flex align-items-center gap-2 checkpoint" data-work-id="${workId}" data-id="${item.id}">
                                    <input type="checkbox" class="" name="equipment_checklist[${workId}][]" value="${item.id}" id="checkpoint-${workId}-${item.id}">
                                    <label for="checkpoint-${workId}-${item.id}">${item.checklist}</label>
                                </div>`;
                            }
                        });

                        if (checkpointsHtml) {
                            container.append(checkpointsHtml);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    },
                });
            }

            function removeEquimentchecklist(workId) {
                container.find(`.checkpoint[data-work-id="${workId}"]`).remove();
                displayedEquipments.forEach(equip => {
                    if (!$(`.checkpoint[data-id="${equip}"]`).length) {
                        displayedEquipments.delete(equip);
                    }
                });
            }

            $('.work-type-checkbox[data-checked="true"]').each(function() {
                const workId = $(this).data('id');
                fetchEquimentchecklist(workId);
            });

            $('.work-type-checkbox').on('change', function() {
                const workId = $(this).data('id');

                if ($(this).is(':checked')) {
                    fetchEquimentchecklist(workId);
                } else {
                    removeEquimentchecklist(workId);
                }
            });
        });

        // Safe work instruction

        $(document).ready(function() {
            const displayedEquipments = new Set();
            const container = $('#getinstruction-container');


            function fetchSafeworkInstruction(workId) {
                $.ajax({
                    url: "{{ admin_url('safetypermit/getinstruction') }}/" + workId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let checkpointsHtml = '';

                        data.forEach(function(item) {
                            if (!displayedEquipments.has(item.safe_work)) {
                                displayedEquipments.add(item.safe_work);

                                checkpointsHtml += `
                                <div class="col-12 col-md-12 d-flex align-items-center gap-2 checkpoint" data-work-id="${workId}" data-id="${item.id}">
                                    <input type="checkbox" class="" name="safework_instruction[${workId}][]" value="${item.id}" id="checkpoint-${workId}-${item.id}">
                                    <label for="checkpoint-${workId}-${item.id}">${item.safe_work}</label>
                                </div>`;
                            }
                        });

                        if (checkpointsHtml) {
                            container.append(checkpointsHtml);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    },
                });
            }

            function removeSafeworkInstruction(workId) {
                container.find(`.checkpoint[data-work-id="${workId}"]`).remove();
                displayedEquipments.forEach(equip => {
                    if (!$(`.checkpoint[data-id="${equip}"]`).length) {
                        displayedEquipments.delete(equip);
                    }
                });
            }

            $('.work-type-checkbox[data-checked="true"]').each(function() {
                const workId = $(this).data('id');
                fetchSafeworkInstruction(workId);
            });

            $('.work-type-checkbox').on('change', function() {
                const workId = $(this).data('id');

                if ($(this).is(':checked')) {
                    fetchSafeworkInstruction(workId);
                } else {
                    removeSafeworkInstruction(workId);
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            var permitId = $('#id').val();

            $('#employee_code').select2({
                ajax: {
                    url: '{{ admin_url('safetypermit/employeeid') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            permit_id: permitId
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

                parentRow.find('select[name="employee_code"]').val("");
                parentRow.find('select[name="employee_code"]').trigger("change");
                parentRow.find('input[name="workman_name"]').val("");
                parentRow.find('input[name="workman_desig"]').val("");
                parentRow.find('select[name="workman_dept"]').val("");
                parentRow.find('select[name="workman_dept"]').trigger("change");
                parentRow.find('input[name="nature_of_job"]').val("");

                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: "Workman has been added to the table.",
                });
            });


            $(document).on("click", ".remove-entry", function() {
                $(this).closest("tr").remove();
            });

            $('#employeenameshutdown, #employeenameloto').select2({
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



             const lotocheckbox = $('#loto-checkbox');
    const shutdowncheckbox = $('#shutdown-checkbox');
    const sectionemployeenameloto = $('#employeenameloto');
    const employeenameshutdown = $('#employeenameshutdown');


    if (lotocheckbox.is(':checked')) {
        sectionemployeenameloto.prop('disabled', false);
    } else {
        sectionemployeenameloto.prop('disabled', true);
    }

    if (shutdowncheckbox.is(':checked')) {
        employeenameshutdown.prop('disabled', false);
    } else {
        employeenameshutdown.prop('disabled', true);
    }


    lotocheckbox.on('change', function() {
        if ($(this).is(':checked')) {
            sectionemployeenameloto.prop('disabled', false);
        } else {
            sectionemployeenameloto.prop('disabled', true);
        }
    });

    shutdowncheckbox.on('change', function() {
        if ($(this).is(':checked')) {
            employeenameshutdown.prop('disabled', false);
        } else {
            employeenameshutdown.prop('disabled',true);
        }
    });





        $(document).ready(function() {
            const section1Inputs = $('#section-1 input');
            const checkboxWithValue8 = $('.work-type-checkbox[data-id="8"]');

            if (checkboxWithValue8.is(':checked')) {
                section1Inputs.prop('disabled', false);
            } else {
                section1Inputs.prop('disabled', true);
            }

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

            if (shutdowncheckbox.is(':checked')) {
                targetInputs.prop('disabled', false);
    } else {
        targetInputs.prop('disabled', true);
    }


            shutdownCheckbox.on('change', function() {
                if ($(this).is(':checked')) {
                    targetInputs.prop('disabled', false);
                } else {

                    targetInputs.prop('disabled', true);
                }
            });
        });



        });

        flatpickr("#date", {
            // enableTime: true,
            dateFormat: "d-m-Y",
            // time_24hr: true,
            minuteIncrement: 5,
        });

        flatpickr("#time_from", {
            enableTime: true,
            noCalendar: true,
            time_24hr: true,
            minuteIncrement: 5,
            dateFormat: "H:i"
        });

        flatpickr("#time_to", {
            enableTime: true,
            noCalendar: true,
            time_24hr: true,
            minuteIncrement: 5,
            dateFormat: "H:i"
        });
    </script>
@endpush
