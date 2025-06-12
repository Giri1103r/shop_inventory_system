@extends('admin.layouts.admin')
@section('title', 'PPE Stock Inventory')
@section('pageurl', admin_url('ppe_stock_inventory/list'))
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2 me-2">
                        <x-button-filter dataId="" class="search me-2" href=""></x-button-filter>
                        {{-- @if (CheckUserPermission('add'))
                        <x-button-add dataId="" class="add btn btn-primary"
                        href="{{ admin_url('stockitem') }}">Add</x-button-add>
                    @endif --}}

                    </div>


                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_id" class="form-label ">{{__('ppe_management.item_code')}}</label>
                                            <input type="text" name="item_code" id="item_code" class="form-control ">
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">{{ __('ppe_management.inven_item_id') }}</label>
                                            <input type="text" name="inventory_item_id" id="inventory_item_id"
                                                class="form-control ">
                                        </div>

                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">{{__('ppe_management.from_date')}}</label>
                                            <div class="input-group date form-input custom-height">
                                                <input type="text" class="form-control " name="from_date" id="from_date"
                                                    autocomplete="off">
                                                <div class="input-group-addon input-group-text">
                                                    <span class="fa fa-calendar"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">{{__('ppe_management.to_date')}}</label>
                                            <div class="input-group date form-input  custom-height">
                                                <input type="text" class="form-control " name="to_date" id="to_date"
                                                    autocomplete="off">
                                                <div class="input-group-addon input-group-text">
                                                    <span class="fa fa-calendar"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 mt-3">
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
                                class="table primary-table-bordered table-bordered table-striped  nowrap w-100 mt-2 datatable-list">
                                <thead class="thead-primary">
                                    <tr>
                                        <th>{{ __('common.sno') }}</th>
                                        {{-- <th>Org ID</th> --}}
                                        {{-- <th>Inventory Item ID</th> --}}
                                        <th>{{__('ppe_management.item_code')}}</th>
                                        {{-- <th>PPE Name</th> --}}
                                        {{-- <th>SUB</th> --}}
                                        {{-- <th>UOM</th> --}}
                                        <th>{{ __('ppe_management.ppe_qunatity') }}</th>
                                        {{-- <th>{{ __('common.created_by') }}</th> --}}
                                        {{-- <th>{{ __('common.created_date') }}</th> --}}
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
    </div>
@stop
@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {

            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });


            var firstTh = $('.datatable-list thead th:first');
            firstTh.removeClass('sorting_asc');


            var fromDatepicker = flatpickr("#from_date", {
                dateFormat: "d-m-Y",
                onChange: function(selectedDates) {
                    if (selectedDates.length > 0) {
                        var startDate = selectedDates[0];
                        toDatepicker.set('minDate', startDate);
                        toDatepicker.clear();
                    }
                }
            });

            var toDatepicker = flatpickr("#to_date", {
                dateFormat: "d-m-Y",
                minDate: "today"
            });

            var dashboard_sub =
                '{{ isset($dashboard_search['sub']) && $dashboard_search['sub'] != '' ? $dashboard_search['sub'] : '' }}';

            var table = $('.datatable-list').DataTable({
                serverSide: true,
                searching: true,
                ordering: true,
                bSort: true,
                scrollX: true,
                autoWidth: true,
                responsive: false,
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
                    url: "{{ admin_url('ppe_stock_inventory/list') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.item_code = $('#item_code').val();
                        d.inventory_item_id = $('#inventory_item_id').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.dashboard_sub = dashboard_sub;

                    },
                    error: function(xhr, error, code) {
                        if (xhr.status === 419) {
                            alert('Session has expired. You will be redirected to the login page.');
                            window.location.href = "{{ url('') }}";
                        }
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    // {
                    //     data: 'org',
                    //     name: 'org'
                    // },
                    // {
                    //     data: 'inventory_item_id',
                    //     name: 'inventory_item_id'
                    // },
                    {
                        data: 'item_code',
                        name: 'item_code'
                    },
                    // {
                    //     data: 'ppe_name',
                    //     name: 'ppe_name'
                    // },
                    // {
                    //     data: 'sub',
                    //     name: 'sub'
                    // },
                    // {
                    //     data: 'uom',
                    //     name: 'uom'
                    // },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    // {
                    //     data: 'created_by',
                    //     name: 'created_by'
                    // },
                    // {
                    //     data: 'created_at',
                    //     name: 'created_at'
                    // },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    }
                ],
                language: {
                    paginate: {
                        first: '<i title="{{ __('common.first') }}" class="fa fa-angle-double-left" aria-hidden="true"></i>',
                        last: '<i title="{{ __('common.last') }}" class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        next: '<i title="{{ __('common.next') }}" class="fa fa-angle-right" aria-hidden="true"></i>',
                        previous: '<i title="{{ __('common.previous') }}" class="fa fa-angle-left" aria-hidden="true"></i>',
                    },
                    info: "{{ __('common.dt_info') }}",
                    infoEmpty: "{{ __('common.dt_infoEmpty') }}",
                    infoFiltered: "{{ __('common.dt_infoFiltered') }}",
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
                                    var item_code = $('#item_code').val();
                                    var inventory_item_id = $('#inventory_item_id').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();
                                    var dashboard_sub = dashboard_sub;


                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('ppe_stock_inventory/export/pdf') }}" +
                                        '?search=' + searchValue +
                                        '&item_code=' + item_code +
                                        '&inventory_item_id=' + inventory_item_id +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date +
                                        '&dashboard_sub=' + dashboard_sub;

                                }
                            },
                            {
                                extend: 'excel',
                                text: '{{ __('common.excel') }}',
                                action: function(e, dt, button, config) {
                                    var searchValue = $('#datatable-list_filter input').val();
                                    var item_code = $('#item_code').val();
                                    var inventory_item_id = $('#inventory_item_id').val();
                                    var from_date = $('#from_date').val();
                                    var to_date = $('#to_date').val();
                                    var dashboard_sub = dashboard_sub;


                                    $(".dt-button").removeClass('processing');
                                    $('body').click();
                                    window.location.href =
                                        "{{ admin_url('ppe_stock_inventory/export/excel') }}" +
                                        '?search=' + searchValue +
                                        '&item_code=' + item_code +
                                        '&inventory_item_id=' + inventory_item_id +
                                        '&from_date=' + from_date +
                                        '&to_date=' + to_date + '&dashboard_sub=' +
                                        dashboard_sub;

                                }
                            }
                        ]
                    },
                    {
                        extend: 'pageLength',
                        text: '{{ __('common.show') }} 10 {{ __('common.records') }}'
                    }
                ]
            });

            // Update records display text
            table.on('length.dt', function(e, settings, len) {
                var text = '{{ __('common.show') }} ' + len + ' {{ __('common.records') }}';
                $('.buttons-page-length').find('span').text(text);
            });


            $('#searchform').on('click', function() {
                table.draw();
            });


            $('#resetform').on('click', function() {
                $('#item_code').val('');
                $('#inventory_item_id').val('');
                $('#from_date').val('');
                $('#to_date').val('');
                $('#ppe_status').val('');
                table.draw();
            });

            $(document).on('click', '.statusChange', function() {
                var id = $(this).data('id');
                var types = $(this).data('type');
                if (types == 1) {
                    var title = '{{ __('Do You want to In-Activate  PPE Stock Inventory ') }}';
                    var text = '{{ __('common.inactive') }}';
                    var btncolor = '#dc3545'

                } else {
                    var title = '{{ __('Do You want to Activate PPE Stock Inventory') }}';
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
                            url: "{{ admin_url('ppe_stock_inventory/status') }}",
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
        });
    </script>
@endpush
