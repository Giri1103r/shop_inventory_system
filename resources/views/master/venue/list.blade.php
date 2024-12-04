@extends('admin.layouts.admin')
@section('title', 'Venue')
@section('pageurl', admin_url('venue/list'))


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2">

                        <x-button-filter dataId="" class="search me-1" href=""></x-button-filter>
                        @if (CheckUserPermission('import'))
                            <x-button-import href="{{ admin_url('venue/import') }}"></x-button-import>
                        @endif
                        @if (CheckUserPermission('add'))
                            <x-button-add dataId=""  class="add btn btn-primary ms-1"
                                href="{{ admin_url('venue/add') }}">Add</x-button-add>
                        @endif
                    </div>
                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="inspectiontype" class="form-label ">Name of the Conference Hall</label>
                                            <input type="text" name="name_of_the_conference_hall"
                                                id="name_of_the_conference_hall" class="form-control">
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="unit_id" class="form-label ">Unit Name</label>
                                            <select name="unit_id" id="unit_id" class=" form-control single-select"
                                                style="width: 100%">
                                                <option value="">Select Unit Name</option>
                                                @foreach ($unitList as $unit)
                                                    <option value="{{ encryptId($unit->id) }}">
                                                        {{ $unit->unit_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="inspectiontype" class="form-label ">Capacity</label>
                                            <input type="text" name="capacity" id="capacity" class="form-control">
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                                <label class="form-label ">Projector/LCD Availability</label>
                                                <select name="projector_or_lcd_availability"
                                                    id="projector_or_lcd_availability" class="form-control single-select"
                                                    style="width: 100%;">
                                                    <option value="">Select Projector/LCD Availability</option>
                                                    <option value="YES">YES</option>
                                                    <option value="NO">NO</option>
                                                </select>
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="status" class="form-label ">{{ __('common.status') }}</label>
                                            <select name="status" id="status" style="width: 100%"
                                                class="form-control single-select">
                                                <option value="">Select Status</option>
                                                <option value="{{ encryptId(1) }}">Active</option>
                                                <option value="{{ encryptId(0) }}">In-Active</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <x-button-search></x-button-search>
                                            <x-button-reset></x-button-reset>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <hr>
                    </div>


                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable-list"
                                class="table primary-table-bordered table-bordered table-striped display responsive nowrap w-100 mt-2 datatable-list">
                                <thead class="thead-primary">
                                    <tr>
                                        <th>{{ __('common.sno') }}</th>
                                        <th>Conference Hall Name</th>
                                        <th>Unit</th>
                                        <th>Capacity</th>
                                        <th>Projector/LCD Availability</th>
                                        <th>{{ __('common.status') }}</th>
                                        <th>{{ __('common.created_by') }}</th>
                                        <th>{{ __('common.created_date') }}</th>
                                        <th>{{ __('common.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    @stop

    @push('script')
        <script type="text/javascript">
            $(document).ready(function() {
                var firstTh = $('.datatable-list thead th:first');
                firstTh.removeClass('sorting_asc');
            });

            $(function() {
                /* Datatable */
                var table = $('.datatable-list').DataTable({
                    autoWidth: false,
                    responsive: true,
                    processing: false,
                    serverSide: true,
                    searching: true,
                    ordering: true,
                    dom: 'Bfrtip',
                    layout: {
                        top2Start: 'buttons',
                        top2End: {
                            search: {
                                placeholder: ''
                            }
                        },
                        topStart: '',
                        topEnd: '',
                        bottomStart: '',
                        bottomEnd: '',
                        bottom2Start: 'info',
                        bottom2End: 'paging'
                    },

                    ajax: {
                        url: "{{ admin_url('venue/list') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                .attr('content')
                        },
                        data: function(d) {
                            d.name_of_the_conference_hall = $('#name_of_the_conference_hall').val();
                            d.unit_id = $('#unit_id').val();
                            d.capacity = $('#capacity').val();
                            d.projector_or_lcd_availability = $('#projector_or_lcd_availability').val();
                            d.status = $('#status').val();

                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: true,
                        },
                        {
                            data: 'name_of_the_conference_hall',
                            name: 'name_of_the_conference_hall'
                        },
                        {
                            data: 'unit_name',
                            name: 'unit_name'
                        },
                        {
                            data: 'capacity',
                            name: 'capacity'
                        },
                        {
                            data: 'projector_or_lcd_availability',
                            name: 'projector_or_lcd_availability'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'created_by',
                            name: 'created_by'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                        },
                    ],
                    language: {
                        paginate: {
                            first: '<i title="{{ __('common.first') }}" class="fa fa-angle-double-left" aria-hidden="true"></i>',
                            last: '<i title="{{ __('common.last') }}" title="Next" class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            next: '<i title="{{ __('common.next') }}" class="fa fa-angle-right" aria-hidden="true"></i>',
                            previous: '<i title="{{ __('common.previous') }}" class="fa fa-angle-left" aria-hidden="true"></i>',
                        },
                        "info": "{{ __('common.dt_info') }}",
                        "infoEmpty": "{{ __('common.dt_infoEmpty') }}",
                        "infoFiltered": "{{ __('common.dt_infoFiltered') }}",
                    },
                    aLengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    buttons: [{
                            extend: 'collection',
                            text: '{{ __('common.export') }}',
                            buttons: [{
                                    extend: 'pdf',
                                    text: '{{ __('common.pdf') }}',
                                    action: function(e, dt, button, config) {
                                        var searchValue = $('#datatable-list_filter input').val();
                                        name_of_the_conference_hall = $(
                                            '#name_of_the_conference_hall').val();
                                        unit_id = $('#unit_id').val();
                                        capacity = $('#capacity').val();
                                        projector_or_lcd_availability = $(
                                            '#projector_or_lcd_availability').val();
                                        status = $('#status').val();

                                        $(".dt-button").removeClass('processing');
                                        $('body').click();
                                        window.location.href =
                                            "{{ admin_url('venue/export/pdf') }}" +
                                            '?search=' + searchValue +
                                            '&name_of_the_conference_hall=' +
                                            name_of_the_conference_hall +
                                            '&unit_id=' + unit_id +
                                            '&capacity=' + capacity +
                                            '&projector_or_lcd_availability=' +
                                            projector_or_lcd_availability +
                                            '&status=' + status
                                    }
                                },
                                {
                                    extend: 'excel',
                                    text: '{{ __('common.excel') }}',
                                    action: function(e, dt, button, config) {
                                        var searchValue = $('#datatable-list_filter input').val();
                                        name_of_the_conference_hall = $(
                                            '#name_of_the_conference_hall').val();
                                        unit_id = $('#unit_id').val();
                                        capacity = $('#capacity').val();
                                        projector_or_lcd_availability = $(
                                            '#projector_or_lcd_availability').val();
                                        status = $('#status').val();
                                        $(".dt-button").removeClass('processing');
                                        $('body').click();
                                        window.location.href =
                                            "{{ admin_url('venue/export/excel') }}" +
                                            '?search=' + searchValue +
                                            '&name_of_the_conference_hall=' +
                                            name_of_the_conference_hall +
                                            '&unit_id=' + unit_id +
                                            '&capacity=' + capacity +
                                            '&projector_or_lcd_availability=' +
                                            projector_or_lcd_availability +
                                            '&status=' + status
                                    }
                                },
                            ]
                        },

                        {
                            "extend": 'pageLength',
                            "text": '{{ __('common.show') }} 10 {{ __('common.records') }}'
                        }
                    ],

                });

                table.on('length.dt', function(e, settings, len) {
                    var text = '{{ __('common.show') }} ' + len + ' {{ __('common.records') }}';
                    $('.buttons-page-length').find('span').text(text);
                });

                $(document).on('click', '#searchform', function() {
                    table.draw();
                });

                $(document).on('click', '#resetform', function() {
                    $('#formsearch .single-select').val('');
                    $('#formsearch .single-select').trigger('change');
                    setTimeout(function() {
                        table.draw();
                    }, 150);
                });

                /* Status Change */
                $(document).on('click', '.statusChange', function() {
                    var id = $(this).data('id');
                    var types = $(this).data('type');
                    if (types == 1) {
                        var title = '{{ __('Do You want to In-Activate Venue Master') }}';
                        var text = '{{ __('common.inactive') }}';
                        var btncolor = '#dc3545'

                    } else {
                        var title = '{{ __('Do You want to Activate Venue Master') }}';
                        var text = '{{ __('common.active') }}';
                        var btncolor = '#7ddc35'
                    }

                    Swal.fire({
                        title: title,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: text,
                        confirmButtonColor: btncolor,
                        customClass: {
                            confirmButton: 'btn-skew',
                            cancelButton: 'btn-skew'
                        },
                    }).then((result) => {


                        if (result.value) {
                            $.ajax({
                                url: "{{ admin_url('venue/status') }}",
                                type: 'post',

                                data: {
                                    id: id,
                                    types: types
                                },
                                success: function(response) {
                                    const Toast = Swal.mixin({
                                        toast: true,
                                        position: 'top-right',
                                        showConfirmButton: false,
                                        timer: 3000,
                                        timerProgressBar: true,
                                        didOpen: (toast) => {
                                            toast.addEventListener(
                                                'mouseenter',
                                                Swal.stopTimer)
                                            toast.addEventListener(
                                                'mouseleave',
                                                Swal.resumeTimer
                                            )
                                        }
                                    });
                                    Toast.fire({
                                        icon: 'success',
                                        title: response.msg
                                    });
                                    table.draw();
                                },
                                error: function(data) {
                                    $.notify(data.responseJSON.msg, "error");
                                }
                            });
                        } else if (result.isDenied) {
                            Swal.fire('Something went wrong', '', 'info');
                        }
                    })

                });


                /* Delete Record */
                $(document).on('click', '.recordDelete', function() {

                    var id = $(this).data('id');
                    var login_id = $(this).data('login_id');

                    var title = '{{ __('Do You want to Delete Venue Master') }}';
                    var text = '{{ __('common.delete') }}';
                    var btncolor = '#dc3545'

                    Swal.fire({
                        title: title,
                        icon: 'warning',
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: text,
                        confirmButtonColor: btncolor,
                        denyButtonColor: '#28a745',
                        customClass: {
                            confirmButton: 'btn-skew',
                            cancelButton: 'btn-skew'
                        },
                    }).then((result) => {

                        if (result.value) {
                            $.ajax({
                                url: "{{ admin_url('venue/delete') }}",
                                type: 'post',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                        .attr('content')
                                },
                                data: {
                                    id: id,
                                    login_id: login_id
                                },
                                success: function(response) {
                                    const Toast = Swal.mixin({
                                        toast: true,
                                        position: 'top-right',
                                        showConfirmButton: false,
                                        timer: 3000,
                                        timerProgressBar: true,
                                        didOpen: (toast) => {
                                            toast.addEventListener(
                                                'mouseenter',
                                                Swal.stopTimer)
                                            toast.addEventListener(
                                                'mouseleave',
                                                Swal.resumeTimer
                                            )
                                        }
                                    });
                                    Toast.fire({
                                        icon: 'success',
                                        title: response.msg
                                    });
                                    table.draw();
                                },
                                error: function(data) {
                                    if (data.status === 406 && data.responseJSON.msg ===
                                        'module_exits') {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Venue Deletion Failed: Module Dependencies Exist.',
                                        });
                                    } else {
                                        $.notify(data.responseJSON.msg, "error");
                                    }
                                }
                            });
                        } else if (result.isDenied) {
                            Swal.fire('Something went wrong', '', 'info');
                        }
                    })


                });

            });
        </script>
    @endpush
