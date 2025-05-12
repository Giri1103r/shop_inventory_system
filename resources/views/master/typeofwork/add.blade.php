@extends('admin.layouts.admin')
@section('title', 'Type of work Add')
@section('pageurl', admin_url('ptw/typeofworkmaster/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">Company Add</h4> --}}

        </div>
        {{-- <ol class="breadcrumb">
            <li class="breadcrumb-item active ms-auto">
                <a class="d-flex align-self-center" href="{{ admin_url('dashboard') }}">
                    <svg class="me-2 svg-main-icon" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24"
                        version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"></rect>
                            <path
                                d="M3.95709826,8.41510662 L11.47855,3.81866389 C11.7986624,3.62303967 12.2013376,3.62303967 12.52145,3.81866389 L20.0429,8.41510557 C20.6374094,8.77841684 21,9.42493654 21,10.1216692 L21,19.0000642 C21,20.1046337 20.1045695,21.0000642 19,21.0000642 L4.99998155,21.0000673 C3.89541205,21.0000673 2.99998155,20.1046368 2.99998155,19.0000673 L2.99999828,10.1216672 C2.99999935,9.42493561 3.36258984,8.77841732 3.95709826,8.41510662 Z M10,13 C9.44771525,13 9,13.4477153 9,14 L9,17 C9,17.5522847 9.44771525,18 10,18 L14,18 C14.5522847,18 15,17.5522847 15,17 L15,14 C15,13.4477153 14.5522847,13 14,13 L10,13 Z"
                                fill="#009999"></path>
                        </g>
                    </svg>
                    {{ __('common.dashboard') }}
                </a>
            </li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('leftmenu.menu_4') }}</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('leftmenu.menu_8') }}</a></li>
        </ol> --}}
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
                                    <x-button-back href="{{ admin_url('ptw/typeofworkmaster/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="typeofwork_add" enctype="multipart/form-data"
                                        action="{{ admin_url('ptw/typeofworkmaster/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Work Name</label>
                                                    <input type="text" name="work_name" id = "work_name"
                                                        class="form-control" placeholder="Name">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Description</label>
                                                    <input type="text" name="description" id = "description"
                                                        class="form-control" placeholder="Description">
                                                </div>
                                            </div>
                                            <div class="col-md-4 form-input">
                                                <label class = "form-label require">Image</label>
                                                <input type="file" name="typeofwork_upload" id="typeofwork_upload"   accept="image/png, image/jpeg, image/jpg"
                                                    class="form-control" placeholder="Signature">
                                            </div>
                                            <div class="table-wrapper">
                                                <div class="table-container">
                                                    <h3>
                                                        Protective Equipment's To be Worn
                                                    </h3>

                                                    <div>
                                                        <table class="table view_card">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sr.No</th>
                                                                    <th><input type="checkbox" id="select-all"
                                                                            name="select-all" class=""></th>
                                                                    <th>Check Points</th>
                                                                    <th>Is Default Enable</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $index = 1;
                                                                @endphp
                                                                @foreach ($protectivequip_checklist as $detail)
                                                                    <tr>
                                                                        <input type="hidden"
                                                                        name="protectiveequipment[{{ $detail->id }}][checklist_id]"
                                                                        value="{{ $detail->id }}">
                                                                        <td>{{ $index }}</td>
                                                                        <td>
                                                                            
                                                                         
                                                                            <input type="checkbox"
                                                                               name="protectiveequipment[{{ $detail->id }}][left_check]"
                                                                                class="row-checkbox " value="1">

                                                                        </td>
                                                                        <td>
                                                                            <input type="hidden"
                                                                                name="protective[protective_equip][type1][{{ $detail->id }}]"
                                                                                value="{{ $detail->id }}">
                                                                            {{ $detail->protective_equip }}

                                                                        </td>
                                                                        <td class="form-input">

                                                                            <input type="checkbox"
                                                                                name="protectiveequipment[{{ $detail->id }}][right_check]"
                                                                                class="" value="1">
                                                                        </td>
                                                                    </tr>
                                                                    @php
                                                                        $index++;
                                                                    @endphp
                                                                @endforeach
                                                            </tbody>
                                                        </table>

                                                    </div>
                                                </div>

                                                <div class="table-container">
                                                    <h3>
                                                        Equipment Involved
                                                    </h3>

                                                    <div>
                                                        <table class="table view_card">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sr.No</th>
                                                                    <th><input type="checkbox" id="select-all1"
                                                                            name="select-all1" class=""></th>
                                                                    <th>Check Points</th>
                                                                    <th>Is Default Enable</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $index = 1;
                                                                @endphp
                                                                @foreach ($equipinvalve_checklist as $equipinvalve_checklist)
                                                                    <tr>
                                                                        <input type="hidden"
                                                                        name="equipmentinvolved[{{ $equipinvalve_checklist->id }}][checklist_id]"
                                                                        value="{{ $equipinvalve_checklist->id }}">

                                                                        <td>{{ $index }}</td>

                                                                        <td>

                                                                            <input type="hidden"
                                                                                name="equipment[equipinvalve_check][type2][{{ $equipinvalve_checklist->id }}]"
                                                                                value="0">
                                                                            <input type="checkbox"
                                                                                name="equipmentinvolved[{{ $equipinvalve_checklist->id }}][left_check]"
                                                                                class="row-checkbox1 " value="1">

                                                                        </td>

                                                                        <td>
                                                                            <input type="hidden"
                                                                                name="equipment[equip_involve][type2][{{ $equipinvalve_checklist->id }}]"
                                                                                value="{{ $equipinvalve_checklist->id }}">
                                                                            {{ $equipinvalve_checklist->equip_involve }}
                                                                        </td>
                                                                        <td class="form-input">


                                                                            <input type="hidden"
                                                                                name="equipment[equipinvalve_checklist][type2][{{ $equipinvalve_checklist->id }}]"
                                                                                value="0">
                                                                            <input type="checkbox"
                                                                                name="equipmentinvolved[{{ $equipinvalve_checklist->id }}][right_check]"
                                                                                class="" value="1">
                                                                        </td>
                                                                    </tr>
                                                                    @php
                                                                        $index++;
                                                                    @endphp
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                            </div>


                                            <div class="table-wrapper">

                                                <div class="table-container">
                                                    <h3>
                                                       Precaution to be taken</h3>

                                                    <div>
                                                        <table class="table view_card">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sr.No</th>
                                                                    <th><input type="checkbox" id="select-all2"
                                                                            name="select-all2" class=""></th>
                                                                    <th>Check Points</th>
                                                                    <th>Is Default Enable</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $index = 1;
                                                                @endphp
                                                                @foreach ($precaution_checklist as $precaution_checklist)
                                                                    <tr>
                                                                        <input type="hidden"
                                                                        name="manuallist[{{ $precaution_checklist->id }}][checklist_id]"
                                                                        value="{{ $precaution_checklist->id }}">
                                                                        <td>{{ $index }}</td>

                                                                        <td>

                                                                            <input type="hidden"
                                                                                name="manual[precaution_check][type3][{{ $precaution_checklist->id }}]"
                                                                                value="0">
                                                                            <input type="checkbox"
                                                                               name="manuallist[{{ $precaution_checklist->id }}][left_check]"
                                                                                class="row-checkbox2 " value="1">


                                                                        </td>

                                                                        <td>
                                                                            <input type="hidden"
                                                                                name="manual[precaution][type3][{{ $precaution_checklist->id }}]"
                                                                                value="{{ $precaution_checklist->id }}">
                                                                            {{ $precaution_checklist->precaution }}
                                                                        </td>
                                                                        <td class="form-input">


                                                                            <input type="hidden"
                                                                                name="manual[precaution_checklist][type3][{{ $precaution_checklist->id }}]"
                                                                                value="0">
                                                                            <input type="checkbox"
                                                                              name="manuallist[{{ $precaution_checklist->id }}][right_check]"
                                                                                class="" value="1">
                                                                        </td>
                                                                    </tr>
                                                                    @php
                                                                        $index++;
                                                                    @endphp
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <div class="table-container">
                                                    <h3>
                                                       Equipment Inspection</h3>

                                                    <div>
                                                        <table class="table view_card">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sr.No</th>
                                                                    <th><input type="checkbox" id="select-all3"
                                                                            name="select-all3" class=""></th>
                                                                    <th>Check Points</th>
                                                                    <th>Is Default Enable</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $index = 1;
                                                                @endphp
                                                                @foreach ($equipchecklist_checklist as $equipchecklist_checklist)
                                                                <input type="hidden"
                                                                        name="checklist[{{ $equipchecklist_checklist->id }}][checklist_id]"
                                                                        value="{{ $equipchecklist_checklist->id }}">
                                                                    <tr>
                                                                        <td>{{ $index }}</td>


                                                                        <td>

                                                                            <input type="hidden"
                                                                                name="check[equipchecklist_check][type4][{{ $equipchecklist_checklist->id }}]"
                                                                                value="0">
                                                                            <input type="checkbox"
                                                                            name="checklist[{{ $equipchecklist_checklist->id }}][left_check]"
                                                                                class="row-checkbox3" value="1">

                                                                        </td>
                                                                        <td>
                                                                            <input type="hidden"
                                                                                name="check[checklist][type4][{{ $equipchecklist_checklist->id }}]"
                                                                                value="{{ $equipchecklist_checklist->id }}">
                                                                            {{ $equipchecklist_checklist->checklist }}
                                                                        </td>
                                                                        <td class="form-input">


                                                                            <input type="hidden"
                                                                                name="check[equipchecklist_checklist][type4][{{ $equipchecklist_checklist->id }}]"
                                                                                value="0">
                                                                            <input type="checkbox"
                                                                                name="checklist[{{ $equipchecklist_checklist->id }}][right_check]"
                                                                                class=" " value="1">
                                                                        </td>
                                                                    </tr>
                                                                    @php
                                                                        $index++;
                                                                    @endphp
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                            </div>


                                            <div class="table-wrapper">
                                                <div class="table-container">
                                                    <h3>
                                                       Safe Work Instructions </h3>

                                                    <div style="overflow-y: auto; max-height: 400px;">
                                                        <table class="table view_card">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sr.No</th>
                                                                    <th><input type="checkbox" id="select-all4"
                                                                            name="select-all4" class=""></th>
                                                                    <th>Check Points</th>
                                                                    <th>Is Default Enable</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $index = 1;
                                                                @endphp
                                                                @foreach ($safework_checklist as $safework_checklist)
                                                                    <tr>
                                                                        <input type="hidden"
                                                                        name="instructionList[{{ $safework_checklist->id }}][checklist_id]"
                                                                        value="{{ $safework_checklist->id }}">
                                                                        <td>{{ $index }}</td>

                                                                        <td>
                                                                            <input type="hidden"
                                                                                name="instruction[safework_check][type5][{{ $safework_checklist->id }}]"
                                                                                value="0">
                                                                            <input type="checkbox"
                                                                               name="instructionList[{{ $safework_checklist->id }}][left_check]"
                                                                                class="row-checkbox4 " value="1">

                                                                        </td>
                                                                        <td>

                                                                            <input type="hidden"
                                                                                name="instruction[safe_work][type5][{{ $safework_checklist->id }}]"
                                                                                value="{{ $safework_checklist->id }}">
                                                                            {{ $safework_checklist->safe_work }}
                                                                        </td>
                                                                        <td class="form-input">

                                                                            <input type="hidden"
                                                                                name="instruction[safework_checklist][type5][{{ $safework_checklist->id }}]"
                                                                                value="0">
                                                                            <input type="checkbox"
                                                                              name="instructionList[{{ $safework_checklist->id }}][right_check]"
                                                                                class="" value="1">

                                                                        </td>
                                                                    </tr>
                                                                    @php
                                                                        $index++;
                                                                    @endphp
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">

                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ptw/typeofworkmaster/list') }}"></x-button-cancel>
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
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });

        $('#select-all').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.row-checkbox').prop('checked', isChecked);
        });

        $('.row-checkbox').on('change', function() {
            if (!$('#select-all').is(':checked')) {
                const allChecked = $('.row-checkbox').length === $('.row-checkbox:checked').length;
                $('#select-all').prop('checked', allChecked);
            }
        });
        $('#select-all1').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.row-checkbox1').prop('checked', isChecked);
        });

        $('.row-checkbox1').on('change', function() {
            if (!$('#select-all1').is(':checked')) {
                const allChecked = $('.row-checkbox1').length === $('.row-checkbox1:checked').length;
                $('#select-all1').prop('checked', allChecked);
            }
        });

        $('#select-all2').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.row-checkbox2').prop('checked', isChecked);
        });

        $('.row-checkbox2').on('change', function() {
            if (!$('#select-all2').is(':checked')) {
                const allChecked = $('.row-checkbox2').length === $('.row-checkbox2:checked').length;
                $('#select-all2').prop('checked', allChecked);
            }
        });

        $('#select-all3').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.row-checkbox3').prop('checked', isChecked);
        });

        $('.row-checkbox3').on('change', function() {
            if (!$('#select-all3').is(':checked')) {
                const allChecked = $('.row-checkbox3').length === $('.row-checkbox3:checked').length;
                $('#select-all3').prop('checked', allChecked);
            }
        });


        $('#select-all4').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.row-checkbox4').prop('checked', isChecked);
        });

        $('.row-checkbox4').on('change', function() {
            if (!$('#select-all4').is(':checked')) {
                const allChecked = $('.row-checkbox4').length === $('.row-checkbox4:checked').length;
                $('#select-all4').prop('checked', allChecked);
            }
        });
        $(function() {

            if ($.validator) {

                $.validator.addMethod("regex", function(value, element, regexp) {
                    var re = new RegExp(regexp);
                    return this.optional(element) || re.test(value);
                }, "Please check your input.");

                // Initialize validation on the form
                $('#typeofwork_add').validate({
                    rules: {
                        work_name: {
                            required: true,
                            minlength: 3,
                            maxlength: 2000,
                            regex: /^[a-zA-Z0-9-\s]*$/,
                            remote: {
                                url: '{{ admin_url('ptw/typeofworkmaster/unique') }}',
                                type: 'post',
                                data: {
                                    work_name: function() {
                                        return $('#work_name').val();
                                    }
                                }
                            }
                        },
                        description: {
                            required: true,
                            minlength: 3,
                            maxlength: 50,
                            regex: /^[a-zA-Z0-9\s\-_'"(),&/]*$/,
                        },
                        typeofwork_upload: {
                            required: true,
                            extension: "png|jpeg|jpg"
                        },
                    },
                    messages: {
                        work_name: {
                            required: "{{ __('Name is Required') }}",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 2000",
                            remote: "{{ __('Name should be unique') }}",
                        },
                        description: {
                            required: "{{ __('Description is Required') }}",
                            minlength: "{{ __('common.validate_min_length') }}",
                            maxlength: "Maximum Characters should not exceed 100",
                            regex: "description should be alphanumeric and can include -, _, ', \", (, ).,:,;",
                        },
                        typeofwork_upload: {
                            required: "{{ __('Image is Required') }}",
                             extension: "Please select a file with .jpeg,.jpg,.png"
                        },
                    },
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-input').append(error);
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    },
                    submitHandler: function(form) {
                        console.log('Form is valid and ready for submission');
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
            } else {
                console.error("jQuery Validate plugin is not loaded.");
            }
        });
    </script>
@endpush
