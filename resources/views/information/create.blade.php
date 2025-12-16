@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 px-4">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <h3 class="mb-0">Add New Information</h3>
                            <small class="text-muted ml-3">Create a new information page</small>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('information.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-lg" placeholder="Name">
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Slug <span class="text-danger">*</span></label>
                                    <input type="text" name="slug" value="{{ old('slug') }}" class="form-control form-control-lg" placeholder="my-information">
                                    <small class="text-muted">
                                        • Should be lowercase with hyphens instead of spaces (e.g., "my-information")<br>
                                        • Avoid special characters and punctuation<br>
                                        • Should be unique across all informations
                                    </small>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-12">
                                    <label class="font-weight-bold">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="summernote" name="description" placeholder="Description">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <div class="form-row align-items-center">
                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">Position</label>
                                    <select name="position" class="form-control">
                                        <option value="Both" {{ old('position') == 'Both' ? 'selected' : '' }}>Both</option>
                                        <option value="Top Menu" {{ old('position') == 'Top Menu' ? 'selected' : '' }}>Top Menu</option>
                                        <option value="Bottom Footer" {{ old('position') == 'Bottom Footer' ? 'selected' : '' }}>Bottom Footer</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold d-block">Status</label>
                                    <input type="hidden" name="status" value="0">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status">Enabled</label>
                                    </div>
                                </div>

                                <div class="form-group col-md-4 text-right">
                                    <label class="d-block invisible">Save</label>
                                    <button type="submit" class="btn btn-primary btn-lg">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        (function($) {
            $('#summernote').summernote({
                tabsize: 2,
                height: 300,
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
                        $('#summernote').summernote('insertImage', response.url);
                    },
                    error: function(response) {
                        console.error(response);
                    }
                });
            }
        })(jQuery);
    </script>
@endsection
