@extends('admin.layouts.admin')
@section('title', 'First Aid Record List')
@section('pageurl', admin_url('ohc/first-aid-record/list'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="card">
                <h4 class="card-title"></h4>
                <div class="d-flex justify-content-end p-2">

                    <x-button-filter dataId="" class="search me-1" href=""></x-button-filter>

                    <x-button-add dataId="" class="add btn btn-primary ms-1"
                        href="{{ admin_url('ohc/first-aid-record/add') }}">Add</x-button-add>

                </div>
                <div id="search" class="collapse">
                    <form action="" id="formsearch">
                        <div class="card-body">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4 mb-3 form-input">
                                        <label class="form-label require">Month</label>
                                        <input type="text" name="month" id="month"
                                            class="form-control" placeholder="Month" value="">
                                    </div>
                                    <div class="col-md-4 mb-3 form-input">
                                        <label class="form-label require">Year</label>
                                        <input type="text" name="year" id="year"
                                            class="form-control" placeholder="Year" value="">
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
                                    <th>Month</th>
                                    <th>Year</th>
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

        $('#month').datepicker({
            format: 'MM',
            minViewMode: 1,
            autoclose: true
        });
        $('#year').datepicker({
            format: 'yyyy',
            viewMode: 'years',
            minViewMode: 'years',
            autoclose: true
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
                url: "{{ admin_url('ohc/first-aid-record/list') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                        .attr('content')
                },
                data: function(d) {
                    d.month = $('#month').val();
                    d.year = $('#year').val();

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
                    data: 'month',
                    name: 'month'
                },
                {
                    data: 'year',
                    name: 'year'
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
                                month = $('#month').val();
                                year = $('#year').val();

                                $(".dt-button").removeClass('processing');
                                $('body').click();
                                window.location.href =
                                    "{{ admin_url('ohc/first-aid-record/export/pdf') }}" +
                                    '?search=' + searchValue +
                                    '&month=' + month +
                                    '&year=' + year
                            }
                        },
                        {
                            extend: 'excel',
                            text: '{{ __('common.excel') }}',
                            action: function(e, dt, button, config) {
                                var searchValue = $('#datatable-list_filter input').val();
                                month = $('#month').val();
                                year = $('#year').val();

                                $(".dt-button").removeClass('processing');
                                $('body').click();
                                window.location.href =
                                    "{{ admin_url('ohc/first-aid-record/export/excel') }}" +
                                    '?search=' + searchValue +
                                    '&month=' + month +
                                    '&year=' + year
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
