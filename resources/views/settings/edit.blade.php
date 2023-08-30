@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 margin-tb">
        <div class="float-start">
            <h2>Edit Setting</h2>
        </div>
    </div>
</div>
@if ($message = Session::get('success'))
<div class="alert alert-success">
    <span>{{ $message }}</span>
    <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
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
<form action="{{ route('settings.update',$setting->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Key:</strong>
                <input type="text" name="key" value="{{ $setting->key }}" class="form-control" placeholder="key" disabled>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Value:</strong>
                @if ($setting->key === 'Slider Image')
                <p class="text-danger"><strong>Note: </strong>For optimal slider appearance, kindly upload an image with dimensions 1140 × 504px. Thank you!</p>
                <table id="imageTable" class="table">
                    <thead>
                        <tr>
                            <th>Previous Images</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($setting->value)
                        @foreach (explode(',', $setting->value) as $imagePath)
                        <tr data-image-filename="{{ $imagePath }}" data-id="{{ $setting->id }}">
                            <td>
                                <img src="/slider-images/{{ $imagePath }}" height="200px" width="auto" alt="Image">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger remove-image">Remove</button>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
                <button id="addImageBtn" type="button" class="btn btn-primary float-right">Add Image</button>
                @else
                <input type="text" name="value" value="{{ $setting->value }}" class="form-control" placeholder="Value">
                @endif
            </div>
        </div>
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $("#addImageBtn").click(function() {
            // Append a new row to the table
            $("#imageTable tbody").append(`
                <tr>
                    <td>
                        <input type="file" name="image[]" class="form-control image-input" accept="image/*">
                        <img class="image-preview" height="130px">
                    </td>
                </tr>
            `);
        });

        $(document).on("click", ".remove-image", function() {
            var row = $(this).closest("tr");
            var imageFilename = row.data('image-filename');
            var id = row.data('id');

            // Make an AJAX call to remove the image from the database
            $.ajax({
                type: "GET",
                url: "/removeSliderImage", // Replace with your route URL
                data: {
                    id: id,
                    filename: imageFilename
                },
                success: function(response) {
                    // On success, remove the row from the table
                    row.remove();
                },
                error: function(xhr, status, error) {
                    console.log(error); // Handle the error appropriately
                }
            });
        });

        $(document).on("change", ".image-input", function(e) {
            var preview = $(this).siblings('.image-preview')[0];
            preview.src = URL.createObjectURL(e.target.files[0]);
        });
    });
</script>

@endsection