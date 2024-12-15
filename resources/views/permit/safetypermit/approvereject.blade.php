@extends('admin.layouts.admin')
@section('title', '')
@section('pageurl', admin_url('safetypermit/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="align-back-btc">
                                <x-button-back href="{{ admin_url('safetypermit/list') }}"></x-button-back>

                            </div>
                        </div>

                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">Safety Permit</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Work Permit No') }}</label>
                                    <div class="view_data">
                                        {{ isset($safetypermit->permit_id) ? $safetypermit->permit_id : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Date') }}</label>
                                    <div class="view_data">
                                        {{ isset($safetypermit->date) ? Displaydateformat($safetypermit->date) : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Time(From)') }}</label>
                                    <div class="view_data">
                                        {{ isset($safetypermit->time_from) ? $safetypermit->time_from : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Time(To)') }}</label>
                                    <div class="view_data">
                                        {{ isset($safetypermit->time_to) ? $safetypermit->time_to : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Unit') }}</label>
                                    <div class="view_data">
                                        {{ getUnitname(isset($safetypermit->unit_id) ? $safetypermit->unit_id : '') }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Exact location of job') }}</label>
                                    <div class="view_data">
                                        {{ isset($safetypermit->exact_location_job) ? $safetypermit->exact_location_job : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Job Location & Area') }}</label>
                                    <div class="view_data">
                                        {{ isset($safetypermit->job_location_area) ? $safetypermit->job_location_area : '' }}
                                    </div>
                                </div>

                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Created By') }}</label>
                                    <div class="view_data">
                                        {{ getUsername(isset($safetypermit->created_by) ? $safetypermit->created_by : '') }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                    <div class="view_data">
                                        {{ displayDateformat($safetypermit->created_at) }}
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">Type of Job</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-6 form-input">
                                    <div class="view_data">
                                        @if (isset($safetypermit->sub_permit_names) && isset($safetypermit->sub_permit_images))
                                            @php
                                                $names = is_array($safetypermit->sub_permit_names)
                                                    ? $safetypermit->sub_permit_names
                                                    : explode(', ', $safetypermit->sub_permit_names);
                                                $images = is_array($safetypermit->sub_permit_images)
                                                    ? $safetypermit->sub_permit_images
                                                    : explode(', ', $safetypermit->sub_permit_images);
                                                $count = max(count($names), count($images));
                                            @endphp
                                            <div class="row">
                                                @for ($i = 0; $i < $count; $i++)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="d-flex align-items-center">
                                                            @if (isset($images[$i]) && $images[$i] != '')
                                                                <img src="{{ asset($images[$i]) }}" alt="Permit Image"
                                                                    class="img-fluid"
                                                                    style="width: 50px; height: 50px; margin-right: 10px;">
                                                            @endif
                                                            @if (isset($names[$i]) && $names[$i] != '')
                                                                <span>{{ $names[$i] }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endfor
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label m-1">Job Description</label>
                                    <div class="view_data">
                                        {{ $safetypermit->job_description }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-4 form-input">
                                    <img src="{{ url('public/assets/images/safetypermit/power-off.png') }}"
                                        class="img-fluid" style="width: 50px; height: 50px;">
                                    <label class="form-label view_label m-1">Shut Down Required (Yes/No)</label>
                                    <span class="view_data">
                                        @if ($safetypermit->shutdown_req == 1)
                                            <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i></b>
                                        @endif
                                    </span>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <img src="{{ url('public/assets/images/safetypermit/profile.png') }}" class="img-fluid"
                                        style="width: 50px; height: 50px;">
                                    <label class="form-label view_label">Taken By (Name & Department)</label>
                                    <span class="view_data">
                                        {{ $safetypermit->shut_down_takenby }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4 form-input">
                                    <img src="{{ url('public/assets/images/safetypermit/process.png') }}" class="img-fluid"
                                        style="width: 50px; height: 50px;">
                                    <label class="form-label view_label m-1">Isolation/LOTO Required (Yes/No)</label>
                                    <span class="view_data">
                                        @if ($safetypermit->loto_req == 1)
                                            <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i></b>
                                        @endif
                                    </span>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <img src="{{ url('public/assets/images/safetypermit/profile.png') }}" class="img-fluid"
                                        style="width: 50px; height: 50px;">
                                    <label class="form-label view_label">Taken By (Name & Department)</label>
                                    <span class="view_data">
                                        {{ $safetypermit->loto_takenby }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-4 form-input">

                                    <label class="form-label view_label m-1">Loto No</label>
                                    <div class="view_data">
                                        {{ $safetypermit->job_description }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">

                                    <label class="form-label view_label">Tag Field properly (Yes/No)</label>
                                    @if ($safetypermit->tagfield == 1)
                                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i></b>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">State of Isolation & LOTO</h4>
                                </div>
                            </div>

                            <div class="row">
                                @foreach (['Air', 'Gas', 'Electrical', 'Water/Liquid'] as $item)
                                    <div class="mb-3 col-md-4 form-input">
                                        <!-- Display image based on the item -->
                                        @switch($item)
                                            @case('Air')
                                                <img src="{{ url('public/assets/images/safetypermit/person.png') }}"
                                                    class="img-fluid" style="width: 50px; height: 50px;">
                                            @break

                                            @case('Gas')
                                                <img src="{{ url('public/assets/images/safetypermit/natural-gas.png') }}"
                                                    class="img-fluid" style="width: 50px; height: 50px;">
                                            @break

                                            @case('Electrical')
                                                <img src="{{ url('public/assets/images/safetypermit/electrician.png') }}"
                                                    class="img-fluid" style="width: 50px; height: 50px;">
                                            @break

                                            @case('Water/Liquid')
                                                <img src="{{ url('public/assets/images/safetypermit/leak.png') }}"
                                                    class="img-fluid" style="width: 50px; height: 50px;">
                                            @break
                                        @endswitch

                                        <label class="form-label view_label m-1">{{ $item }}</label>
                                        <span class="view_data">
                                            @if (in_array($item, $stateIsolationLoto))
                                                <!-- Check if the item is in the array -->
                                                <b><i class="fa-solid fa-check"
                                                        style="color: #267709; width: 15px;"></i></b>
                                            @else
                                                <b><i class="fa-solid fa-xmark"
                                                        style="color: #ff0000; width: 15px;"></i></b>
                                                <!-- Display X if not found -->
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            @if ($stateIsolationLoto)
                                <div class="row">
                                    @foreach ($stateIsolationLoto as $item)
                                        @if (!in_array($item, ['Air', 'Gas', 'Electrical', 'Water/Liquid']))
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label m-1">Others if any please
                                                    specify</label>
                                                <div class="view_data">

                                                    {{ $item }}
                                                </div>
                                            </div>
                                        @break
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="card-header-inner">
                                <h4 class="text-white">Applicable for Confined Space Entry</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label">O2%</label>
                                <span class="view_data">
                                    {{ $confined_space_entry->o2_percentage ?? 'N/A' }}
                                </span>
                            </div>

                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label m-1">System Isolated</label>
                                <span class="view_data">
                                    @if ($confined_space_entry->system_isolated == 1)
                                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i></b>
                                    @else
                                        <span>No</span>
                                    @endif
                                </span>
                            </div>

                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label m-1">Rescue System Available</label>
                                <span class="view_data">
                                    @if ($confined_space_entry->rescue_system == 1)
                                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i></b>
                                    @else
                                        <span>No</span>
                                    @endif
                                </span>
                            </div>

                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label m-1">Confined Space Attendant</label>
                                <span class="view_data">
                                    @if ($confined_space_entry->confined_attendant == 1)
                                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i></b>
                                    @else
                                        <span>No</span>
                                    @endif
                                </span>
                            </div>

                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label">Attendant Name</label>
                                <span class="view_data">
                                    {{ $confined_space_entry->attendant_name ?? 'N/A' }}
                                </span>
                            </div>

                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label m-1">Register for entry & exits</label>
                                <span class="view_data">
                                    @if ($confined_space_entry->register_entry_exits == 'on')
                                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i></b>
                                    @else
                                        <span>No</span>
                                    @endif
                                </span>
                            </div>

                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label">Any Other Gas / PPM</label>
                                <span class="view_data">
                                    {{ $confined_space_entry->other_gas ?? 'N/A' }}
                                </span>
                            </div>

                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label">PPM and is therefore safe to enter
                                    from</label>
                                <span class="view_data">
                                    {{ $confined_space_entry->ppm_safe_to_enter ?? 'N/A' }}
                                </span>
                            </div>

                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label">To</label>
                                <span class="view_data">
                                    {{ $confined_space_entry->to ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="card-header-inner">
                                <h4 class="text-white">Protective Equipment's to be Worn</h4>
                            </div>
                        </div>

                        <div class="row">
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

                            <div class="mb-3 col-md-4 form-input">
                                <div class="view_data">
                                    @foreach ($safetypermit->mapped_protective_equip as $job => $details)
                                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                            @foreach ($details['checkpoint_names'] as $checkpoint_name)
                                                <div
                                                    style="flex: 1 1 calc(33% - 10px); display: flex; align-items: center; gap: 5px;">
                                                    <i class="fa-solid fa-check"
                                                        style="color: #267709; width: 15px;"></i>
                                                    {{ $checkpoint_name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="card-header-inner">
                                <h4 class="text-white">Name of Equipment's involved in Job</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="d-flex flex-wrap justify-content-center align-items-center gap-3">

                                    <div style="width: 80px; height: 80px;"
                                        class="d-flex justify-content-center align-items-center border rounded bg-white shadow-sm">
                                        <img src="{{ url('public/assets/images/safetypermit/flash.png') }}"
                                            class="img-fluid" style="max-width: 70%; max-height: 70%;">
                                    </div>
                                    <div style="width: 80px; height: 80px;"
                                        class="d-flex justify-content-center align-items-center border rounded bg-white shadow-sm">
                                        <img src="{{ url('public/assets/images/safetypermit/gloves.png') }}"
                                            class="img-fluid" style="max-width: 70%; max-height: 70%;">
                                    </div>
                                    <div style="width: 80px; height: 80px;"
                                        class="d-flex justify-content-center align-items-center border rounded bg-white shadow-sm">
                                        <img src="{{ url('public/assets/images/safetypermit/shoes.png') }}"
                                            class="img-fluid" style="max-width: 70%; max-height: 70%;">
                                    </div>
                                    <div style="width: 80px; height: 80px;"
                                        class="d-flex justify-content-center align-items-center border rounded bg-white shadow-sm">
                                        <img src="{{ url('public/assets/images/safetypermit/gloves (1).png') }}"
                                            class="img-fluid" style="max-width: 70%; max-height: 70%;">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 col-md-4 form-input">
                                <div class="view_data">
                                    @foreach ($safetypermit->mapped_equiment_involved as $job => $details)
                                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                            @foreach ($details['checkpoint_names'] as $checkpoint_name)
                                                <div
                                                    style="flex: 1 1 calc(33% - 10px); display: flex; align-items: center; gap: 5px;">
                                                    <i class="fa-solid fa-check"
                                                        style="color: #267709; width: 15px;"></i>
                                                    {{ $checkpoint_name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>


                            <div class="row">
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label m-1">Other If any</label>
                                    <div class="view_data">
                                        {{ $safetypermit->equiment_involved_others }}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="card-header-inner">
                                <h4 class="text-white">Precaution To be Taken</h4>
                            </div>
                        </div>

                        <div class="mb-3 col-md-12 form-input">
                            <div class="view_data">
                                @foreach ($safetypermit->mapped_precaution_taken as $job => $details)
                                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                        @foreach ($details['checkpoint_names'] as $checkpoint_name)
                                            <div
                                                style="flex: 1 1 calc(33% - 10px); display: flex; align-items: center; gap: 5px;">
                                                <i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i>
                                                {{ $checkpoint_name }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="card-header-inner">
                                <h4 class="text-white">Equipment's Check List</h4>
                            </div>
                        </div>

                        <div class="row">

                            <div class="mb-3 col-md-12 form-input">
                                <div class="view_data">
                                    @foreach ($safetypermit->mapped_equipment_checklist as $job => $details)
                                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                            @foreach ($details['checkpoint_names'] as $checkpoint_name)
                                                <div
                                                    style="flex: 1 1 calc(33% - 10px); display: flex; align-items: center; gap: 5px;">
                                                    <i class="fa-solid fa-check"
                                                        style="color: #267709; width: 15px;"></i>
                                                    {{ $checkpoint_name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-3 col-md-12 form-input">
                                <label class="form-label view_label m-1">All Involved Equipment's have been
                                    inspected as per the inspection checklist prior to start work (Yes/No)</label>

                                @if ($safetypermit->equipment_checklist_inspection == 1)
                                    <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i></b>
                                @endif

                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="card-header-inner">
                                <h4 class="text-white">Safe Work Instructions</h4>
                            </div>
                        </div>

                        <div class="row">

                            <div class="mb-3 col-md-12 form-input">
                                <div class="view_data">
                                    @foreach ($safetypermit->mapped_safework_instruction as $job => $details)
                                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                            @foreach ($details['checkpoint_names'] as $checkpoint_name)
                                                <div
                                                    style="flex: 1 1 calc(33% - 10px); display: flex; align-items: center; gap: 5px;">
                                                    <i class="fa-solid fa-check"
                                                        style="color: #267709; width: 15px;"></i>
                                                    {{ $checkpoint_name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-3 col-md-12 form-input">
                                <label class="form-label view_label m-1"> Safe Work Procedure discussed in tool box
                                    talk before start the work
                                    (Yes/No)</label>

                                @if ($safetypermit->toolbox_talk == 1)
                                    <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i></b>
                                @endif

                            </div>
                            <div class="mb-3 col-md-4 form-input">

                                <label class="form-label view_label">Tool box Talk Given By (Name) </label>
                                <span class="view_data">
                                    {{ $safetypermit->talk_givenby }}
                                </span>
                            </div>

                        </div>
                    </div>



                    <div class="card-body">
                        <div class="row">
                            <div class="card-header-inner">
                                <h4 class="text-white">List of Workman involved in Job</h4>
                            </div>
                        </div>

                        <div class="row">

                            <div class="table-responsive mt-3">
                                <table class="table table-bordered text-center">
                                    <thead class=" text-white" style="background-color:#5b626b">
                                        <tr>
                                            <th>Employee Code / Visitor ID</th>
                                            <th>Name of Workman</th>
                                            <th>Designation</th>
                                            <th>Department / Company</th>
                                            <th>Nature of Job</th>
                                        </tr>
                                    </thead>
                                    <tbody id="workman-list-entries">
                                        @foreach ($workmaninvolved as $workmaninvolved)
                                            <tr>
                                                <td>{{ $workmaninvolved->employee_id }}</td>
                                                <td>{{ $workmaninvolved->workman_name }}</td>
                                                <td>{{ $workmaninvolved->workman_desig }}</td>
                                                <td>{{ $workmaninvolved->department_name }}</td>
                                                <td>{{ $workmaninvolved->nature_of_job }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>
                            </div>

                            <div class="mb-3 col-md-12 form-input">
                                <label class="form-label view_label m-1"> Are all above employee competent for
                                    assigned job & physically fit for duty (Yes/No)</label>

                                @if ($safetypermit->assigned_job == 1)
                                    <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i></b>
                                @endif

                            </div>
                            <div class="mb-3 col-md-4 form-input">

                                <label class="form-label view_label">Total number of attendance in Tool box
                                    Talk</label>
                                <span class="view_data">
                                    {{ $safetypermit->attendance_toolbox_talk }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @if ($safetypermit['permit_status'] == 1)
                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">EHS Verification</h4>
                                </div>
                            </div>
                            <div class="basic-form">
                                <form method="POST" id="ehs_verification"
                                    action="{{ admin_url('safetypermit/ehsverification/submit') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="">
                                        <input type="hidden" id= "permit_id" name="permit_id"
                                            value="{{ $safetypermit->id }}">
                                        <div class="mb-3 row">
                                            <div class="col-md-4 mb-3">
                                                <label for="approver_name" class="form-label">Approver Name</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name= "approver_name" id="approver_name" readonly
                                                    value="{{ Auth::user()->name }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="date" class="form-label">Date</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="date" name="date" readonly
                                                    value="{{ date('d-m-Y H:i:s') }}">
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <div class="mb-1">
                                                    <label for="remarks" class="form-label">Remarks</label>
                                                    <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="ehs_verification_remarks"
                                                        rows="3"></textarea>
                                                    <div class="text-danger" id="remarks_error"></div>
                                                    @error('remarks')
                                                        <span id="remark_error"
                                                            class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex float-end gap-2 mx-auto">
                                        <button type="submit" name="verify" value="verify"
                                            class="btn btn-success w-100">Verify</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">EHS Verification</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Approver Name') }}</label>
                                    <div class="view_data">
                                        {{ isset($getEhSverification->approve_reject_by) ? $getEhSverification->approve_reject_by : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Date') }}</label>
                                    <div class="view_data">
                                        {{ isset($getEhSverification->date) ? Displaydateformat($getEhSverification->date) : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Remarks') }}</label>
                                    <div class="view_data">
                                        {{ isset($getEhSverification->remarks) ? $getEhSverification->remarks : '' }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif


                    @if ($safetypermit['permit_status'] == 2)
                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">EHS Approval</h4>
                                </div>
                            </div>
                            <div class="basic-form">
                                <form method="POST" id="ehs_approval"
                                    action="{{ admin_url('safetypermit/ehsapproval/submit') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="">
                                        <input type="hidden" id= "permit_id" name="permit_id"
                                            value="{{ $safetypermit->id }}">
                                        <div class="mb-3 row">
                                            <div class="col-md-4 mb-3">
                                                <label for="approver_name" class="form-label">Approver Name</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name= "approver_name" id="approver_name" readonly
                                                    value="{{ Auth::user()->name }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="date" class="form-label">Date</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="date" name="date" readonly
                                                    value="{{ date('d-m-Y H:i:s') }}">
                                            </div>
                                            <div class="col-md-4 mb-3 reassign-div" style = "display:none;">
                                                <label for="reassign_to" class="form-label">Reassign To</label>
                                                <select name="reassign_to" id="reassign_to"
                                                    class="form-control reassign_to">
                                                    <option value="">Select Person</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <div class="mb-1">
                                                    <label for="remarks" class="form-label">Remarks</label>
                                                    <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="ehs_approval_remarks"
                                                        rows="3"></textarea>
                                                    <div class="text-danger" id="remarks_error"></div>
                                                    @error('remarks')
                                                        <span id="remark_error"
                                                            class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    @if ($safetypermit['permit_status'] == 3)
                                        <div class="d-flex float-end gap-2 mx-auto">
                                            <button type="submit" name="resume" value="resume"
                                                class="btn btn-info w-100">Resume</button>
                                        </div>
                                    @else
                                        <div class="d-flex float-end gap-2 mx-auto">
                                            <button type="submit" name="hold" value="hold"
                                                class="btn btn-info w-100">Hold</button>
                                            <button type="submit" name="decline" value="decline"
                                                class="btn btn-danger w-100">Decline</button>
                                            <button type="submit" name="reassign" value="reassign"
                                                class="btn btn-secondary w-100 reassign-btn">Reassign</button>
                                            <button type="submit" name="forward" value="forward"
                                                class="btn btn-success w-100">Forward</button>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">EHS Approval</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Approver Name') }}</label>
                                    <div class="view_data">
                                        {{ isset($getEhsapproval->approve_reject_by) ? $getEhsapproval->approve_reject_by : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Date') }}</label>
                                    <div class="view_data">
                                        {{ isset($getEhsapproval->date) ? Displaydateformat($getEhsapproval->date) : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Remarks') }}</label>
                                    <div class="view_data">
                                        {{ isset($getEhsapproval->remarks) ? $getEhsapproval->remarks : '' }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif


                    @if ($safetypermit['permit_status'] == 6)
                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">Plant Head Approval</h4>
                                </div>
                            </div>
                            <div class="basic-form">
                                <form method="POST" id="planthead_approval"
                                    action="{{ admin_url('safetypermit/plantheadapproval/submit') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="">
                                        <input type="hidden" id= "permit_id" name="permit_id"
                                            value="{{ $safetypermit->id }}">
                                        <div class="mb-3 row">
                                            <div class="col-md-4 mb-3">
                                                <label for="approver_name" class="form-label">Approver Name</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name= "approver_name" id="approver_name" readonly
                                                    value="{{ Auth::user()->name }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="date" class="form-label">Date</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="date" name="date" readonly
                                                    value="{{ date('d-m-Y H:i:s') }}">
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <div class="mb-1">
                                                    <label for="remarks" class="form-label">Remarks</label>
                                                    <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks"
                                                        name="planthead_approval_remarks" rows="3"></textarea>
                                                    <div class="text-danger" id="remarks_error"></div>
                                                    @error('remarks')
                                                        <span id="remark_error"
                                                            class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex float-end gap-2 mx-auto">
                                        <button type="submit" name="approve" value="approve"
                                            class="btn btn-info w-100">Approve</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">EHS Approval</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Approver Name') }}</label>
                                    <div class="view_data">
                                        {{ isset($getEhsapproval->approve_reject_by) ? $getEhsapproval->approve_reject_by : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Date') }}</label>
                                    <div class="view_data">
                                        {{ isset($getEhsapproval->date) ? Displaydateformat($getEhsapproval->date) : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Remarks') }}</label>
                                    <div class="view_data">
                                        {{ isset($getEhsapproval->remarks) ? $getEhsapproval->remarks : '' }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
@stop

@push('script')
    <script>
        $(document).ready(function() {
            $('#ehs_verification').validate({
                rules: {
                    ehs_verification_remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 255,
                        regex: /^[a-zA-Z0-9\s]+$/
                    },
                },
                messages: {

                    ehs_verification_remarks: {
                        required: " Remarks cannot be empty.",
                        minlength: "Remarks  must contain between 3 and 255 characters.",
                        maxlength: "Remarks must contain between 3 and 255 characters.",
                        regex: "Remarks must contain only letters and numbers."
                    },
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    var errorDiv = element.siblings('div.text-danger');
                    errorDiv.html(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    $('#submit').prop('disabled', true);
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

            $.validator.addMethod("regex", function(value, element, regexp) {
                return this.optional(element) || regexp.test(value);
            }, "Please check your input.");


            $('.reassign-btn').on('click', function() {

                $('.reassign-div').show();
            });


            $('#ehs_approval').validate({
                rules: {
                    ehs_approval_remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 255,
                        regex: /^[a-zA-Z0-9\s]+$/
                    },
                },
                messages: {

                    ehs_approval_remarks: {
                        required: " Remarks cannot be empty.",
                        minlength: "Remarks  must contain between 3 and 255 characters.",
                        maxlength: "Remarks must contain between 3 and 255 characters.",
                        regex: "Remarks must contain only letters and numbers."
                    },
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    var errorDiv = element.siblings('div.text-danger');
                    errorDiv.html(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    $('#submit').prop('disabled', true);
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
            $('#reassign_to').select2({
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
                selectionCssClass: 'form-control',
                width: '100%'
            });


            $('#planthead_approval').validate({
                rules: {
                    planthead_approval_remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 255,
                        regex: /^[a-zA-Z0-9\s]+$/
                    },
                },
                messages: {

                    planthead_approval_remarks: {
                        required: " Remarks cannot be empty.",
                        minlength: "Remarks  must contain between 3 and 255 characters.",
                        maxlength: "Remarks must contain between 3 and 255 characters.",
                        regex: "Remarks must contain only letters and numbers."
                    },
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    var errorDiv = element.siblings('div.text-danger');
                    errorDiv.html(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    $('#submit').prop('disabled', true);
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
