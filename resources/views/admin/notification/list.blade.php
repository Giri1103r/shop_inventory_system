@extends('admin.layouts.admin')
@section('title', 'Notification List')
@section('pageurl', admin_url('dashboard'))

@push('style')
    <style>


    </style>
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">

                        <div class="d-inline-block align-items-center">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('') }}"><i class="mdi mdi-home-outline"></i></a> </li>
                                    <li class="breadcrumb-item" aria-current="page">{{__('administration.notification')}}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="row">

                    <div class="col-12">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ __('administration.notification_list') }}</h4>

                                </div>



                                <div class="card-body ">
                                    <div class="table-responsive">
                                        <table id="datatable-list"
                                            class="table primary-table-bordered table-bordered table-striped display responsive nowrap w-100 mt-2 datatable-list">
                                            <thead class="thead-primary">
                                                <tr>
                                                    <th>{{ __('No.') }}</th>
                                                    <th>{{ __('Notification Type') }}</th>
                                                    <th>{{ __('Notitication') }}</th>
                                                    <th>{{ __('Received') }}</th>
                                                    <th>{{ __('Action') }}</th>
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
            </section>

        </div>
    </div>


@endsection


@push('scripts')
@endpush

@push('script')
<script type="text/javascript">
    $(document).ready(function() {

        var firstTh = $('.datatable-list thead th:first');
        firstTh.removeClass('sorting_asc');
    });

    $(function() {
        / Datatable /
        var table = $('.datatable-list').DataTable({
            autoWidth: false,
            responsive: true,
            processing: false,
            serverSide: true,
            searching: true,
            ordering: true,
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
                url: "{{ admin_url('notification/list') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                        .attr('content')
                },
                data: function(d) {
                    d.factory = $('#notification').val();
                    d.status = $('#status').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'notification_type',
                    name: 'notification_type'
                },
                {
                    data: 'notification_message',
                    name: 'notification_message'
                },
                {
                    data: 'datetime',
                    name: 'datetime'
                },
                {
                    data: 'action',
                    name: 'action'
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
                                factory = $('#factory').val();
                                status = $('#status').val();

                                console.log(factory);

                                $(".dt-button").removeClass('processing');
                                $('body').click();
                                window.location.href =
                                    "{{ admin_url('oper_manage/factory/export/pdf') }}" +
                                    '?search=' + searchValue +
                                    '&factory=' + factory +
                                    '&status=' + status
                            }
                        },
                        {
                            extend: 'excel',
                            text: '{{ __('common.excel') }}',
                            action: function(e, dt, button, config) {
                                var searchValue = $('#datatable-list_filter input').val();
                                factory = $('#factory').val();
                                status = $('#status').val();
                                $(".dt-button").removeClass('processing');
                                $('body').click();
                                window.location.href =
                                    "{{ admin_url('oper_manage/factory/export/excel') }}" +
                                    '?search=' + searchValue +
                                    '&factory=' + factory +
                                    '&status=' + status
                            }
                        },
                    ]
                },

                {
                    "extend": 'pageLength',
                    "text": '{{ _('Show') }} 10 {{ _('Records') }}'
                }
            ],

        });

        table.on('length.dt', function(e, settings, len) {
            var text = '{{ _('Show') }} ' + len + ' {{ _('Records') }}';
            $('.buttons-page-length').find('span').text(text);
        });

        $(document).on('click', '#searchform', function() {
            table.draw();
        });

        $(document).on('click', '#resetform', function() {
            setTimeout(function() {
                table.draw();
            }, 150);
        });

        / Status Change /
        $(document).on('click', '.statusChange', function() {
            var id = $(this).data('id');
            var types = $(this).data('type');
            if (types == 1) {
                var title = '{{ __('administration.factory_inactive_msg') }}';
                var text = '{{ __('common.inactive') }}';
                var btncolor = '#dc3545'

            } else {
                var title = '{{ __('administration.factory_active_msg') }}';
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
                        url: "{{ admin_url('oper_manage/factory/status') }}",
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

        / Delete Record /
        $(document).on('click', '.recordDelete', function() {

            var id = $(this).data('id');

            var title = '{{ __('administration.factory_delete_msg') }}';
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
                        url: "{{ admin_url('oper_manage/factory/delete') }}",
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                .attr('content')
                        },
                        data: {
                            id: id,
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
