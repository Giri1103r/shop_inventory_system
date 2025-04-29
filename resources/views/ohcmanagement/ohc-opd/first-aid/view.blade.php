@extends('admin.layouts.admin')
@section('title', 'First Aid Show')
@section('pageurl', admin_url('ohc/first-aid/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Company Show') }}</h4> --}}

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
                                    <x-button-back href="{{ admin_url('ohc/first-aid/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">First Aid</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee Id') }}</label>
                                        <div class="view_data">
                                            {{ isset($opd_first_aid->emp_id) ? $opd_first_aid->emp_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($opd_first_aid->emp_name) ? $opd_first_aid->emp_name : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Date of Incident') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($opd_first_aid->date_of_incident) ? $opd_first_aid->date_of_incident : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Time of Incident') }}</label>
                                        <div class="view_data">
                                            {{ (isset($opd_first_aid->time_of_incident) ? $opd_first_aid->time_of_incident : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Medicine Name') }}</label>
                                        <div class="view_data">
                                            @php
                                                $SelectedMedicineIds = explode(',', $opd_first_aid->medicine_id ?? '');
                                                $SelectedMedicineNames = [];
                                            @endphp

                                            @foreach ($medicine as $list)
                                                @if (in_array($list->id, $SelectedMedicineIds))
                                                    @php
                                                        $SelectedMedicineNames[] = $list->medicine;
                                                    @endphp
                                                @endif
                                            @endforeach

                                            {{ implode(', ', $SelectedMedicineNames) }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-8 form-input">
                                        <label class="form-label view_label">{{ __('Treatment Provided') }}</label>
                                        <div class="view_data">
                                            {{ isset($opd_first_aid->treatment_provided) ? $opd_first_aid->treatment_provided : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-8 form-input">
                                        <label class="form-label view_label">{{ __('Cheif Complaint') }}</label>
                                        <div class="view_data">
                                            {{ isset($opd_first_aid->cheif_complaint) ? $opd_first_aid->cheif_complaint : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Treatment start Time') }}</label>
                                        <div class="view_data">
                                            {{ isset($opd_first_aid->treatment_start_time) ? $opd_first_aid->treatment_start_time : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Treatment end Time') }}</label>
                                        <div class="view_data">
                                            {{ isset($opd_first_aid->treatment_end_time) ? $opd_first_aid->treatment_end_time : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Hospital Name') }}</label>
                                        <div class="view_data">
                                            {{ getHospitalname(isset($opd_first_aid->hospital_id) ? $opd_first_aid->hospital_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('First Aider Name') }}</label>
                                        <div class="view_data">
                                            {{ (isset($opd_first_aid->first_aider_name) ? $opd_first_aid->first_aider_name : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Follow up Required') }}</label>
                                        <div class="view_data">
                                            @if ( $opd_first_aid->follow_up_required == 1)
                                                {{ __('Yes') }}
                                            @else
                                                {{ __('No') }}
                                            @endif

                                        </div>

                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Status') }}</label>
                                        <div class="view_data">
                                            @if ($opd_first_aid->status == 1)
                                                {{ __('Active') }}
                                            @else
                                                {{ __('In-active') }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created By') }}</label>
                                        <div class="view_data">
                                            {{getUsername(isset($opd_first_aid->created_by) ? $opd_first_aid->created_by : '')  }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created Date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($opd_first_aid->created_at) ? $opd_first_aid->created_at : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-8 form-input">
                                        <label class="form-label view_label">{{ __('Remarks ') }}</label>
                                        <div class="view_data">
                                            {{ isset($opd_first_aid->remarks) ? $opd_first_aid->remarks : '' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
