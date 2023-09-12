@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 margin-tb">
        <div class="float-start">
            <h2>Edit User</h2>
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
<form action="{{ route('updateProfile',$user->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="profile" value="1">

    <ul class="nav nav-tabs" id="myTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a>
        </li>
        @if($user->hasRole('Staff'))
        @if($socialLinks)
        <li class="nav-item">
            <a class="nav-link" id="social-links-tab" data-toggle="tab" href="#social-links" role="tab" aria-controls="social-links" aria-selected="false">Social Links</a>
        </li>
        @endif
        <li class="nav-item">
            <a class="nav-link" id="gallery-tab" data-toggle="tab" href="#gallery" role="tab" aria-controls="gallery" aria-selected="false">Gallery</a>
        </li>
        @endif
    </ul>
    <div class="tab-content" id="myTabsContent">
        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Name:</strong>
                        <input type="text" name="name" value="{{ $user->name }}" class="form-control" placeholder="Name">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Email:</strong>
                        <input type="email" name="email" value="{{ $user->email }}" class="form-control" placeholder="abc@gmail.com">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Password:</strong>
                        <input type="password" name="password" class="form-control" placeholder="Password">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Confirm Password:</strong>
                        <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password">
                    </div>
                </div>
            </div>
        </div>
        @if($user->hasRole('Staff'))
        @if($socialLinks)
        <div class="tab-pane fade" id="social-links" role="tabpanel" aria-labelledby="social-links-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Instagram <i class="fa fa-instagram"></i>:</strong>
                        <input type="text" name="instagram" class="form-control" placeholder="Instagram" value="{{ $user->staff->instagram }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Snapchat:</strong>
                        <input type="text" name="snapchat" class="form-control" placeholder="Snapchat" value="{{ $user->staff->snapchat }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Facebook:</strong>
                        <input type="text" name="facebook" class="form-control" placeholder="Facebook" value="{{ $user->staff->facebook }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Youtube:</strong>
                        <input type="text" name="youtube" class="form-control" placeholder="Youtube" value="{{ $user->staff->youtube }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Tiktok:</strong>
                        <input type="text" name="tiktok" class="form-control" placeholder="Tiktok" value="{{ $user->staff->tiktok }}">
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="tab-pane fade" id="gallery" role="tabpanel" aria-labelledby="gallery-tab">
            <div class="row">
                <div class="col-md-12">
                    <strong>Youtube Videos:</strong>
                    @if(count($user->staffYoutubeVideo))
                    @foreach($user->staffYoutubeVideo as $staffYoutubeVideo)
                    <div class="form-group">
                        <input type="text" name="youtube_video[]" class="form-control" placeholder="Youtube Video" value="{{ $staffYoutubeVideo->youtube_video }}">
                    </div>
                    @endforeach
                    @endif
                    <div class="form-group" id="video-div">
                    </div>
                    <button id="addVideoBtn" type="button" class="btn btn-primary float-right">Add Youtube Video</button>
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
                                @if($user->staffImages)
                                @foreach ($user->staffImages as $imagePath)
                                <tr data-image-filename="{{ $imagePath->image }}" data-id="{{ $user->id }}">
                                    <td>
                                        <img src="/staff-images/{{ $imagePath->image }}" height="200px" width="auto" alt="Image">
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
            </div>
        </div>
        @endif
        <div class="col-md-12 text-center mt-3">
            <button type="submit" class="btn btn-block btn-primary">Save</button>
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
                        <input type="file" name="gallery_images[]" class="form-control image-input" accept="image/*">
                        <img class="image-preview" height="130px">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-image">Remove</button>
                    </td>
                </tr>
            `);
        });

        $("#addVideoBtn").click(function() {
            // Append a new row to the table
            $("#video-div").append(`
                <div class="form-group">
                    <input type="text" name="youtube_video[]" class="form-control" placeholder="Youtube Video">
                </div>
            `);
        });

        $(document).on("click", ".remove-image", function() {
            var row = $(this).closest("tr");
            var imageFilename = row.data('image-filename');
            var id = row.data('id');

            // Make an AJAX call to remove the image from the database
            $.ajax({
                type: "GET",
                url: "/removeStaffImages", // Replace with your route URL
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
    });
</script>
@endsection