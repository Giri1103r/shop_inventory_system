@extends('admin.layouts.admin')
@section('title', 'Safety Permit')
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
                                        {{ isset($safetypermit->shut_down_takenby) ? $safetypermit->shut_down_takenby : '' }}
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
                                        {{ isset($safetypermit->loto_takenby) ? $safetypermit->loto_takenby : '' }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-4 form-input">

                                    <label class="form-label view_label m-1">Loto No</label>
                                    <div class="view_data">
                                        {{ $safetypermit->loto_no }}
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
                                            @if (isset($stateIsolationLoto) && in_array($item, $stateIsolationLoto))
                                                <b><i class="fa-solid fa-check"
                                                        style="color: #267709; width: 15px;"></i></b>
                                            @else
                                                <b><i class="fa-solid fa-xmark"
                                                        style="color: #ff0000; width: 15px;"></i></b>
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

                                                    {{ isset($item) ? $item : '' }}
                                                </div>
                                            </div>
                                        @break
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <div class="row">
                            <div class="mb-3 col-md-4 form-input">
                                <img src="{{ url('public/assets/images/safetypermit/fire.png') }}" class="img-fluid"
                                    style="width: 50px; height: 50px;">
                                <label class="form-label view_label m-1">Isolation fire panel</label>
                                <span class="view_data">
                                    @if ($safetypermit->isolationpanel_checkbox == 1)
                                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i></b>
                                    @else
                                        <span>No</span>
                                    @endif
                                </span>
                            </div>
                            <div class="mb-3 col-md-4 form-input">

                                <label class="form-label view_label m-1">Isolation fire panel Description:</label>
                                <span class="view_data">
                                    {{ $safetypermit->isolationpanel_description }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="card-header-inner">
                                <h4 class="text-white">Applicable for Confined Space Entry</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label">O2% :</label>
                                <span class="view_data">
                                    {{ $confined_space_entry->o2_percentage ?? 'N/A' }}
                                </span>
                            </div>

                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label m-1">System Isolated :</label>
                                <span class="view_data">
                                    @if (isset($confined_space_entry) && $confined_space_entry->system_isolated == 1)
                                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i></b>
                                    @else
                                        <span>No</span>
                                    @endif
                                </span>
                            </div>

                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label m-1">Rescue System Available :</label>
                                <span class="view_data">
                                    @if (isset($confined_space_entry) && $confined_space_entry->rescue_system == 1)
                                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i></b>
                                    @else
                                        <span>No</span>
                                    @endif
                                </span>
                            </div>

                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label m-1">Confined Space Attendant :</label>
                                <span class="view_data">
                                    @if (isset($confined_space_entry) &&
                                            isset($confined_space_entry->confined_attendant) &&
                                            $confined_space_entry->confined_attendant == 1)
                                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i></b>
                                    @else
                                        <span>No</span>
                                    @endif
                                </span>
                            </div>

                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label">Attendant Name :</label>
                                <span class="view_data">
                                    {{ $confined_space_entry->attendant_name ?? 'N/A' }}
                                </span>
                            </div>

                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label m-1">Register for entry & exits :</label>
                                <span class="view_data">
                                    @if (isset($confined_space_entry) &&
                                            isset($confined_space_entry->register_entry_exits) &&
                                            $confined_space_entry->register_entry_exits == 'on')
                                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i></b>
                                    @else
                                        <span>No</span>
                                    @endif

                                </span>
                            </div>

                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label">Any Other Gas / PPM :</label>
                                <span class="view_data">
                                    {{ $confined_space_entry->other_gas ?? 'N/A' }}
                                </span>
                            </div>

                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label">PPM and is therefore safe to enter
                                    from :</label>
                                <span class="view_data">
                                    {{ $confined_space_entry->ppm_safe_to_enter ?? 'N/A' }}
                                </span>
                            </div>

                            <div class="mb-3 col-md-4 form-input">
                                <label class="form-label view_label">To :</label>
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
                                <h4 class="text-white">Mandatory Notes for PPEs</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-12 p-2">

                                <div class="row g-3">
                                    <div class="col-12">

                                        <ol class="mb-0" style="line-height: 1.8;">
                                            <li>PPEs must be of national/international standard.
                                            </li>
                                            <li>Damaged/defective PPEs shall not be used.</li>
                                            <li>Non-standard PPEs shall not be used.</li>
                                            <li>PPEs must be inspected before use.</li>
                                        </ol>
                                    </div>
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
                                    <label class="form-label view_label m-1">Other If any :</label>
                                    <div class="view_data">
                                        {{ isset($safetypermit->equiment_involved_others) ? $safetypermit->equiment_involved_others : 'N/A' }}
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
                                @if (empty($safetypermit->mapped_precaution_taken) ||
                                        !collect($safetypermit->mapped_precaution_taken)->contains(function ($details) {
                                            return !empty($details['checkpoint_names']);
                                        }))
                                    <p class="text-center">No data is available</p>
                                @else
                                    @foreach ($safetypermit->mapped_precaution_taken as $job => $details)
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
                                @endif
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
                                    @if (empty($safetypermit->mapped_equipment_checklist) ||
                                            !collect($safetypermit->mapped_equipment_checklist)->contains(function ($details) {
                                                return !empty($details['checkpoint_names']);
                                            }))
                                        <p class="text-center">No data is available</p>
                                    @else
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
                                    @endif
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
                                    @if (empty($safetypermit->mapped_safework_instruction) ||
                                            !collect($safetypermit->mapped_safework_instruction)->contains(function ($details) {
                                                return !empty($details['checkpoint_names']);
                                            }))
                                        <p class="text-center">No data is available</p>
                                    @else
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
                                    @endif
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
                                <h4 class="text-white">Mandatory Notes for Equipments</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-12 p-2">

                                <div class="row g-3">
                                    <div class="col-12">

                                        <ol class="mb-0" style="line-height: 1.8;">
                                            <li>Equipemnt must be of national/international
                                                standard.
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


                    <div class="card-body">
                        <div class="row">
                            <div class="card-header-inner">
                                <h4 class="text-white">List of Workman involved in Job</h4>
                            </div>
                        </div>

                        <div class="row">

                            <div class="table-responsive mt-3">
                                <table class="table table-bordered text-center">
                                    <thead class="text-white" style="background-color:#5b626b">
                                        <tr>
                                            <th>Employee Code / Visitor ID</th>
                                            <th>Name of Workman</th>
                                            <th>Designation</th>
                                            <th>Department / Company</th>
                                            <th>Nature of Job</th>
                                        </tr>
                                    </thead>
                                    <tbody id="workman-list-entries">
                                        @if (empty($workmaninvolved) ||
                                                $workmaninvolved->every(function ($item) {
                                                    return is_null($item->emp_id) &&
                                                        is_null($item->workman_name) &&
                                                        is_null($item->workman_desig) &&
                                                        is_null($item->department_name) &&
                                                        is_null($item->nature_of_job);
                                                }))
                                            <tr>
                                                <td colspan="5" class="text-center">No data is available</td>
                                            </tr>
                                        @else
                                            @foreach ($workmaninvolved as $workman)
                                                <tr>
                                                    <td>{{ $workman->emp_id }}</td>
                                                    <td>{{ $workman->workman_name }}</td>
                                                    <td>{{ $workman->workman_desig }}</td>
                                                    <td>{{ $workman->department_name }}</td>
                                                    <td>{{ $workman->nature_of_job }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>


                            <div class="mb-3 col-md-12 form-input">
                                <label class="form-label view_label m-1">Are all above employees competent for the
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

                    <div class="card-body">
                        <div class="row">
                            <div class="card-header-inner">
                                <h4 class="text-white">Note</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-12 p-2">

                                <div class="row g-3">
                                    <div class="col-12">

                                        <ol class="mb-0" style="line-height: 1.8;">
                                            <li>Work Permit is mandatory for non routine work, third party working agency & high risk Job.
                                            </li>
                                            <li>Work Permit is valid for 8 hours / Renewal may be extended as per unit head approval.</li>
                                            <li>Work Permit will be canceled in case of emergency i.e Fire, weather condition, disaster etc.</li>
                                            <li>Work permit is not valid without signature of Requestor, Verifier & Approver.</li>
                                            <li>Safe Work procedure & method of statement must be discussed in the tool box talk.</li>
                                            <li>Permit to be signed by (Requestor, Verifier & Approver) people not less than Site Engineer / Floor Manager.</li>
                                            <li>Permit Safety compliance shall be discussed to all involved person in local language.</li>

                                        </ol>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    @if (isset($getEhSverification) && $safetypermit['permit_status'] >= 2)
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
                                    <label class="form-label view_label">{{ __('Additional suggestion') }}</label>
                                    <div class="view_data">
                                        {{ isset($getEhSverification->remarks) ? $getEhSverification->remarks : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-6 form-input">
                                    <label class="form-label ">{{ __('Signature') }}</label>
                                    <div>
                                        <div>

                                            @foreach (explode(',', $getEhSverification->file_paths) as $file_path)
                                                <a href="{{ asset($file_path) }}" target="_blank">
                                                    <img src="{{ asset($file_path) }}" alt="Signature"
                                                        style="max-width: 30%;">
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif
                    {{-- @dd($getsafetyPermitExtension) --}}
                    @if (
                        !is_null($getsafetyPermitExtension) &&
                            ($safetypermit['permit_status'] >= STATUS_PERMIT_EXTENDED ||
                                ($safetypermit['permit_status'] >= STATUS_EHS_APPROVE_PENDING &&
                                    $safetypermit['permit_extension_status'] == 1 &&
                                    $safetypermit['permit_status'] != STATUS_PLANTHEAD_REJECTED)))
                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">Permit Extension</h4>
                                </div>
                            </div>
                            @foreach ($getsafetyPermitExtension as $getsafetyPermitExtension)
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Submitted by') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($getsafetyPermitExtension->created_by) ? $getsafetyPermitExtension->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Date') }}</label>
                                        <div class="view_data">
                                            {{ isset($getsafetyPermitExtension->date) ? Displaydateformat($getsafetyPermitExtension->date) : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Time') }}</label>
                                        <div class="view_data">
                                            {{ isset($getsafetyPermitExtension->to_time) ? $getsafetyPermitExtension->to_time : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Remarks') }}</label>
                                        <div class="view_data">
                                            {{ isset($getsafetyPermitExtension->remarks) ? $getsafetyPermitExtension->remarks : '' }}
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if (
                        !is_null($getpermitextensionapproval) &&
                            ($safetypermit['permit_status'] >= STATUS_PERMIT_EXTENDED_APPROVAL ||
                                ($safetypermit['permit_status'] >= STATUS_EHS_APPROVE_PENDING &&
                                    $safetypermit['permit_extension_status'] == 1)))

                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">Permit Extension Approval</h4>
                                </div>
                            </div>
                            @foreach ($getpermitextensionapproval as $getpermitextensionapproval)
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Approver Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($getpermitextensionapproval->approve_reject_by) ? $getpermitextensionapproval->approve_reject_by : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Date') }}</label>
                                        <div class="view_data">
                                            {{ isset($getpermitextensionapproval->date) ? Displaydateformat($getpermitextensionapproval->date) : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Remarks') }}</label>
                                        <div class="view_data">
                                            {{ isset($getpermitextensionapproval->remarks) ? $getpermitextensionapproval->remarks : '' }}
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if (isset($getEhsapproval) &&
                            ($safetypermit['permit_status'] != 8 &&
                                $safetypermit['permit_status'] != STATUS_PERMIT_EXTENDED &&
                                $safetypermit['permit_status'] != STATUS_PLANTHEAD_REJECTED &&
                                $safetypermit['permit_status'] != 5 &&
                                ($safetypermit['permit_status'] > 3 || $safetypermit['permit_status'] > 4)))
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

                    @if (isset($getplantheadapproval) &&
                            ($safetypermit['permit_status'] >= STATUS_PLANT_HEAD_APPROVED &&
                                $safetypermit['permit_status'] != STATUS_EHS_RESUME &&
                                $safetypermit['permit_status'] != STATUS_PERMIT_EXTENDED &&
                                $safetypermit['permit_status'] != STATUS_PERMIT_EXTENDED_APPROVAL))
                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">Plant Head Approval</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Approver Name') }}</label>
                                    <div class="view_data">
                                        {{ isset($getplantheadapproval->approve_reject_by) ? $getplantheadapproval->approve_reject_by : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Date') }}</label>
                                    <div class="view_data">
                                        {{ isset($getplantheadapproval->date) ? Displaydateformat($getplantheadapproval->date) : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Remarks') }}</label>
                                    <div class="view_data">
                                        {{ isset($getplantheadapproval->remarks) ? $getplantheadapproval->remarks : '' }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif

                    @if ($safetypermit['permit_status'] == STATUS_CLOSED)
                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">Permit Closed</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Closed By') }}</label>
                                    <div class="view_data">
                                        {{ getUsername(isset($safetypermit->closed_by) ? $safetypermit->closed_by : '') }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Closed Date') }}</label>
                                    <div class="view_data">
                                        {{ isset($safetypermit->closed_date) ? Displaydateformat($safetypermit->closed_date) : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Closed Remarks') }}</label>
                                    <div class="view_data">
                                        {{ isset($safetypermit->close_remarks) ? $safetypermit->close_remarks : '' }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif
                    @if ($safetypermit['permit_status'] == STATUS_CANCELLED)
                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">Permit Cancelled</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Cancelled By') }}</label>
                                    <div class="view_data">
                                        {{ getUsername(isset($safetypermit->cancelled_by) ? $safetypermit->cancelled_by : '') }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Cancelled Date') }}</label>
                                    <div class="view_data">
                                        {{ isset($safetypermit->cancelled_date) ? Displaydateformat($safetypermit->cancelled_date) : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Cancelled Remarks') }}</label>
                                    <div class="view_data">
                                        {{ isset($safetypermit->cancel_remarks) ? $safetypermit->cancel_remarks : '' }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif


                    <div class="card-body ">
                        <div class="row">
                            <div class="card-header-inner">
                                <h4 class="text-white">Status logs</h4>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>From Status</th>
                                            <th>To Status</th>
                                            <th>Approved By</th>
                                            <th>Remarks</th>
                                            <th>Date</th>

                                        </tr>
                                    </thead>

                                    <tbody>
                                        @if ($status_log->isEmpty())
                                            <tr>
                                                <td class="text-center" colspan="5">No data is available</td>
                                            </tr>
                                        @else
                                        @foreach ($status_log as $status)
                                        <tr>
                                            <td>{{ isset($status['to_status']) ? $status['to_status'] : '-' }}</td>
                                            <td>{{ isset($status['status_name']) ? $status['status_name'] : '-' }}</td>
                                            <td>{{ isset($status['approved_by']) ? getUsername($status['approved_by']) : '-' }}</td>
                                            <td>{{ isset($status['remarks']) ? $status['remarks'] : '-' }}</td>
                                            <td>{{ null !== Displaydateformat($status['created_at']) ? Displaydateformat($status['created_at']) : '-' }}</td>
                                        </tr>

                                        @if ($status['to_status'] == 'EHS Approved')
                                            <tr>
                                                <td>Plant Head Approval Pending</td>
                                                <td>Plant Head Approved</td>
                                                <td>{{ isset($status['approved_by']) ? getUsername($status['approved_by']) : '-' }}</td>
                                                <td>{{ isset($status['remarks']) ? $status['remarks'] : '-' }}</td>
                                                <td>{{ null !== Displaydateformat($status['created_at']) ? Displaydateformat($status['created_at']) : '-' }}</td>
                                            </tr>

                                        @endif
                                    @endforeach

                                        @endif
                                    </tbody>
                                </table>

                            </div>
                        </div>


                    </div>


                </div>
            </div>
        </div>
    </div>
@stop
