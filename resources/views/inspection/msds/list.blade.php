@extends('admin.layouts.admin')
@section('title', 'MSDS List')
@section('pageurl', admin_url('msds/list'))

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2">

                        <x-button-filter dataId="" class="search me-1" href=""></x-button-filter>

                        <x-button-add dataId="" class="add btn btn-primary ms-1"
                            href="{{ admin_url('msds/add') }}">Add</x-button-add>

                    </div>
                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="document_number" class="form-label ">Document Number</label>
                                            <input type="text" name="document_number" id="document_number"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="issue_date" class="form-label ">Issue Date</label>
                                            <input type="text" name="issue_date" id="issue_date"
                                                class="form-control">
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
                                class="table primary-table-bordered table-bordered table-striped display responsive nowrap w-100 mt-2 datatable-list">
                                <thead class="thead-primary">
                                    <tr>
                                        <th>{{ __('common.sno') }}</th>
                                        <th>Document Number</th>
                                        <th>Issue Date</th>
                                        <th>Revision & Data</th>
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
    </div>

@stop

@push('script')
<script type="text/javascript">
    $(document).ready(function() {

        var firstTh = $('.datatable-list thead th:first');
        firstTh.removeClass('sorting_asc');

        var fromDatepicker = flatpickr("#issue_date", {
            dateFormat: "d-m-Y",
            // minDate: new Date(),
        });

        var fromDatepicker = flatpickr("#revision_date", {
            dateFormat: "d-m-Y",
            // minDate: new Date(),
        });

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
                url: "{{ admin_url('msds/list') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                        .attr('content')
                },
                data: function(d) {
                    d.document_number = $('#document_number').val();
                    d.issue_date = $('#issue_date').val();
                    d.revision_date = $('#revision_date').val();
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
                    searchable: true,
                },

                {
                    data: 'document_number',
                    name: 'document_number'
                },
                {
                    data: 'issue_date',
                    name: 'issue_date'
                },
                {
                    data: 'revision_date',
                    name: 'revision_date'
                },
                {
                    data: 'created_date',
                    name: 'created_date'
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
                                document_number = $('#document_number').val();
                                issue_date = $('#issue_date').val();

                                $(".dt-button").removeClass('processing');
                                $('body').click();
                                window.location.href =
                                    "{{ admin_url('msds/export/pdf') }}" +
                                    '?search=' + searchValue +
                                    '&document_number=' + document_number +
                                    '&issue_date=' + issue_date
                            }
                        },
                        {
                            extend: 'excel',
                            text: '{{ __('common.excel') }}',
                            action: function(e, dt, button, config) {
                                var searchValue = $('#datatable-list_filter input').val();
                                document_number = $('#document_number').val();
                                issue_date = $('#issue_date').val();

                                $(".dt-button").removeClass('processing');
                                $('body').click();
                                window.location.href =
                                    "{{ admin_url('msds/export/excel') }}" +
                                    '?search=' + searchValue +
                                    '&document_number=' + document_number +
                                    '&issue_date=' + issue_date
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

    });
</script>
@endpush
