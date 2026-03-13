@extends('admin.layouts.admin')
@section('title', 'User Permission')
@section('pageurl', admin_url('administration/permission/list'))

@section('content')
    @push('style')
        <style>
            .btn-submit {
                background-color: #30c230 !important;
                color: #fff !important;
                border: none;
                font-weight: 500;
                border-radius: 6px;

                transition:
                    background-color 0.3s ease,
                    box-shadow 0.3s ease,
                    transform 0.2s ease;
            }

            .btn-submit:hover {
                background-color: rgb(104, 199, 116) !important;
                color: #fff !important;
                box-shadow: 0 6px 16px rgba(48, 194, 48, 0.35);
                transform: translateY(-2px);
            }

            .btn-submit:active {
                transform: translateY(0);
                box-shadow: 0 3px 8px rgba(48, 194, 48, 0.25);
            }

            .btn-icon-start i {
                margin-right: 6px;
                transition: transform 0.3s ease;
            }

            .btn-submit:hover .btn-icon-start i {
                transform: scale(1.1);
            }

            .fa-upload {
                font-size: 15px;
            }
        </style>
    @endpush
    <div class="page-wrapper">
        <div class="page-content">

            <div class="card mainCard permission-cart">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center gap-3">
                        <div class="position-relative">
                            {{-- <h5 class="card-title">User Permission</h5> --}}
                        </div>
                        <div class="ms-auto"></div>
                    </div>

                    <form id="userpermission" action="{{ admin_url('administration/permission/update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-4 form-input">
                                    <select name="role" id="role" class="form-control single-select" required>
                                        <option value="">Select Role</option>
                                        @foreach ($roleList as $role)
                                            <option value="{{ encryptId($role->id) }}">{{ $role->role_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="table-responsive">
                            <table id="example2" class="table table-striped table-bordered datatable-list">
                                <thead>
                                    <tr>
                                        <th>Menu</th>
                                        <th>Add</th>
                                        <th>Edit</th>
                                        <th>View</th>
                                        <th>Delete</th>
                                        <th>Export</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $menuList !!}
                                </tbody>
                            </table>

                            <hr>

                            <div class="card-bottom float-end">
                                <div class="col-12 mt-2 mb-3">
                                    <button class="btn btn-submit" type="submit" data-bs-toggle="tooltip"
                                        title="Save">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            // Form validation
            $('#userpermission').validate({
                rules: {
                    role: {
                        required: true
                    },
                },
                messages: {
                    role: {
                        required: "Please select Role"
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
                    form.submit();

                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {

                    });
                }
            });

            // Role change: fetch permissions via AJAX
            $("#role").on("change", function() {
                $("#userpermission input[type=checkbox]").prop("checked", false);
                var id = $(this).val();
                if (id === '') return;

                $.ajax({
                    url: "{{ admin_url('administration/permission/get') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response.status === "success" && response.userpermission) {
                            response.userpermission.forEach(function(permissionName) {
                                $(":checkbox[name='" + permissionName + "']").prop(
                                    "checked", true);
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX request failed: " + error);
                    }
                });
            });

            // Parent checkbox toggling child checkboxes
            $('.parent').change(function() {
                var id = $(this).data('id');
                $(this).closest('tbody').find('.' + id).prop('checked', this.checked);
            });

            // Optional: log child checkbox click
            const childCheckboxes = document.querySelectorAll('.child');
            childCheckboxes.forEach((childCheckbox) => {
                childCheckbox.addEventListener('click', function() {
                    const parentClasses = this.classList;
                    console.log(parentClasses);
                });
            });
        });
    </script>
@endpush
