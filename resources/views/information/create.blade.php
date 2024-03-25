@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <h2>Add New Information Page</h2>
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
        <form action="{{ route('information.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Name:</strong>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control"
                            placeholder="Name">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Description:</strong>
                        <textarea class="form-control" style="height:150px" name="description" placeholder="Description">{{ old('description') }}</textarea>
                        <script src="https://cdn.ckeditor.com/4.16.1/standard/ckeditor.js"></script>
                        <script>
                            CKEDITOR.replace('description', {
                                filebrowserUploadUrl: '{{ route('ckeditor.upload') }}',
                                filebrowserUploadSuccess: function(file, response) {
                                    var imageUrl = response.url;
                                    var imageInfoUrl = response.image_info_url;

                                    CKEDITOR.instances['description'].insertHtml('<img src="' + imageUrl + '" alt="Preview">');

                                    window.location.href = imageInfoUrl;
                                }
                            });
                        </script>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Position:</strong>
                        <select name="position" class="form-control">
                            <option value="Top Menu">Top Menu</option>
                            <option value="Bottom Footer">Bottom Footer</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
@endsection
