@extends('admin.layout.layout')

@section('page_title', 'Dashboard')

@section('content')

    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Import Daily Sales ( .xlsx / .csv )</h5>
        </div>
        <div class="card-body">
            <form id="import-form" action="{{ route('admin.import_daily_sales') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="form-group mb-3">
                    <input type="file" name="excel_file[]" id="excel_file" class="form-control" multiple required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-warning">
                        <i class="fa-solid fa-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>

    @livewire('admin.dashboard.low-stock-ingredient-list')

@endsection

@section('script')
    <script>
        $(function() {
            $('#import-form').validate({
                rules: {
                    excel_file: {
                        required: true,
                        extension: "xlsx|csv"
                    }
                },
                messages: {
                    excel_file: {
                        required: "Please upload a sales file.",
                        extension: "Only .xlsx or .csv files are allowed."
                    }
                },
                errorElement: 'span',
                errorClass: 'invalid-feedback',
                errorPlacement: function(error, element) {
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        notifier.show('Error!', 'Please ensure all inputs are correct.', 'warning', '',
                            4000);
                    }
                },
            })
        })
    </script>
@endsection
