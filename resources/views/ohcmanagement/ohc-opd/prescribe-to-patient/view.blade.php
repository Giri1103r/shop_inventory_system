@extends('admin.layouts.admin')
@section('title', 'OPD Patient List')
@section('pageurl', admin_url('ohc/prescribe-to-patient/list'))


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
                                    <x-button-back href="{{ admin_url('ohc/prescribe-to-patient/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">OPD Patient List</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Is Outside Worker') }}</label>
                                        <div class="view_data">
                                            @if ($opdpatient->is_outside_employee == 1)
                                                <b><i class="fa-solid fa-check"
                                                        style="color: #267709; width: 15px;"></i></b>
                                            @else
                                            <b><i class="fa fa-times"
                                                style="color: #ee0a0a; width: 15px;"></i></b>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($opdpatient->emp_name) ? $opdpatient->emp_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee Id') }}</label>
                                        <div class="view_data">
                                            {{ isset($opdpatient->emp_id) ? $opdpatient->emp_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Unit') }}</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($opdpatient->unit_id) ? $opdpatient->unit_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Department') }}</label>
                                        <div class="view_data">
                                            {{ getDepartment(isset($opdpatient->department_id) ? $opdpatient->department_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Mobile Number') }}</label>
                                        <div class="view_data">
                                            {{ isset($opdpatient->mobile_no) ? $opdpatient->mobile_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Emergency Contact') }}</label>
                                        <div class="view_data">
                                            {{ isset($opdpatient->emergency_contact) ? $opdpatient->emergency_contact : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-8 form-input">
                                        <label class="form-label view_label">{{ __('Address') }}</label>
                                        <div class="view_data">
                                            {{ isset($opdpatient->address) ? $opdpatient->address : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Date of birth') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($opdpatient->dob) ? $opdpatient->dob : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Gender') }}</label>
                                        <div class="view_data">
                                            {{ isset($opdpatient->gender) ? $opdpatient->gender : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Date ') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($opdpatient->date) ? $opdpatient->date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Time ') }}</label>
                                        <div class="view_data">
                                            {{ isset($opdpatient->time) ? $opdpatient->time : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-8 form-input">
                                        <label class="form-label view_label">{{ __('Cheif Complaint ') }}</label>
                                        <div class="view_data">
                                            {{ isset($opdpatient->cheif_complaint) ? $opdpatient->cheif_complaint : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Vital Check Up') }}</label>
                                        <div class="view_data">
                                            @if ($opdpatient->vital_checkup == 1)
                                                <b><i class="fa-solid fa-check"
                                                        style="color: #267709; width: 15px;"></i></b>
                                            @else
                                            <b><i class="fa fa-times"
                                                style="color: #ee0a0a; width: 15px;"></i></b>
                                            @endif
                                        </div>

                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Suggested By') }}</label>
                                        <div class="view_data">
                                            {{ getSuggestedBy(isset($opdpatient->suggested_by) ? $opdpatient->suggested_by : '' )}}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('First Aid Treatment') }}</label>
                                        <div class="view_data">
                                            @if ($opdpatient->first_aid_treatment== 1)
                                                <b><i class="fa-solid fa-check"
                                                        style="color: #267709; width: 15px;"></i></b>
                                            @else
                                            <b><i class="fa fa-times"
                                                style="color: #ee0a0a; width: 15px;"></i></b>
                                            @endif
                                        </div>

                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Treatment') }}</label>
                                        <div class="view_data">
                                            {{ isset($opdpatient->treatment) ? $opdpatient->treatment : '' }}
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <div class="col-md-12">
                                            <table class="table table-bordered ">

                                                <thead class="bg-secondary" style="color: #ffff">
                                                    <tr>
                                                        <th>S.No</th>
                                                        <th>Medicine Name</th>
                                                        <th>Available Quantity</th>
                                                        <th>Quantity</th>
                                                        <th>Remarks</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @if ($opd_firstaid->isEmpty())
                                                        <tr>
                                                            <td colspan="5" class="text-center">No data available</td>
                                                        </tr>
                                                    @else
                                                        @foreach ($opd_firstaid as $data)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ getMedicinename($data->medicine_id) }}</td>
                                                                <td>{{ $data->available_quantity }}</td>
                                                                <td>{{ $data->quantity }}</td>
                                                                <td>{{ $data->remarks }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Is Reffered') }}</label>
                                        <div class="view_data">
                                            @if ($opdpatient->is_refered== 1)
                                                <b><i class="fa-solid fa-check"
                                                        style="color: #267709; width: 15px;"></i></b>
                                            @else
                                            <b><i class="fa fa-times"
                                                style="color: #ee0a0a; width: 15px;"></i></b>
                                            @endif
                                        </div>

                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Hospital Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($isreffered->hospital_name) ? $isreffered->hospital_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('First Aiders') }}</label>
                                        <div class="view_data">
                                            {{ isset($isreffered->first_aider) ? $isreffered->first_aider : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('First Aider Mobile number') }}</label>
                                        <div class="view_data">
                                            {{ isset($isreffered->mobile_no) ? $isreffered->mobile_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Vechicle') }}</label>
                                        <div class="view_data">
                                            {{ isset($isreffered->refered_vechicle) ? $isreffered->refered_vechicle : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Patient Status') }}</label>
                                        <div class="view_data">
                                            {{ getPatientStatus(isset($opdpatient->patient_status) ? $opdpatient->patient_status : '') }}
                                        </div>
                                    </div>
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
