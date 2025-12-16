@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 px-4">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <h3 class="mb-0">Update Information</h3>
                            <small class="text-muted ml-3">Manage the information page content and visibility</small>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br /><br />
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('information.update', $information->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <input type="hidden" name="url" value="{{ url()->previous() }}" />

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ old('name', $information->name) }}" class="form-control form-control-lg" placeholder="Name" />
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Slug <span class="text-danger">*</span></label>
                                    <input type="text" name="slug" value="{{ old('slug', $information->slug) }}" class="form-control form-control-lg" placeholder="my-information">
                                    <small class="form-text text-muted mt-1">
                                        Use lowercase and hyphens (e.g. "my-information"). Avoid special characters.
                                    </small>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-12">
                                    <label class="font-weight-bold">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="summernote" name="description" placeholder="Description">{{ old('description', $information->description) }}</textarea>
                                </div>
                            </div>

                            <div class="form-row align-items-center">
                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">Position</label>
                                    <select name="position" class="form-control">
                                        <option value="Both" {{ old('position', $information->position) == 'Both' ? 'selected' : '' }}>Both</option>
                                        <option value="Top Menu" {{ old('position', $information->position) == 'Top Menu' ? 'selected' : '' }}>Top Menu</option>
                                        <option value="Bottom Footer" {{ old('position', $information->position) == 'Bottom Footer' ? 'selected' : '' }}>Bottom Footer</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold d-block">Status</label>
                                    <input type="hidden" name="status" value="0">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" {{ old('status', $information->status) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status">{{ $information->status ? 'Enabled' : 'Disabled' }}</label>
                                    </div>
                                </div>

                                <div class="form-group col-md-4 text-right">
                                    <label class="d-block invisible">Save</label>
                                    <button type="submit" class="btn btn-primary btn-lg">Update</button>
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
