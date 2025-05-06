@extends('admin.layouts.admin')
@section('title', 'RRAA List')
@section('pageurl', admin_url('rraa/ohc_fire_environment_compliance/list'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="card">
                <h4 class="card-title"></h4>
                <div class="d-flex justify-content-end p-2">

                    <x-button-filter dataId="" class="search me-1" href=""></x-button-filter>

                    <x-button-add dataId="" class="add btn btn-primary ms-1"
                        href="{{ admin_url('rraa/ohc_fire_environment_compliance/add') }}">Add</x-button-add>

                </div>
                <div id="search" class="collapse">
                    <form action="" id="formsearch">
                        <div class="card-body">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4 mb-3 form-input">
                                        <label class="form-label require">Category</label>
                                        <select name="category" class="form-control single-select" id="category" style="width: 100%">
                                            <option value="">Select Category</option>
                                            @foreach ($category as $item)
                                                <option value="{{ encryptId($item->id) }}">
                                                    {{ $item->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3 form-input">
                                        <label for="ohs_compliance_index" class="form-label ">OHS Compliance Index</label>
                                        <input type="text" name="ohs_compliance_index" id="ohs_compliance_index"
                                            class="form-control" placeholder="OHS Compliance Index">
                                    </div>
                                    <div class="col-md-4 mb-3 form-input">
                                        <label class="form-label require">{{ __('inspection.frequency') }}</label>
                                        <select name="frequency" id="frequency"
                                            class=" form-control single-select" style="width: 100%">
                                            <option value="">Select Frequency</option>
                                            @foreach ($frequency as $frequency)
                                                <option value="{{ encryptId($frequency->id) }}">
                                                    {{ $frequency->frequency_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3 form-input">
                                        <label for="scope" class="form-label ">Scope</label>
                                        <input type="text" name="scope" id="scope"
                                            class="form-control" placeholder="Scope">
                                    </div>
                                    <div class="col-md-4  gap-2 mt-3">
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
                                    <th>Category</th>
                                    <th>OHS Compliance Index</th>
                                    <th>Frequency</th>
                                    <th>Scope</th>
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

    });

    $(function() {
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
                url: "{{ admin_url('rraa/ohc_fire_environment_compliance/list') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                        .attr('content')
                },
                data: function(d) {
                    d.category = $('#category').val();
                    d.ohs_compliance_index = $('#ohs_compliance_index').val();
                    d.frequency = $('#frequency').val();
                    d.scope = $('#scope').val();
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
                    data: 'category_name',
                    name: 'category_name'
                },
                {
                    data: 'ohs_compliance_index',
                    name: 'ohs_compliance_index'
                },
                {
                    data: 'frequency_name',
                    name: 'frequency_name'
                },
                {
                    data: 'scope',
                    name: 'scope'
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
                                category = $('#category').val();
                                ohs_compliance_index = $('#ohs_compliance_index').val();
                                frequency = $('#frequency').val();
                                scope = $('#scope').val();

                                $(".dt-button").removeClass('processing');
                                $('body').click();
                                window.location.href =
                                    "{{ admin_url('rraa/ohc_fire_environment_compliance/export/pdf') }}" +
                                    '?search=' + searchValue +
                                    '&category=' + category +
                                    '&ohs_compliance_index=' + ohs_compliance_index +
                                    '&frequency=' + frequency +
                                    '&scope=' + scope
                            }
                        },
                        {
                            extend: 'excel',
                            text: '{{ __('common.excel') }}',
                            action: function(e, dt, button, config) {
                                var searchValue = $('#datatable-list_filter input').val();
                                category = $('#category').val();
                                ohs_compliance_index = $('#ohs_compliance_index').val();
                                frequency = $('#frequency').val();
                                scope = $('#scope').val();

                                $(".dt-button").removeClass('processing');
                                $('body').click();
                                window.location.href =
                                    "{{ admin_url('rraa/ohc_fire_environment_compliance/export/excel') }}" +
                                    '?search=' + searchValue +
                                    '&category=' + category +
                                    '&ohs_compliance_index=' + ohs_compliance_index +
                                    '&frequency=' + frequency +
                                    '&scope=' + scope
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
