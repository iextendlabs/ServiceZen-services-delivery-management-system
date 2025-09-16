@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row align-items-center mb-4">
        <div class="col-md-4 margin-tb">
            <h2>Service Staff Gallery</h2>
        </div>
        <div class="col-md-8 mt-2 mt-md-0">
            <div class="d-flex flex-wrap justify-content-md-end gap-2">
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.general', $serviceStaff->id) }}">
                    General
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.time-slots', $serviceStaff->id) }}">
                    Time Slots
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.zones', $serviceStaff->id) }}">
                    Zones
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.social-links', $serviceStaff->id) }}">
                    Social Links
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.categories-and-services', $serviceStaff->id) }}">
                     Categories & Services
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.documents', $serviceStaff->id) }}">
                     Documents
                </a>
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
    <form action="{{ route('serviceStaff.gallery.update',$serviceStaff->id) }}" method="POST" enctype="multipart/form-data">
        <input type="hidden" value="{{ $serviceStaff->staff->id ?? '' }}" name="staff_id">
        @csrf
        @method('POST')
        <input type="hidden" name="url" value="{{ url()->previous() }}">
        <div class="tab-pane fade show active" id="gallery" role="tabpanel" aria-labelledby="gallery-tab">
            <div class="row">
                <div class="col-md-12">
                    <strong>Youtube Videos:</strong>
                    @if(count($serviceStaff->staffYoutubeVideo))
                    @foreach($serviceStaff->staffYoutubeVideo as $staffYoutubeVideo)
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
                                @if($serviceStaff->staffImages)
                                @foreach ($serviceStaff->staffImages as $imagePath)
                                <tr data-image-filename="{{ $imagePath->image }}" data-id="{{ $serviceStaff->id }}">
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
        <div class="col-md-12 text-center mt-3">
            <button type="submit" class="btn btn-block btn-primary">Update</button>
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