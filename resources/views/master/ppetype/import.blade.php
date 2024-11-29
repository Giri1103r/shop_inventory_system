@extends('admin.layouts.admin')
@section('title', 'PPE Type Import')
@section('pageurl', admin_url('ppe_type/list'))
@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h2 class="text-black">{{ __('administration.employee') }}</h2> --}}

        </div>

    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">

            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-end p-2 gap-2">
                                    <x-button-download
                                        href="{{ admin_url('ppe_type/sample_download') }}"></x-button-download>
                                    <x-button-back href="{{ admin_url('ppe_type/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">

                                <div class="basic-form">
                                    <form method="POST" id="ppetypeimport" enctype="multipart/form-data"
                                        action="{{ admin_url('ppe_type/import/Submit') }}">
                                        @csrf
                                        <div class="row">
                                            <div class="mb-3 col-md-6 form-input">
                                                <label class="form-label required">File</label>
                                                <input type="file" id="ppetype_upload" name="ppetype_upload"
                                                    class="form-control" placeholder="">
                                                <div class="text-danger" id="ppetype_upload_error"></div>
                                            </div>
                                        </div>
                                        <hr>

                                        <div class="">
                                            <x-button-submit id="submit"></x-button-submit>
                                            <a href="{{ url('ppe_type/list') }}">
                                                <button type="button" class="btn btn-danger">{{ __('common.cancel') }}</button>
                                            </a>


                                        </div>

                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@push('script')
    <script type="text/javascript">
        $('#ppetypeimport').on('submit', function(e) {
            let valid = true;

            if (!validateImport()) valid = false;

            if (!valid) {
                e.preventDefault();
            } else {
                $('#submit').prop('disabled', true);
            }
        });

        function validateImport() {
            var fileInput = $('#ppetype_upload');
            var filePath = fileInput.val();
            var errorMessage = $('#ppetype_upload_error');


            errorMessage.text('');


            if (!filePath) {
                errorMessage.text('Please select a file to upload.');
                return false;
            }


            var fileExtension = filePath.split('.').pop().toLowerCase();


            if (fileExtension !== 'xlsx') {
                errorMessage.text('Please upload a valid .xlsx file.');
                fileInput.val('');
                return false;
            }

            return true;
        }
    </script>
@endpush
