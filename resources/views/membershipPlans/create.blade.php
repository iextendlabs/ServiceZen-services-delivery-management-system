@extends('layouts.app')
@section('content')
    <div class="container-fluid px-1">
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0">Add New Membership Plan</h2>
                </div>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('membershipPlans.store') }}" method="POST">
            @csrf
            <div class="card mt-3">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-12 col-md-6 mb-3">
                            <label class="font-weight-bold"> <span class="text-danger">*</span> Plan Name</label>
                            <input type="text" name="plan_name" class="form-control" value="{{ old('plan_name') }}" placeholder="Plan Name">
                        </div>

                        <div class="form-group col-12 col-md-6 mb-3">
                            <label class="font-weight-bold"> <span class="text-danger">*</span> Membership Fee</label>
                            <input type="number" name="membership_fee" class="form-control" value="{{ old('membership_fee') }}" placeholder="Membership Fee">
                        </div>

                        <div class="form-group col-12 mb-3">
                            <label class="font-weight-bold">Description</label>
                            <textarea class="form-control" id="description_summernote" name="description" placeholder="Description">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group col-12 col-md-6 mb-3">
                            <label class="font-weight-bold">Expire after days</label>
                            <input type="number" name="expire" class="form-control" value="{{ old('expire') }}" placeholder="Enter days like 20">
                        </div>

                        <div class="form-group col-12 col-md-3 mb-3">
                            <label class="font-weight-bold"> <span class="text-danger">*</span> Type</label>
                            <select name="type" class="form-control">
                                <option></option>
                                <option value="Affiliate" {{ old('type') == 'Affiliate' ? 'selected' : '' }}>Affiliate</option>
                                <option value="Freelancer" {{ old('type') == 'Freelancer' ? 'selected' : '' }}>Freelancer</option>
                            </select>
                        </div>

                        <div class="form-group col-12 col-md-3 mb-3">
                            <label class="font-weight-bold"> <span class="text-danger">*</span> Status</label>
                            <select name="status" class="form-control">
                                <option></option>
                                <option value="1" {{ old('status') == '1' ? 'selected' : ''}}>Enable</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : ''}}>Disable</option>
                            </select>
                        </div>

                        <div class="col-12 text-center mt-2">
                            <button type="submit" class="btn btn-md btn-primary shadow-sm float-end font-weight-bold">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <script>
            (function($) {
                $('#description_summernote').summernote({
                    tabsize: 2,
                    height: 250,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['fontname', ['fontname']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']],
                        ['insert', ['picture', 'link', 'video', 'table']],
                        ['misc', ['undo', 'redo']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    popover: {
                        image: [
                            ['custom', ['imageAttributes']],
                            ['resize', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
                            ['float', ['floatLeft', 'floatRight', 'floatNone']],
                            ['remove', ['removeMedia']]
                        ]
                    },
                    callbacks: {
                        onImageUpload: function(files) {
                            uploadImage(files[0]);
                        }
                    }
                });

                function uploadImage(file) {
                    let data = new FormData();
                    data.append("file", file);
                    data.append("_token", "{{ csrf_token() }}");

                    $.ajax({
                        url: "{{ route('summerNote.upload') }}",
                        method: "POST",
                        data: data,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#description_summernote').summernote('insertImage', response.url);
                        },
                        error: function(response) {
                            console.error(response);
                        }
                    });
                }
            })(jQuery);
        </script>

    </div>
@endsection
