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
                        <input type="text" name="name" value="{{ $information->name }}" class="form-control"
                            placeholder="Name" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red">*</span><strong>Description:</strong>
                        <textarea class="form-control" style="height: 150px" name="description"
                            placeholder="Description">{{ $information->description }}</textarea>
                        <script src="https://cdn.ckeditor.com/4.16.1/standard/ckeditor.js"></script>
                        <script>
                            CKEDITOR.replace("description", {
                                filebrowserUploadUrl: '{{ route('ckeditor.upload') }}',
                                filebrowserUploadSuccess: function(
                                    file,
                                    response
                                ) {
                                    var imageUrl = response.url;
                                    var imageInfoUrl = response.image_info_url;

                                    CKEDITOR.instances["description"].insertHtml(
                                        '<img src="' + imageUrl + '" alt="Preview">'
                                    );

                                    window.location.href = imageInfoUrl;
                                },
                            });
                        </script>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Position:</strong>
                        <select name="position" class="form-control">
                            <option value="Top Menu" @if ($information->position == 'Top Menu') selected @endif>Top Menu
                            </option>
                            <option value="Bottom Footer" @if ($information->position == 'Bottom Footer') selected @endif>Bottom Footer
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
