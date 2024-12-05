@extends('admin.layouts.admin')
@section('title', 'Training Matrix Show')
@section('pageurl', admin_url('training_matrix/list'))


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
                                    <x-button-back href="{{ admin_url('training_matrix/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Training Matrix Details</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Training Topic</label>
                                        <div class="view_data">
                                            {{ isset($training_matrix->topic_name) ? $training_matrix->topic_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Trainer </label>
                                        <div class="view_data">
                                            {{ isset($training_matrix->emp_name) ? $training_matrix->emp_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Training Offered for </label>
                                        <div class="view_data">
                                            {{ ['1' => 'Worker', '2' => 'Executive'][$training_matrix->training_offered_for] ?? '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Unit Name</label>
                                        <div class="view_data">
                                            {{ isset($training_matrix->unit_name) ? $training_matrix->unit_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Target Department</label>
                                        <div class="view_data">
                                            {{ isset($training_matrix->department_name) ? $training_matrix->department_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Target Content</label>
                                        <div class="view_data">
                                            @if ($training_matrixFiles_target_content)
                                                <a href="{{ asset($training_matrixFiles_target_content->file_path) }}"
                                                    target="_blank" class="d-block mt-2">
                                                    <i class="fa-solid fa-eye text-danger"></i> View</a>
                                            @else
                                                <small class="text-muted">No file uploaded yet.</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Mode of Training </label>
                                        <div class="view_data">
                                            {{ ['1' => 'Online', '2' => 'Offline'][$training_matrix->mode_of_training] ?? '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Training Evaluation</label>
                                        <div class="view_data">
                                            {{ ['1' => 'Yes', '2' => 'No'][$training_matrix->training_evaluation] ?? '' }}
                                        </div>
                                    </div>
                                    @if ($training_matrix->training_evaluation == 1)
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Questionnaire</label>
                                            <div class="view_data">
                                                @if ($training_matrixFiles_questionnaire)
                                                    <a href="{{ asset($training_matrixFiles_questionnaire->file_path) }}"
                                                        target="_blank" class="d-block mt-2">
                                                        <i class="fa-solid fa-eye text-danger"></i> View</a>
                                                @else
                                                    <small class="text-muted">No file uploaded yet.</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($training_matrix->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($training_matrix->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($training_matrix->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

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
