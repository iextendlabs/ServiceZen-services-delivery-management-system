@extends('layouts.app')
@section('content')
    <div class="container">
<div class="row">
    <div class="col-md-12 margin-tb">
        <h2>Update Review</h2>
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
<form action="{{ route('reviews.update',$review->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Your Name:</strong>
                <input type="text" name="user_name" value="{{ $review->user_name }}" class="form-control">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Service:</strong>
                <select name="service_id" class="form-control">
                    <option></option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}" @if($service->id == $review->service_id) selected @endif>{{ $service->name }}</option>
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
                    <option value="{{ $staff->id }}" @if($staff->id == $review->staff_id) selected @endif>{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Review:</strong>
                <textarea class="form-control" style="height:150px" name="content" placeholder="Review">{{ $review->content }}</textarea>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Images:</strong>
                <table id="imageTable" class="table">
                    <thead>
                        <tr>
                            <th>Previous Images</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($review->images)
                        @foreach ($review->images as $imagePath)
                        <tr data-image-filename="{{ $imagePath->image }}" data-id="{{ $review->id }}">
                            <td>
                                <img src="/review-images/{{ $imagePath->image }}" height="200px" width="auto" alt="Image">
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
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong for="video">Upload video</strong>
                <input type="file" name="video" id="video" class="form-control-file" accept="video/*">
                <br>
                <video id="videoPreview"  src="/review-videos/{{ $review->video }}" type="video/mp4" controls style="max-width:100%;"></video>
                @if($review->video)
                <button type="button" class="float-right mt-3 btn btn-danger remove-video" data-video="{{ $review->video }}" data-id="{{ $review->id }}">Remove</button>
                @endif
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label for="rating">Rating</label><br>
                @for($i = 1; $i <= 5; $i++) <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" {{ $review->rating == $i ? 'checked' : '' }}>
                    <label class="form-check-label" for="rating{{ $i }}">{{ $i }}</label>
            </div>
            @endfor
        </div>
    </div>
    <div class="col-md-12 text-center">
        <button type="submit" class="btn btn-primary">Update</button>
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
            var row = $(this).closest("tr");
            var imageFilename = row.data('image-filename');
            var id = row.data('id');

            // Make an AJAX call to remove the image from the database
            $.ajax({
                type: "GET",
                url: "/removeReviewImages", // Replace with your route URL
                data: {
                    id: id,
                    image: imageFilename
                },
                success: function(response) {
                    // On success, remove the row from the table
                    row.remove();
                },
                error: function(xhr, status, error) {
                    console.log(error); // Handle the error appropriately
                }
            });
            row.html('');
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

        $(document).on("click", ".remove-video", function() {
            var that = $(this);
            var videoFilename = that.data('video');
            var id = that.data('id');

            // Make an AJAX call to remove the video from the database
            $.ajax({
                type: "GET",
                url: "/removeReviewVideo", // Replace with your route URL
                data: {
                    id: id,
                    video: videoFilename
                },
                success: function(response) {
                    // On success, remove the that from the table
                    that.remove();
                },
                error: function(xhr, status, error) {
                    console.log(error); // Handle the error appropriately
                }
            });
            location.reload()
        });
    });
</script>
@endsection