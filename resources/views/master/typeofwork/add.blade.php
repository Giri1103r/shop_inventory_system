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
                                    <form method="POST" id="safe_workadd"
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
                                                        class="form-control" placeholder="Name">
                                                </div>
                                            </div>
                                            <div class="col-md-4 form-input">
                                                <label class = "form-label required">Image</label>
                                                <input type="file" name="typeofwork_upload" id="typeofwork_upload"
                                                    class="form-control" placeholder="Signature">
                                            </div>
                                            <div class="table-wrapper">
                                                <div class="table-container">
                                                    <h3>
                                                        Protective Equipment's To be Worn
                                                    </h3>
                                                    <input type="hidden" name = "type" id="" value = "type_1" >
                                                    <div>
                                                        <table class="table view_card">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sr.No</th>
                                                                    <th><input type="checkbox" id="select-all" class="validate-radio-required"></th>
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
                                                                        <td>{{ $index }}</td>
                                                                        <td>
                                                                            
                                                                            <input type="checkbox" name="check[{{ $detail->id }}]" class="row-checkbox validate-radio-required" value="{{ $detail->id }}">
                                                                        </td>
                                                                        <td>{{ $detail->protective_equip }}</td>
                                                                        <td class="form-input">
                                                                            <!-- Checkbox for 'Is Default Enable' -->
                                                                            <input type="checkbox" name="protectivequip_checklist[{{ $detail->id }}]" class="validate-radio-required" value="YES">
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
                                                    <input type="hidden" name = "type" id="" value = "type_2" >
                                                    <div>
                                                        <table class="table view_card">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sr.No</th>
                                                                    <th><input type="checkbox"
                                                                            name="allprotectivequip_checklist"
                                                                            id="select-all1"
                                                                            class="validate-radio-required"></th>
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
                                                                        <td>{{ $index }}</td>

                                                                        <td>
                                                                            
                                                                            <input type="checkbox" name="check[{{ $equipinvalve_checklist->id }}]" class="row-checkbox1 validate-radio-required" value="{{ $equipinvalve_checklist->id }}">
                                                                        </td>
                                                                     
                                                                        <td>{{ $equipinvalve_checklist->equip_involve }}
                                                                        </td>
                                                                        <td class="form-input">
                                                                            <input type="checkbox"
                                                                                name="equipinvalve_checklist[{{ $equipinvalve_checklist->id }}]"
                                                                                class="validate-radio-required"
                                                                                value="{{ $equipinvalve_checklist->id}}">
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
                                                        Manual List</h3>
                                                        <input type="hidden" name = "type" id="" value = "type_3" >
                                                    <div>
                                                        <table class="table view_card">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sr.No</th>
                                                                    <th><input type="checkbox" id="select-all2"
                                                                            name="allprotectivequip_checklist"
                                                                            class="validate-radio-required"></th>
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
                                                                        <td>{{ $index }}</td>

                                                                        <td>
                                                                            
                                                                            <input type="checkbox" name="check[{{ $precaution_checklist->id }}]" class="row-checkbox2 validate-radio-required" value="{{ $precaution_checklist->id }}">
                                                                        </td>
                                                                       
                                                                        <td>{{ $precaution_checklist->precaution }}</td>
                                                                        <td class="form-input">
                                                                            <input type="checkbox"
                                                                                name="precaution_checklist[{{ $precaution_checklist->id }}]"
                                                                                class="validate-radio-required"
                                                                                value="{{ $precaution_checklist->id}}">
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
                                                        Check List</h3>
                                                        <input type="hidden" name = "type" id="" value = "type_4" >
                                                    <div>
                                                        <table class="table view_card">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sr.No</th>
                                                                    <th><input type="checkbox" id="select-all3"
                                                                            name="allprotectivequip_checklist"
                                                                            class="validate-radio-required"></th>
                                                                    <th>Check Points</th>
                                                                    <th>Is Default Enable</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $index = 1;
                                                                @endphp
                                                                @foreach ($equipchecklist_checklist as $equipchecklist_checklist)
                                                                    <tr>
                                                                        <td>{{ $index }}</td>

                                                                        
                                                                        <td>
                                                                            
                                                                            <input type="checkbox" name="check[{{ $equipchecklist_checklist->id }}]" class="row-checkbox3 validate-radio-required" value="{{ $equipchecklist_checklist->id }}">
                                                                        </td>
                                                                        <td>{{ $equipchecklist_checklist->checklist }}</td>
                                                                        <td class="form-input">
                                                                            <input type="checkbox"
                                                                                name="equipchecklist_checklist[{{ $equipchecklist_checklist->id }}]"
                                                                                class="validate-radio-required"
                                                                                value="{{ $equipchecklist_checklist->id}}">
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
                                                        Instruction List</h3>
                                                        <input type="hidden" name = "type" id="" value = "type_5" >
                                                    <div style="overflow-y: auto; max-height: 400px;">
                                                        <table class="table view_card">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sr.No</th>
                                                                    <th><input type="checkbox" id="select-all4"
                                                                            name="allprotectivequip_checklist"
                                                                            class="validate-radio-required"></th>
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
                                                                        <td>{{ $index }}</td>

                                                                        <td>
                                                                            
                                                                            <input type="checkbox" name="check[{{ $safework_checklist->id }}]" class="row-checkbox4 validate-radio-required" value="{{ $safework_checklist->id }}">
                                                                        </td>
                                                                        <td>{{ $safework_checklist->safe_work }}</td>
                                                                        <td class="form-input">
                                                                            <input type="checkbox"
                                                                                name="safework_checklist[{{ $safework_checklist->id }}]"
                                                                                class="validate-radio-required"
                                                                                value="{{ $safework_checklist->id}}">
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
                                        <div class="submit-button">

                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-cancel></x-button-cancel>
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
            $('#safe_workadd').validate({
                rules: {
                    safe_work: {
                        required: true,

                        minlength: 3,
                        remote: {
                            url: '{{ admin_url('ptw/typeofworkmaster/unique') }}',
                            type: 'post',
                            data: {
                                location_type_name: function() {
                                    return $('#checklist').val();
                                }
                            }
                        }
                    },

                },
                messages: {
                    safe_work: {
                        required: "{{ __('Name is Required') }}",
                        minlength: "{{ __('common.validate_min_length') }}",
                        remote: "{{ __('Name should be unique') }}"
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
                    console.log('test');
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
