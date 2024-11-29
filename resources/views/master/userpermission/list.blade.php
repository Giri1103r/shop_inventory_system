@extends('admin.layouts.admin')
@section('title', 'User Permission')
@section('pageurl', admin_url('administration/permission/list'))

@section('content')
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
                            <div class="col-md-4">
                                <div class="form-group form-input">
                                    <label class="form-label require">User Role</label>
                                    <select name="role" id="role" class="form-control single-select"
                                        style="width: 100%;">
                                        <option value="">Select User Role</option>
                                        @foreach ($roleList as $role)
                                            <option value="{{ encryptId($role->id) }}">{{ $role->role_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <h3>Menu</h3>
                            <table id="example2" class="table table-hover mb-0">

                                {!! $menuList !!}
                            </table>
                            <hr>
                            <div class="submit-button" style="text-align: right;">
                                <x-button-submit class="submit"></x-button-submit>
                                <x-button-reset class="submit"></x-button-reset>
                            </div>
                            {{-- <div class="card-bottom">
                                <div class="col-12 mt-2 mb-3">
                                    <button class="btn btn-primary" type="submit" data-bs-toggle="tooltip" title="Save">Submit</button>
                                </div>
                            </div> --}}
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
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });
        $(function() {
            $('#userpermission').validate({
                rules: {
                    role: {
                        required: true,
                    },
                },
                messages: {
                    role: {
                        required: "Please select Role",
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
            });

            // Handle parent checkboxes
            $('.parent').change(function() {
                var id = $(this).data('id');
                $(this).closest('tbody').find('.' + id).prop('checked', this.checked);
            });

            const childCheckboxes = document.querySelectorAll('.child');

            childCheckboxes.forEach((childCheckbox) => {
                childCheckbox.addEventListener('click', function() {
                    const parentClasses = this.classList;
                    console.log(parentClasses);
                });
            });

            $(document).ready(function() {
                $("#role").on("change", function() {
                    $("#userpermission input[type=checkbox]").prop("checked", false);
                    var id = $(this).val();
                    if (id == '') {
                        return true;
                    }

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
                            if (response.status === "success" && response
                                .userpermission) {
                                response.userpermission.forEach(function(
                                permissionName) {
                                    $(":checkbox[name='" + permissionName +
                                        "']").prop("checked", true);
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX request failed: " + error);
                        }
                    });
                });
            });
        });
    </script>
@endpush
