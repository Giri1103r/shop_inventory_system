@extends('admin.layouts.admin')
@section('title', 'Expire Medicine List')
@section('pageurl', admin_url('ohc/medicine-receiving-form/list'))
@section('content')
    @push('style')
        <style>
            .table-responsive {
                overflow-x: auto;
                width: 100%
            }
        </style>
    @endpush
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2 me-2">
                        <x-button-filter dataId="" class="search me-2" href=""></x-button-filter>



                    </div>

                    <div id="search" class="collapse">
                        <form action="" id="formsearch">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="medicine_name" class="form-label ">Medicine Name</label>
                                            <select name="medicine_id" id="medicine_id"
                                                class="form-control single-select form-control-sm" style="width: 100%">
                                                <option value="">Select the Medicine Name</option>
                                                @foreach ($medicine as $list)
                                                    <option value="{{ $list->id }}">{{ $list->medicine }}</option>
                                                @endforeach
                                            </select>
                                        </div>


                                        <div class="col-md-3 mb-3 form-input">
                                            <label for="emp_name" class="form-label ">Expire Date</label>
                                            <div class="input-group date form-input custom-height">
                                                <input type="text" class="form-control " name="expire_date"
                                                    id="expire_date" autocomplete="off">
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
                                        <th>Medicine Name</th>
                                        <th>Batch Number</th>
                                        <th>Expire Date</th>
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

            var toDatepicker = flatpickr("#expire_date", {
                dateFormat: "d-m-Y",

            });
            // Initialize DataTable



            // Change event for length selection
            table.on('length.dt', function(e, settings, len) {
                var text = '{{ __('common.show') }} ' + len + ' {{ __('common.records') }}';
                $('.buttons-page-length').find('span').text(text);
            });

            // Search form submit event
            $('#searchform').on('click', function() {
                table.draw();
            });

            // Reset form submit event
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                $('#medicine_id').val('');

                $('#datatable-list').DataTable().draw();
            });
        });

        var table = $('.datatable-list').DataTable({
            autoWidth: true,
            responsive: false,
            processing: false,
            serverSide: true,
            searching: true,
            scrollX: true,
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
                url: "{{ admin_url('ohc/medicine-expire-report/list') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.medicine_id = $('#medicine_id').val();
                    d.expire_date = $('#expire_date').val();

                },
                error: function(xhr, error, code) {
                    if (xhr.status === 419) {
                        alert('Session has expired. You will be redirected to the login page.');
                        window.location.href = "{{ url('') }}"; // Redirect to login page
                    }
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'medicine_id',
                    name: 'medicine_id'
                },


                {
                    data: 'batch_number',
                    name: 'batch_number'
                },

                {
                    data: 'expire_date',
                    name: 'expire_date'
                },

            ],
            language: {
                paginate: {
                    first: '<i title="{{ __('common.first') }}" class="fa fa-angle-double-left" aria-hidden="true"></i>',
                    last: '<i title="{{ __('common.last') }}" class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    next: '<i title="{{ __('common.next') }}" class="fa fa-angle-right" aria-hidden="true"></i>',
                    previous: '<i title="{{ __('common.previous') }}" class="fa fa-angle-left" aria-hidden="true"></i>'
                },
                info: "{{ __('common.dt_info') }}",
                infoEmpty: "{{ __('common.dt_infoEmpty') }}",
                infoFiltered: "{{ __('common.dt_infoFiltered') }}"
            },
            aLengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            buttons: [
                {
                    extend: 'pageLength',
                    text: '{{ __('common.show') }} 10 {{ __('common.records') }}'
                }
            ],
        });

        $(document).on('click', '.stockClose', function() {
            var id = $(this).data('id');
            var login_id = $(this).data('login_id');

            var title = "Do you want to close the stock request?";
            var text = "Close";
            var btncolor = "#28a745";

            Swal.fire({
                title: title,
                icon: "warning",
                input: "textarea",
                inputPlaceholder: "Enter your remarks here...",
                showCloseButton: true,
                confirmButtonText: text,
                confirmButtonColor: btncolor,
                customClass: {
                    confirmButton: "btn-skew"
                },
                preConfirm: (remarks) => {
                    if (!remarks || remarks.trim() === "") {
                        Swal.showValidationMessage("Remarks are required!");
                        return false;
                    }
                    return remarks.trim();
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var remarks = result.value;

                    // Proceed with AJAX request
                    $.ajax({
                        url: "{{ url('ohc/medicine-receiving-form/close') }}",
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        data: {
                            id: id,
                            login_id: login_id,
                            remarks: remarks
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: "success",
                                title: response.msg ||
                                    "Stock request closed successfully!",
                                toast: true,
                                position: "top-right",
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            table.draw();
                        },
                        error: function(xhr) {
                            let errorMessage = "Something went wrong!";

                            // Check if responseJSON exists and contains a 'msg'
                            if (xhr.responseJSON && xhr.responseJSON.msg) {
                                errorMessage = xhr.responseJSON.msg;
                            } else if (xhr.status === 419) {
                                errorMessage = "Session expired. Please refresh and try again.";
                            } else if (xhr.status === 500) {
                                errorMessage =
                                    "Internal Server Error. Please check the server logs.";
                            }

                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: errorMessage
                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush
