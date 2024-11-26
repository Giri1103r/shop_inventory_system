@extends('admin.layouts.admin')
@section('title', 'Equipment Checklist Edit')
@section('pageurl', admin_url('ptw/typeofworkmaster/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Company Edit') }}</h4> --}}

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
                                    <x-button-back
                                        href="{{ admin_url('ptw/typeofworkmaster/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="safe_workadd" enctype="multipart/form-data"
                                        action="{{ admin_url('ptw/typeofworkmaster/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                        value="{{ encryptId($typeofwork->id) }}">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Work Name</label>
                                                    <input type="text" name="work_name" id = "work_name"
                                                        class="form-control" placeholder="Name" value = "{{$typeofwork->work_name}}">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Description</label>
                                                    <input type="text" name="description" id = "description"
                                                        class="form-control" placeholder="Name" value = "{{$typeofwork->description}}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 form-input">
                                                <label class = "form-label required">Image</label>
                                                <input type="file" name="typeofwork_upload" id="typeofwork_upload"
                                                    class="form-control" placeholder="Signature">
                                                    @if (isset($file->file_path))
                                                    <div>
                                                    <a href="{{ asset($file->file_path) }}" target="_blank">
                                                        <img src="{{ asset($file->file_path) }}" alt="Signature"
                                                            style="max-width: 50%;">
                                                    </a>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="table-wrapper">
                                                <div class="table-container">
                                                    <h3>
                                                        Protective Equipment's To be Worn
                                                    </h3>
                                                   
                                                    <div>
                                                        <table class="table view_card" id = "protective">
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
                                                                        <input type="hidden" name="protective[record_id][{{$protective[$detail->id]->id  }}]" value="{{$protective[$detail->id]->id }}">

                                                                        <td>{{ $index }}</td>
                                                                        <td>
                                                                            <input type="hidden" name="protective[protective_check][type1][{{ $detail->id }}]" value="0">
                                                                            <input type="checkbox" name="protective[protective_check][type1][{{ $detail->id }}]" class="row-checkbox validate-radio-required" value="1"{{ getCheckedVal($protective[$detail->id]['checked'], '1') }}>
                                                                            
                                                                        </td>
                                                                        <td>
                                                                            <input type="hidden" name="protective[protective_equip][type1][{{ $detail->id }}]" value="{{$detail->id}}">
                                                                            {{ $detail->protective_equip }}
                                                                        
                                                                        </td>
                                                                        <td class="form-input">

                                                                            <input type="hidden" name="protective[protectivequip_checklist][type1][{{ $detail->id }}]" value="0">
                                                                            <input type="checkbox" name="protective[protectivequip_checklist][type1][{{ $detail->id }}]" class="validate-radio-required" value="1"{{ getCheckedVal($protective[$detail->id]['default_enable'], '1') }}>
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
                                                        <table class="table view_card" id = "equipment">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sr.No</th>
                                                                    <th><input type="checkbox"
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
                                                                        {{-- @dd() --}}
                                                                        <input type="hidden" name="equipment[equipmentrecord_id][{{$equipment[$equipinvalve_checklist->id]->id  }}]" value="{{$equipment[$equipinvalve_checklist->id]->id }}">

                                                                        <td>{{ $index }}</td>

                                                                        <td>

                                                                            <input type="hidden" name="equipment[equipinvalve_check][type2][{{ $equipinvalve_checklist->id }}]" value="0">
                                                                            <input type="checkbox" name="equipment[equipinvalve_check][type2][{{ $equipinvalve_checklist->id }}]" class="row-checkbox1 validate-radio-required" value="1"{{ getCheckedVal($equipment[$equipinvalve_checklist->id]['checked'], '1') }}>

                                                                        </td>
                                                                     
                                                                        <td>
                                                                            <input type="hidden" name="equipment[equip_involve][type2][{{ $equipinvalve_checklist->id }}]" value="{{$equipinvalve_checklist->id}}">
                                                                            {{ $equipinvalve_checklist->equip_involve }}
                                                                        </td>
                                                                        <td class="form-input">
                                                                    
                                                                            <input type="hidden" name="equipment[equipinvalve_checklist][type2][{{ $equipinvalve_checklist->id }}]" value="0">
                                                                            <input type="checkbox" name="equipment[equipinvalve_checklist][type2][{{ $equipinvalve_checklist->id }}]" class=" validate-radio-required" value="1"{{ getCheckedVal($equipment[$equipinvalve_checklist->id]['default_enable'], '1') }}>
                                                                            
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
                                                       
                                                    <div>
                                                        <table class="table view_card" id = "manual">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sr.No</th>
                                                                    <th><input type="checkbox" id="select-all2"
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

                                                                        <input type="hidden" name="manual[manualrecord_id][{{$manual[$precaution_checklist->id]->id  }}]" value="{{$manual[$precaution_checklist->id]->id }}">

                                                                        <td>{{ $index }}</td>

                                                                        <td>

                                                                            <input type="hidden" name="manual[precaution_check][type3][{{ $precaution_checklist->id }}]" value="0">
                                                                          
                                                                            <input type="checkbox" name="manual[precaution_check][type3][{{ $precaution_checklist->id }}]" class="row-checkbox2 validate-radio-required" value="1"{{ getCheckedVal($manual[$precaution_checklist->id]['checked'], '1') }}>
                                                                            
                                                                           
                                                                        </td>
                                                                       
                                                                        <td>
                                                                            <input type="hidden" name="manual[precaution][type3][{{ $precaution_checklist->id }}]" value="{{$precaution_checklist->id}}">
                                                                            {{ $precaution_checklist->precaution }}
                                                                        </td>
                                                                        <td class="form-input">

                                                                            
                                                                            <input type="hidden" name="manual[precaution_checklist][type3][{{ $precaution_checklist->id }}]" value="0">
                                                                           
                                                                            <input type="checkbox" name="manual[precaution_checklist][type3][{{ $precaution_checklist->id }}]" class=" validate-radio-required" value="1"{{ getCheckedVal($manual[$precaution_checklist->id]['default_enable'], '1') }}>
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
                                                        
                                                    <div>
                                                        <table class="table view_card" id = "check">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sr.No</th>
                                                                    <th><input type="checkbox" id="select-all3"
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


                                                                        <input type="hidden" name="check[checkrecord_id][{{$check[$equipchecklist_checklist->id]->id  }}]" value="{{$check[$equipchecklist_checklist->id]->id }}">
                                                                        <td>{{ $index }}</td>

                                                                        
                                                                        <td>

                                                                            <input type="hidden" name="check[equipchecklist_check][type4][{{ $equipchecklist_checklist->id }}]" value="0">
                                                                          
                                                                            <input type="checkbox" name="check[equipchecklist_check][type4][{{ $equipchecklist_checklist->id }}]" class="row-checkbox3 validate-radio-required" value="1"{{ getCheckedVal($check[$equipchecklist_checklist->id]['checked'], '1') }}>

                                                                        </td>
                                                                        <td>
                                                                            <input type="hidden" name="check[checklist][type4][{{ $equipchecklist_checklist->id }}]" value="{{$equipchecklist_checklist->id}}">
                                                                            {{ $equipchecklist_checklist->checklist }}
                                                                        </td>
                                                                        <td class="form-input">


                                                                            <input type="hidden" name="check[equipchecklist_checklist][type4][{{ $equipchecklist_checklist->id }}]" value="0">
                                                                        
                                                                            <input type="checkbox" name="check[equipchecklist_checklist][type4][{{ $equipchecklist_checklist->id }}]" class=" validate-radio-required" value="1"{{ getCheckedVal($check[$equipchecklist_checklist->id]['default_enable'], '1') }}>
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
                                                       
                                                    <div style="overflow-y: auto; max-height: 400px;">
                                                        <table class="table view_card" id = instruction>
                                                            <thead>
                                                                <tr>
                                                                    <th>Sr.No</th>
                                                                    <th><input type="checkbox" id="select-all4"
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


                                                                    
                                                                    <input type="hidden" name="instruction[instructionrecord_id][{{$instruction[$safework_checklist->id]->id  }}]" value="{{$instruction[$safework_checklist->id]->id }}">
                                                                    <td>{{ $index }}</td>
                                                                    <td>
                                                                        <input type="hidden" name="instruction[safework_check][type5][{{ $safework_checklist->id }}]" value="0">
                                                                        <input type="checkbox" name="instruction[safework_check][type5][{{ $safework_checklist->id }}]" class="row-checkbox4 validate-radio-required" value="1" {{ getCheckedVal($instruction[$safework_checklist->id]['checked'], '1') }}>
                                                                    </td>
                                                                    <td>
                                                                        <input type="hidden" name="instruction[safe_work][type5][{{ $safework_checklist->id }}]" value="{{ $safework_checklist->id }}">
                                                                        {{ $safework_checklist->safe_work }}
                                                                    </td>
                                                                    <td class="form-input">
                                                                        <input type="hidden" name="instruction[safework_checklist][type5][{{ $safework_checklist->id }}]" value="0">
                                                                        <input type="checkbox" name="instruction[safework_checklist][type5][{{ $safework_checklist->id }}]" class="validate-radio-required" value="1" {{ getCheckedVal($instruction[$safework_checklist->id]['default_enable'], '1') }}>
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
            $('#checklistedit').validate({
                rules: {
                    protective_equip: {
                        required: true,
                        minlength: 3,
                        remote: {
                            url: '{{ admin_url("ptw/checklistmaster/unique") }}',
                            type: 'post',
                            data: {
                                location_type_name: function() {
                                    return $('#checklist').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                }
                            }
                        }
                    },
                },
                messages: {
                    protective_equip: {
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
