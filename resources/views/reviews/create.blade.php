@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h4 class="mb-0">Add New Review</h4>
        </div>

        <div class="card-body">
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

            <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">Your Name <span class="text-danger">*</span></label>
                        <input type="text" name="user_name" value="{{ old('user_name') }}" class="form-control"
                            placeholder="Enter your name">
                    </div>

                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">Service</label>
                        <select name="service_id" class="form-control">
                            <option value="">Select a service</option>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}"
                                {{ old('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">Staff</label>
                        <select name="staff_id" class="form-control">
                            @if(auth()->user()->hasRole('Staff'))
                                <option value="{{ auth()->user()->id }}">{{ auth()->user()->name }}</option>
                            @else
                            <option value="">Select staff</option>
                            @foreach($staffs as $staff)
                            <option value="{{ $staff->id }}" {{ old('staff_id') == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">Rating <span class="text-danger">*</span></label>
                        <div>
                            @for($i = 1; $i <= 5; $i++)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}"
                                    value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}>
                                <label class="form-check-label" for="rating{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Review <span class="text-danger">*</span></label>
                    <textarea class="form-control" style="min-height:140px" name="content"
                        placeholder="Write your review...">{{ old('content') }}</textarea>
                </div>

                <div class="form-row align-items-center">
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Feature Review</label>
                        <div class="custom-control custom-switch">
                            <!-- Hidden field ensures a value is sent when checkbox is unchecked -->
                            <input type="hidden" name="feature" value="0">
                            <input type="checkbox" class="custom-control-input" id="feature" name="feature" value="1"
                                {{ old('feature') == '1' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="feature">Enable featured review</label>
                        </div>
                    </div>

                    <div class="form-group col-md-8 text-right">
                        <label class="font-weight-bold d-block">Images</label>
                        <small class="text-muted d-block mb-2">Add one or more images (JPEG, PNG). Preview shown below.</small>
                        <div class="table-responsive">
                            <table id="imageTable" class="table table-borderless mb-2">
                                <thead class="d-none">
                                    <tr><th>Images</th></tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <button id="addImageBtn" type="button" class="btn btn-outline-primary btn-sm">
                            <i class="fa fa-plus mr-1"></i> Add Image
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold" for="video">Upload video</label>
                    <div class="custom-file mb-2" style="max-width:420px;">
                        <input type="file" name="video" id="video" class="custom-file-input" accept="video/*">
                        <label class="custom-file-label" for="video">Choose video file</label>
                    </div>
                    <video id="videoPreview" controls class="w-100 img-fluid" style="display:none; max-height:320px;"></video>
                </div>

                <div class="form-group text-center mt-4">
                    <button type="submit" class="btn btn-primary px-5">Submit</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary ml-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Inline scripts for dynamic previews -->
<script>
    $(document).ready(function() {
        // Update Bootstrap custom-file label text
        $(document).on('change', '.custom-file-input', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).siblings('.custom-file-label').addClass("selected").html(fileName);
        });

        $("#addImageBtn").click(function() {
            $("#imageTable tbody").append(`
                <tr>
                    <td style="width:70%;">
                        <div class="custom-file">
                            <input type="file" name="images[]" class="custom-file-input image-input" accept="image/*">
                            <label class="custom-file-label">Choose image</label>
                        </div>
                        <img class="image-preview img-thumbnail mt-2" style="max-height:130px; display:none;">
                    </td>
                    <td style="width:30%; vertical-align:middle;">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-image">Remove</button>
                    </td>
                </tr>
            `);
        });

        $(document).on("click", ".remove-image", function() {
            $(this).closest("tr").remove();
        });

        $(document).on("change", ".image-input", function(e) {
            var $row = $(this).closest('tr');
            var preview = $row.find('.image-preview');
            var label = $(this).siblings('.custom-file-label');
            var file = e.target.files && e.target.files[0];

            if (file) {
                label.text(file.name);
                preview.attr('src', URL.createObjectURL(file));
                preview.show();
            } else {
                label.text('Choose image');
                preview.attr('src', '').hide();
            }
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