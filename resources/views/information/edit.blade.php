@extends('layouts.app') @section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <h2>Update Information Page</h2>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br /><br />
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('information.update', $information->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <input type="hidden" name="url" value="{{ url()->previous() }}" />
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red">*</span><strong>Name:</strong>
                        <input type="text" name="name" value="{{ old('name' ,$information->name) }}" class="form-control"
                            placeholder="Name" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red">*</span><strong>Description:</strong>
                        <textarea class="form-control" id="summernote" name="description"
                            placeholder="Description">{{ old( 'description' ,$information->description ) }}</textarea>
                            <script>
                                (function($) {
                                    $('#summernote').summernote({
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
                                                $('#summernote').summernote('insertImage', response.url);
                                            },
                                            error: function(response) {
                                                console.error(response);
                                            }
                                        });
                                    }
                                })(jQuery);
                            </script>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Position:</strong>
                        <select name="position" class="form-control">
                            <option value="Top Menu" {{ old('position', $information->position) == 'Top Menu' ? 'selected' : '' }}>Top Menu
                            </option>
                            <option value="Bottom Footer" {{ old('position', $information->position) == 'Bottom Footer' ? 'selected' : '' }}>Bottom Footer
                            </option>
                        </select>
                        
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
@endsection
