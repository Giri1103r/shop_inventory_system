@extends('admin.layouts.admin')
@section('title', 'Medicine Store Inspection Checklist ')
@section('pageurl', admin_url('ohc/monthly-medicine-store/inspection/list'))
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
                                    <x-button-back
                                        href="{{ admin_url('ohc/monthly-medicine-store/inspection/list') }}"></x-button-back>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="basic-form mx-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label require">{{ __('inspection.inspection_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection_details->inspection_date) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.next_due') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection_details->next_due) }}
                                                </div>
                                            </div>
                                        </div>


                                        <table class="table table-bordered table-striped">
                                            <thead class="table-secondary">
                                                <th style="text-align: center">Sr. No.</th>
                                                <th style="text-align: center">Name Of Inspection</th>
                                                <th style="text-align: center">Available Quantity</th>
                                                <th style="text-align: center">Expiry Date</th>
                                                <th style="text-align: center">Inspected By</th>
                                                <th style="text-align: center">Remark</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($inspection_data as $medicines)
                                                    <tr>
                                                        <td class="text-center">{{ $loop->iteration }}</td>
                                                        <td class="text-center">
                                                            {{ getMedicinename($medicines['medicine_id']) }}
                                                        <td class="text-center">{{ $medicines['available_quantity'] }}
                                                        <td class="text-center">
                                                            {{ Displaydateformat($medicines['expired_date']) }}
                                                        <td class="text-center">{{ getUsername($medicines['emp_id']) }}
                                                        <td class="text-center">{{ $medicines['remarks'] }}
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row m-2">
                                        <div class="col-md-4 form-group form-input mb-2">
                                            <label class="form-label"
                                                style="display: block; ">{{ __('inspection.signature') }}</label>
                                            <img src="{{ admin_url($inspection_file) }}"
                                                alt="Signature Upload" style="width: 100px; margin-top:-10px">
                                        </div>
                                    </div>
                                </div>

                                <div class="row card">
                                    <div class="card-header-inner p-2">
                                        <h4 class="text-white">EHS Officer Approval</h4>
                                    </div>
                                    <form method="POST" id="forklistassessmentAdd"
                                        action="{{ admin_url('ohc/monthly-medicine-store/inspection/verify/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ encryptId($inspection_details->id) }}" name="id">
                                        <div class="row">
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label ">{{ __('inspection.name') }}</label>
                                                <input type="text" name="name" id = "name" class="form-control"
                                                    value="{{ getUserName(Auth::id()) }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label ">{{ __('inspection.date') }}</label>
                                                <input type="text" name="date" id = "date" class="form-control"
                                                    value="{{ todayDate() }}" readonly>
                                            </div>

                                            <div class="col-md-4 form-group form-input mb-2">
                                                @if (isset(Auth::user()->signature_upload))
                                                    <label class="form-label"
                                                        style="display: block; ">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url(Auth::user()->signature_upload) }}" alt="Signature Upload"
                                                        style="width: 150px; margin-top:-10px">
                                                @else
                                                    <div class="form-input col-md-12 mb-2">
                                                        <label class="form-label require">Signature</label>
                                                        <input type="file" name="signature_image" id="signature_upload"
                                                            class="form-control form-control-sm" accept="image/*"
                                                            placeholder="Enter the image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload" class="text-danger"></div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-12 mb-2 form-input" id="capa_remarks">
                                                <label for="capa_remarks" class="form-label">Remarks</label>
                                                <textarea id="capa_remarks" class="form-control" rows="3" placeholder="Please provide Remarks..."
                                                    name="capa_remarks"></textarea>
                                            </div>
                                            <div class="submit-button mb-3" style="text-align: right;">
                                                <x-button-approve></x-button-approve>
                                                <x-button-reject></x-button-reject>
                                            </div>
                                        </div>
                                    </form>
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
            $('#forklistassessmentAdd').validate({
                rules: {
                    capa_remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 100,
                        noSpaces: true,
                    },
                    signature_image: {
                        required: true,
                    }
                },
                messages: {
                    capa_remarks: {
                        required: "Remarks is Required",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 100",
                    },
                    signature_image: {
                        required: "Signature is Required",
                    }
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-input').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                    $(element).closest('.form-input').find('.invalid-feedback').remove();
                },
                submitHandler: function(form) {
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {});
                }
            });
        </script>
    @endpush
