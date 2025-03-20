@extends('admin.layouts.admin')
@section('title', 'MSDS Edit')
@section('pageurl', admin_url('msds/list'))

@section('content')

<style>
    .card-header-inner {
        padding: 11px;
    }
</style>

<div class="clearfix"></div>
<div class="page-titles">
    <div class="d-flex align-items-center">
        {{-- <h4 class="text-black">{{ __('MSDS Edit') }}</h4> --}}
    </div>
</div>

<div class="content-body default-height">
    <div class="container-fluid main-content">
        <div class="row">
            <div class="col-12">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"></h4>
                            <div class="align-back-btc">
                                <x-button-back href="{{ admin_url('msds/list') }}"></x-button-back>
                            </div>
                        </div>

                        <div class="card-body">

                            <div class="basic-form">
                                <form method="POST" id="msdsEdit" action="{{ admin_url('msds/edit/submit') }}">
                                    @csrf
                                   
                                    <input type="hidden" name="id" id="id"
                                    value="{{ encryptId($msdsDetails->id) }}">

                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">MSDS Details</h4>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-input">
                                                <label class="form-label require">Document Number</label>
                                                <input type="text" name="document_number" class="form-control"
                                                       placeholder="Document Number" value="{{ old('document_number', $msdsDetails->document_number) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-input">
                                                <label class="form-label require">Issue Date</label>
                                                <input type="text" name="issue_date" id="issue_date"
                                                       class="form-control" placeholder="Issue Date" value="{{ old('issue_date', $msdsDetails->issue_date) }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group form-input">
                                                <label class="form-label require">Revision Date</label>
                                                <input type="text" name="revision_date" class="form-control"
                                                       placeholder="Revision Date" value="{{ old('revision_date', $msdsDetails->revision_date) }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="row mt-2">
                                            <div
                                                class="d-flex justify-content-end align-items-center me-2 mb-3 button-container">
                                                <button class="btn btn-primary add-row me-3" type="button"
                                                        id="add-row" style="width: 84px;">
                                                    Add
                                                </button>
                                            </div>
                                        </div>

                                        <div id="form-wrapper">
                                            @foreach ($msdsCheckList as $key => $checklist)
                                                <div class="form-set mb-3">
                                                    <div class="card-header-inner">
                                                        <h4 class="text-white">MSDS CheckList</h4>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <button type="button" class="btn btn-danger delete-row">
                                                            <i class="fa-solid fa-trash"></i> Remove
                                                        </button>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Serial Number</label>
                                                                <input type="text" name="serial_number[{{ $key }}]"
                                                                       class="form-control" placeholder="Serial Number"
                                                                       value="{{ $checklist->serial_number }}" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Item Code</label>
                                                                <input type="text" name="item_code[{{ $key }}]"
                                                                       class="form-control" placeholder="Item Code"
                                                                       value="{{ $checklist->item_code }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Name of Chemical</label>
                                                                <input type="text" name="name_of_chemical[{{ $key }}]"
                                                                       class="form-control" placeholder="Name of Chemical"
                                                                       value="{{ $checklist->name_of_chemical }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">MSDS Availability Status</label>
                                                                <select name="msds_availability_status[{{ $key }}]"
                                                                        class="form-control single-select" style="width: 100%">
                                                                    <option value="Yes" {{ $checklist->msds_availability_status == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                                    <option value="No" {{ $checklist->msds_availability_status == 'No' ? 'selected' : '' }}>No</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Remark</label>
                                                                <textarea name="remark[{{ $key }}]" class="form-control" placeholder="Remark" rows="3">{{ $checklist->remark }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="submit-button" style="text-align: right;">
                                        <x-button-submit class="submit"></x-button-submit>
                                        <x-button-reset class="submit"></x-button-reset>
                                        <x-button-cancel href="{{ admin_url('msds/list') }}"></x-button-cancel>
                                    </div>

                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@push('script')
<script type="text/javascript" nonce="projectcab">
    $(document).ready(function() {
        var fromDatepicker = flatpickr("#issue_date", {
            dateFormat: "d-m-Y",
            minDate: new Date(),
        });

        let form_set_count = {{ count($msdsCheckList) }};
        let serial_number = parseInt(`{{ getMSDSCount() }}`, 10) + 1;
        const maxFormSets = 200;
        const minFormSets = 1;

        $(".add-row").click(function() {
            let currentFormSets = $('#form-wrapper .form-set').length;

            if (currentFormSets >= maxFormSets) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Maximum MSDS CheckList Reached',
                    text: 'You can only add up to 200 MSDS CheckList.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            let newSerialNumber = 'MSDS-' + ('0000' + serial_number).slice(-
            5); 

            var newFormSet = `
            <div class="form-set mb-3">
                <div class="card-header-inner">
                    <h4 class="text-white">MSDS CheckList</h4>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-danger delete-row">
                        <i class="fa-solid fa-trash"></i> Remove
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group form-input">
                            <label class="form-label require">Serial Number</label>
                            <input type="text" name="serial_number[]" class="form-control" placeholder="Serial Number" value="${newSerialNumber}" readonly>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group form-input">
                            <label class="form-label require">Item Code</label>
                            <input type="text" name="item_code[${form_set_count}]" class="form-control" placeholder="Item Code" value="">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group form-input">
                            <label class="form-label require">Name of Chemical</label>
                            <input type="text" name="name_of_chemical[${form_set_count}]" class="form-control" placeholder="Name of Chemical" value="">
                        </div>
                    </div>

                    <div class="col-md-4 mt-2">
                        <div class="form-group form-input">
                            <label class="form-label require">MSDS Availability Status</label>
                            <select name="msds_availability_status[${form_set_count}]" class="form-control single-select" style="width: 100%">
                                <option value="">Select MSDS Availability Status</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 mt-2">
                        <div class="form-group form-input">
                            <label class="form-label require">Remark</label>
                            <textarea name="remark[${form_set_count}]" class="form-control" placeholder="Remark" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>`;

            $('#form-wrapper').append(newFormSet);
            form_set_count++;
            serial_number++;
        });
 
        $(document).on('click', '.delete-row', function() {
            let currentFormSets = $('#form-wrapper .form-set').length;

            if (currentFormSets <= minFormSets) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Minimum MSDS CheckList Required',
                    text: 'At least 1 MSDS CheckList is required.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            $(this).closest('.form-set').remove();
        });

    });
</script>
@endpush
