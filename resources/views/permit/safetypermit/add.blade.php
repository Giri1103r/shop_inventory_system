@extends('admin.layouts.admin')
@section('title', 'Safety Permit Add')
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
                                        <p class="fw-bold fs-5 mt-3">Type of Job: Please Tick Mark (<i
                                                class="fas fa-check text-primary"></i>)
                                            <span class="text-danger">*</span>
                                        </p>
                                        <div class="row" style="background: #d6f5e0b0;">
                                            <div class="col-12">
                                                <div class="card p-3 rounded m-3">
                                                    <div class="row g-3">
                                                        @foreach ($typeofwork as $work)
                                                            <div class="col-12 col-md-4 d-flex align-items-center gap-2">
                                                                <input type="hidden" name="protective_equip" id="protective_equip" value="{{ $work->id }}">
                                                                <input type="checkbox" class="work-type-checkbox" data-id="{{ $work->id }}">
                                                                <a href="{{ asset($work->file_path) }}" target="_blank">
                                                                    <img src="{{ asset($work->file_path) }}" alt="Image" class="img-fluid" style="max-width: 50px; object-fit: cover;">
                                                                </a>
                                                                <span>{{ $work->work_name }}</span>
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
                                                    <img src="{{ url('public/assets/images/safetypermit/power-off.png') }}"
                                                        class="img-fluid" style="width: 50px; height: 50px;">
                                                    <label class="form-label mb-0">Shut Down Required (Yes/No)</label>
                                                    <input type="checkbox" class="validate-radio-required">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <img src="{{ url('public/assets/images/safetypermit/profile.png') }}"
                                                        class="img-fluid" style="width: 50px; height: 50px;">
                                                    <label class="form-label mb-0 ">Taken By (Name & Department)</label>
                                                    <input type="text" name="description" class="form-control"
                                                        placeholder="Search by Employee Name" disabled>
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
                                                    <input type="checkbox" class="validate-radio-required">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <img src="{{ url('public/assets/images/safetypermit/profile.png') }}"
                                                        class="img-fluid" style="width: 50px; height: 50px;">
                                                    <label class="form-label mb-0 ">Taken By (Name & Department)</label>
                                                    <input type="text" name="description" class="form-control"
                                                        placeholder="Search by Employee Name" disabled>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row border p-3 mx-1">
                                            <div class="col-12 col-md-4 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <label class="form-label mb-0">Loto No</label>
                                                    <input type="text" name="description" class="form-control"
                                                        placeholder="Loto No" disabled>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-8 mb-3">
                                                <div class="form-group d-flex align-items-center gap-3">
                                                    <label class="form-label mb-0 ">Tag Field properly (Yes/No)</label>
                                                    <input type="checkbox" class="validate-radio-required">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row col-md-12 d-flex mt-3">
                                            <!-- Left Side: Scrollable on X-Axis -->
                                            <div class="col-md-4">
                                                <p class="fw-bold fs-5 mt-3">State of Isolation & Loto</p>
                                                <div class="scroll-container border p-3"
                                                    style="overflow-x: auto; white-space: nowrap; width: 100%;">
                                                    <!-- First Row -->
                                                    <div class="row mb-3"
                                                        style="display: flex; flex-wrap: nowrap; justify-content: flex-start;  align-items: center;">
                                                        <div class="d-inline-block"
                                                            style="margin: 0; padding: 0; flex-shrink: 0;">
                                                            <div class="form-group d-flex align-items-center gap-1">
                                                                <img src="{{ url('public/assets/images/safetypermit/person.png') }}"
                                                                    class="img-fluid" style="width: 50px; height: 50px;">
                                                                <label class="form-label mb-0">Air</label>
                                                                <input type="checkbox" class="validate-radio-required">
                                                            </div>
                                                        </div>
                                                        <div class="d-inline-block"
                                                            style="margin-left: -200px; padding: 0; flex-shrink: 0;">
                                                            <div class="form-group d-flex align-items-center gap-1">
                                                                <img src="{{ url('public/assets/images/safetypermit/natural-gas.png') }}"
                                                                    class="img-fluid" style="width: 50px; height: 50px;">
                                                                <label class="form-label mb-0">Gas</label>
                                                                <input type="checkbox" class="validate-radio-required">
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
                                                        style="display: flex; flex-wrap: nowrap; justify-content: flex-start;  align-items: center;">
                                                        <div class="d-inline-block"
                                                            style="margin: 0; padding: 0; flex-shrink: 0;">
                                                            <div class="form-group d-flex align-items-center gap-1">
                                                                <img src="{{ url('public/assets/images/safetypermit/electrician.png') }}"
                                                                    class="img-fluid" style="width: 50px; height: 50px;">
                                                                <label class="form-label mb-0">Electrical</label>
                                                                <input type="checkbox" class="validate-radio-required">
                                                            </div>
                                                        </div>
                                                        <div class="d-inline-block"
                                                            style="margin-left: -200px; padding: 0; flex-shrink: 0;">
                                                            <div class="form-group d-flex align-items-center gap-1">
                                                                <img src="{{ url('public/assets/images/safetypermit/leak.png') }}"
                                                                    class="img-fluid" style="width: 50px; height: 50px;">
                                                                <label class="form-label mb-0">Water/Liquid</label>
                                                                <input type="checkbox" class="validate-radio-required">
                                                            </div>
                                                        </div>
                                                        <div class="d-inline-block"
                                                            style="margin-left: -200px; padding: 0; flex-shrink: 0;">
                                                            <div class="form-group d-flex align-items-center gap-1">
                                                                <textarea class="form-control" placeholder="Search by Employee Name" disabled></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>

                                            <!-- Right Side: Expanded Content -->
                                            <div class="col-md-8">
                                                <p class="fw-bold fs-5 mt-3">Applicable for Confined Space Entry</p>

                                                <div class="row border rounded p-2 mx-1">
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label">O2%</label>
                                                            <input type="text" name="description" class="form-control"
                                                                placeholder="" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label">System Isolated</label>
                                                            <input type="checkbox" class="validate-radio-required">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label">Rescue System Available</label>
                                                            <input type="checkbox" class="validate-radio-required">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row border rounded p-2 mx-1">
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label">Confined Space Attendant</label>
                                                            <input type="checkbox" class="validate-radio-required">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label">Attendant Name</label>
                                                            <input type="text" name="description" class="form-control"
                                                                placeholder="Search by Employee Name" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label">Register for entry & exits</label>
                                                            <input type="checkbox" class="validate-radio-required">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row border rounded p-2 mx-1 mb-3">
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label">Any Other Gas / PPM</label>
                                                            <input type="text" name="description" class="form-control"
                                                                placeholder="Loto No" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label">PPM and is therefore safe to enter
                                                                from</label>
                                                            <input type="text" name="description" class="form-control"
                                                                placeholder="" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label">To</label>
                                                            <input type="text" name="description" class="form-control"
                                                                placeholder="" disabled>
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

                                                    <div class="row g-3">
                                                        {{-- @foreach ($getprotectiveequipment as $getprotectiveequipment)
                                                            <div
                                                                class="col-12 col-md-6 col-lg-4 d-flex align-items-center gap-2">
                                                                <input type="checkbox" id="select-all"
                                                                    class="validate-radio-required">
                                                                <label>{{ $getprotectiveequipment->protective_equip }}</label>
                                                            </div>
                                                        @endforeach --}}

                                                        <div id="checkpoints-container" class="row g-3 mt-3">
                                                            <!-- Checkpoints will be loaded here -->
                                                        </div>
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
                                                            <div class="row row-cols-2 g-3">
                                                                @foreach ($getequipmentinvolved as $getequipmentinvolved)
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <input type="checkbox" id="select-all"
                                                                            class="form-check-input">
                                                                        <label for="select-all"
                                                                            class="form-check-label">{{ $getequipmentinvolved->equip_involve }}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="description" class="form-label fw-bold">Other If
                                                                any</label>
                                                            <textarea id="description" name="description" class="form-control shadow-sm"
                                                                style="background-color: #f9f9f9; border: 1px solid #ccc;" placeholder="Describe here..." disabled></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <p class="fw-bold fs-5 mt-3">Precaution To be Taken</span>
                                        </p>

                                        <div class="row" style="background: #d6f5e0b0;">
                                            <div class="col-12 p-2">

                                                <div class="card p-3  rounded m-2">
                                                    <div class="row g-3">
                                                        @foreach ($getprecaution as $getprecaution)
                                                            <div class="col-12 col-md-12 d-flex align-items-center gap-2">
                                                                <input type="checkbox" id="select-all"
                                                                    class="validate-radio-required">

                                                                <label>{{ $getprecaution->precaution }}</label>
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
                                                    <div class="row g-3">
                                                        @foreach ($getchecklist as $getchecklist)
                                                            <div class="col-12 col-md-12 d-flex align-items-center gap-2">
                                                                <input type="checkbox" id="select-all"
                                                                    class="validate-radio-required">

                                                                <label>{{ $getchecklist->checklist }}</label>
                                                            </div>
                                                        @endforeach
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
                                                            class="validate-radio-required">
                                                    </p>

                                                </div>
                                            </div>
                                        </div>
                                        <p class="fw-bold fs-5 mt-3">Safe Work Instructions</span>
                                        </p>
                                        <div class="row" style="background: #d6f5e0b0;">
                                            <div class="col-12 p-2">
                                                <div class="card p-3  rounded m-2">
                                                    <div class="row g-3">
                                                        @foreach ($getinstruction as $getinstruction)
                                                            <div class="col-12 col-md-12 d-flex align-items-center gap-2">
                                                                <input type="checkbox" id="select-all"
                                                                    class="validate-radio-required">

                                                                <label>{{ $getinstruction->safe_work }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-12 col-md-6">
                                                <div class="p-3 mb-1">
                                                    <p class="fw-bold fs-5 mt-1">
                                                        Safe Work Procedure discussed in tool box talk before start the work
                                                        (Yes/No)
                                                        <span class="text-danger">*</span>
                                                        <input type="checkbox" id="select-all"
                                                            class="validate-radio-required">
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
                                                        <input type="text" name="description" id="description"
                                                            class="form-control" style="flex-grow: 1; max-width: 300px;">
                                                    </div>
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
                                            <div class="col-md-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Employee Code / Visitor ID</label>
                                                    <input type="text" name="work_name" id = "work_name"
                                                        class="form-control" placeholder="Name">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Name of Workman</label>
                                                    <input type="text" name="work_name" id = "work_name"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Designation</label>
                                                    <input type="text" name="work_name" id = "work_name"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Department / Company</label>
                                                    <input type="text" name="work_name" id = "work_name"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Nature of Job</label>
                                                    <input type="text" name="work_name" id = "work_name"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-2 mt-3">
                                                <x-button-add dataId="" class="add btn btn-primary ms-1"
                                                    href="{{ admin_url('ptw/typeofworkmaster/add') }}">Add</x-button-add>
                                            </div>

                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="datatable-list"
                                                    class="table primary-table-bordered table-bordered table-striped display responsive nowrap w-100 mt-2 datatable-list">
                                                    <thead class="thead-primary">
                                                        <tr>
                                                            <th>Employee Code / Visitor ID</th>
                                                            <th>Name of Workman</th>
                                                            <th>Designation</th>
                                                            <th>Department / Company</th>
                                                            <th>Nature of Job</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-12 col-md-6">
                                                <div class="p-3 mb-1">
                                                    <p class="fw-bold fs-5 mt-1">
                                                        Are all above employee competent for assigned job & physically fit
                                                        for duty (Yes/No)
                                                        <span class="text-danger">*</span>
                                                        <input type="checkbox" id="select-all"
                                                            class="validate-radio-required">
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
                                                        <input type="text" name="description" id="description"
                                                            class="form-control" style="flex-grow: 1; max-width: 300px;">
                                                    </div>
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
                    url: "{{ admin_url('uploadlog/list/' . request()->logid) }}",
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
            $('.work-type-checkbox').on('change', function() {
                const workId = $(this).data('id');
                const container = $('#checkpoints-container');

                if ($(this).is(':checked')) {
                    $.ajax({
                        url: `/get-checkpoints/${workId}`,
                        url: "{{ admin_url('safetypermit/getprotectivechecklist/.${workId}') }}",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            let checkpointsHtml = '';
                            data.forEach(function(item) {
                                checkpointsHtml += `
                            <div class="col-12 col-md-6 col-lg-4 d-flex align-items-center gap-2" data-work-id="${workId}">
                                <input type="checkbox" class="validate-radio-required">
                                <label>${item.protective_equip}</label>
                            </div>`;
                            });
                            container.append(checkpointsHtml);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                } else {
                    // Remove checkpoints if unchecked
                    container.find(`[data-work-id="${workId}"]`).remove();
                }
            });
        });
    </script>
@endpush
