@extends('admin.layouts.admin')
@section('title', 'PPE Type Edit')
@section('pageurl', admin_url('ppe_type/list'))
@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">

        </div>

    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <!-- row -->
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"></h4>
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ppe_type/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="PpeTypeForm" enctype="multipart/form-data"
                                        action="{{ admin_url('ppe_type/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $encryptid }}">
                                        <div class="row">

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Type</label>
                                                    <input type="text"name="ppe_type" id="ppe_type"
                                                        class="form-control" placeholder="Enter the PPE name"
                                                        value="{{ $ppetype->ppe_type}}">
                                                    <div class="text-danger" id="ppe_type_error"></div>
                                                </div>
                                            </div>

                                        </div>
                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('ppe_type/list') }}"></x-button-cancel>
                                        </div>

                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>

@endsection
@push('script')
    <script>
        $(document).ready(function() {

            $('#PpeTypeForm').on('submit', function(e) {
                let valid = true;



                if (!validatePPEType()) valid = false;


                if (!valid) {
                    e.preventDefault();
                } else {
                    $('#submit').prop('disabled', true);
                }
            });



            function validatePPEType() {

                var name = $('#ppe_type').val();
                var regex = /^[a-zA-Z0-9\-_'"()\s]{3,30}$/;

                if (name === "") {
                    $('#ppe_type_error').text('PPE Name cannot be empty.');
                    return false;
                }

                if (name.length < 3 || name.length > 30) {
                    $('#ppe_type_error').text('PPE Name must be between 3 and 30 characters.');
                    return false;
                }

                if (!regex.test(name)) {
                    $('#ppe_type_error').text('PPE Name should be alphanumeric and can include -, _, \', ", (, ).');
                    return false;
                }

                $('#ppe_type_error').text('');
                return true;
            }

        });
    </script>
@endpush
