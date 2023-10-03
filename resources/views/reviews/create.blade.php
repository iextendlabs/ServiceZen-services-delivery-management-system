@extends('layouts.app')
@section('content')
    <div class="container">
<div class="row">
    <div class="col-md-12 margin-tb">
        <h2>Add New Review</h2>
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
<form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Your Name:</strong>
                <input type="text" name="user_name" value="{{ old('user_name') }}" class="form-control">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Service:</strong>
                <select name="service_id" class="form-control">
                    <option></option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Staff:</strong>
                <select name="staff_id" class="form-control">
                    <option></option>
                    @foreach($staffs as $staff)
                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Review:</strong>
                <textarea class="form-control" style="height:150px" name="content" placeholder="Review">{{old('content')}}</textarea>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Images:</strong>
                <table id="imageTable" class="table">
                    <thead>
                        <tr>
                            <th>Images</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <button id="addImageBtn" type="button" class="btn btn-primary float-right">Add Image</button>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong for="video">Upload video</strong>
                <input type="file" name="video" id="video" class="form-control-file" accept="video/*">
                <br>
                <video id="videoPreview" controls style="display:none; max-width:100%;"></video>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><label for="rating">Rating</label><br>
                @for($i = 1; $i <= 5; $i++) <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}>
                    <label class="form-check-label" for="rating{{ $i }}">{{ $i }}</label>
            </div>
            @endfor
        </div>
    </div>

    <div class="col-md-12 text-center">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
    </div>
</form>
</div>
<script>
    $(document).ready(function() {
        $("#addImageBtn").click(function() {
            // Append a new row to the table
            $("#imageTable tbody").append(`
                <tr>
                    <td>
                        <input type="file" name="images[]" class="form-control image-input" accept="image/*">
                        <img class="image-preview" height="130px">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-image">Remove</button>
                    </td>
                </tr>
            `);
        });

        $(document).on("click", ".remove-image", function() {
            $(this).closest("tr").html('');
        });

        $(document).on("change", ".image-input", function(e) {
            var preview = $(this).siblings('.image-preview')[0];
            preview.src = URL.createObjectURL(e.target.files[0]);
        });

        $('#video').on('change', function() {
            const videoPreview = $('#videoPreview')[0];
            const video = this.files[0];

            if (video) {
                const videoURL = URL.createObjectURL(video);
                videoPreview.src = videoURL;
                videoPreview.style.display = 'block';
            } else {
                videoPreview.src = '';
                videoPreview.style.display = 'none';
            }
        });
    });
</script>
@endsection