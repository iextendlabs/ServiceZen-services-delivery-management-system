@extends('layouts.app')
@section('content')
<div class="container">
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
                    @if(in_array($setting->key, ['Emails For Daily Alert', 'Not Allowed Status for Staff App', 'Not Allowed Status for Driver App']))
                    <span class="text-danger">Note: Add by comma separated.</span>
                    @endif

                    @if ($setting->key === 'Slider Image' || $setting->key === 'Slider Image For App')
                    <p class="text-danger"><strong>Note: </strong>For optimal slider appearance, kindly upload an image with dimensions @if ($setting->key === 'Slider Image' ) 1140 × 504px. @else 325 x 200px. @endif Thank you!</p>
                    <table id="imageTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Previous Images</th>
                                <th>Link</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($setting->value)
                                @foreach (explode(',', $setting->value) as $imagePath)
                                    @php
                                        // Extract type, id, and filename from the imagePath
                                        list($type, $id, $filename) = explode('_', $imagePath);
                                    @endphp
                                    <tr data-image-filename="{{ $filename }}" data-id="{{ $setting->id }}">
                                        <td>
                                            <img src="/slider-images/{{ $filename }}" height="200px" width="auto" alt="Image">
                                        </td>
                                        <td>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <strong class="float-start mb-2">Select Category or Service for Link</strong>
                                                    <select name="link_type[]" class="form-control col-9 link-type" disabled>
                                                        <option></option>
                                                        <option value="category" {{ $type === 'category' ? 'selected' : '' }}>Categories</option>
                                                        <option value="service" {{ $type === 'service' ? 'selected' : '' }}>Services</option>
                                                        <option value="customLink" {{ $type === 'customLink' ? 'selected' : '' }}>Custom Link</option>
                                                    </select>
                                                    <div class="category" style="display: {{ $type === 'category' ? 'block' : 'none' }};">
                                                        <select name="linked_item[]" class="form-control col-9 mt-2 linked-item category-option" disabled>
                                                            <option></option>
                                                            @foreach($categories as $category)
                                                                <option value="{{ $category->id }}" data-type="category" {{ ($type === 'category' && $id == $category->id) ? 'selected' : '' }}>{{ $category->title }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="service" style="display: {{ $type === 'service' ? 'block' : 'none' }};">
                                                        <select name="linked_item[]" class="form-control col-9 mt-2 linked-item service-option" disabled>
                                                            <option></option>
                                                            @foreach($services as $service)
                                                                <option value="{{ $service->id }}" data-type="service" {{ ($type === 'service' && $id == $service->id) ? 'selected' : '' }}>{{ $service->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="customLink" style="display: {{ $type === 'customLink' ? 'block' : 'none' }};">
                                                        <input name="linked_item[]" type="text" class="form-control col-9 mt-2 linked-item customLink-option" value={{ $id }} disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger remove-image"><i class="fa fa-minus-circle"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    <button id="addImageBtn" type="button" class="btn btn-primary float-right"><i class="fa fa-plus-circle"></i></button>
                    @elseif($setting->key === 'Social Links of Staff')
                    <select name="value" class="form-control">

                        <option value="1" @if($setting->value == 1) selected @endif>Enable</option>
                        <option value="0" @if($setting->value == 0) selected @endif>Disable</option>
                    </select>
                    @elseif($setting->key === 'Daily Order Summary Mail and Notification')
                    <input type="time" name="value" class="form-control" value="{{ $setting->value }}">
                    @elseif($setting->key === 'Terms & Condition' || $setting->key === 'About Us' || $setting->key === 'Privacy Policy' || $setting->key === 'Contact Us')
                    <textarea name="value" style="height:150px" class="form-control"> {{ $setting->value }}</textarea>
                    <script src="https://cdn.ckeditor.com/4.16.1/standard/ckeditor.js"></script>
                    <script>
                        CKEDITOR.replace('value', {
                            filebrowserUploadUrl: '{{ route("ckeditor.upload") }}',
                            filebrowserUploadSuccess: function (file, response) {
                                var imageUrl = response.url;
                                var imageInfoUrl = response.image_info_url;

                                CKEDITOR.instances['value'].insertHtml('<img src="' + imageUrl + '" alt="Preview">');

                                window.location.href = imageInfoUrl;
                            }
                        });
                    </script>
                    @elseif($setting->key === 'Head Tag')
                    <textarea name="value" style="height:150px" class="form-control"> {{ $setting->value }}</textarea>
                    @elseif($setting->key === 'Featured Services')
                    <input type="text" name="search-services" id="search-services" class="form-control" placeholder="Search Services By Name">
                    <div class="scroll-div">
                        <table class="table table-striped table-bordered services-table">
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Duration</th>
                            </tr>
                            @foreach ($services as $service)
                            @if ($service->status)
                            <tr>
                                <td>
                                    <input type="checkbox" @if(in_array($service->id,explode(',', $setting->value))) checked @endif class="service-checkbox" name="service_ids[]" value="{{ $service->id }}">
                                </td>
                                <td>{{ $service->name }}</td>

                                <td>AED<span class="price">{{ isset($service->discount) ? 
                                    $service->discount : $service->price }}</span></td>
                                <td>{{ $service->duration }}</td>
                            </tr>
                            @endif
                            @endforeach

                        </table>
                    </div>

                    @elseif($setting->key === 'App Categories')
                    <input type="text" name="search-categories" id="search-categories" class="form-control" placeholder="Search Categories By Name">
                    <div class="scroll-div">
                        <table class="table table-striped table-bordered categories-table">
                            <tr>
                                <th></th>
                                <th>Name</th>
                            </tr>
                            @foreach ($categories as $category)
                            <tr>
                                <td>
                                    <input type="checkbox" @if(in_array($category->id,explode(',', $setting->value))) checked @endif class="category-checkbox" name="category_ids[]" value="{{ $category->id }}">
                                </td>
                                <td>{{ $category->title }}</td>
                            </tr>
                            @endforeach

                        </table>
                    </div>
                    @elseif($setting->key === 'App Offer Alert')
                        @if($setting->value)
                            @php
                                list($status, $type, $id, $filename) = explode('_', $setting->value);
                            @endphp
                            <hr>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <span style="color: red;">*</span><strong for="image">Image</strong>
                                    <p class="text-danger"><strong>Note: </strong>Upload image with dimensions 325 x 200px Thank you!</p>
                                    <input type="file" name="image" id="image" class="form-control-file">
                                    <br>
                                    <img id="preview" src="/uploads/{{$filename}}" height="130px">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <strong class="float-start mb-2">Select Category or Service for Link</strong>
                                    <select name="link_type" class="form-control col-12 link-type">
                                        <option></option>
                                        <option value="category" {{ $type === 'category' ? 'selected' : '' }}>Categories</option>
                                        <option value="service" {{ $type === 'service' ? 'selected' : '' }}>Services</option>
                                    </select>
                                    <div class="category" style="display: {{ $type === 'category' ? 'block' : 'none' }};">
                                        <select name="linked_item" class="form-control col-12 mt-2 linked-item category-option">
                                            <option></option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" data-type="category" {{ ($type === 'category' && $id == $category->id) ? 'selected' : '' }}>{{ $category->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="service" style="display: {{ $type === 'service' ? 'block' : 'none' }};">
                                        <select name="linked_item" class="form-control col-12 mt-2 linked-item service-option">
                                            <option></option>
                                            @foreach($services as $service)
                                                <option value="{{ $service->id }}" data-type="service" {{ ($type === 'service' && $id == $service->id) ? 'selected' : '' }}>{{ $service->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <strong>Status:</strong>
                                    <select name="status" class="form-control">

                                        <option value="1" @if($status == 1) selected @endif>Enable</option>
                                        <option value="0" @if($status == 0) selected @endif>Disable</option>
                                    </select>
                                </div>
                            </div>
                        @endif
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
</div>
<script>
    $("#addImageBtn").click(function() {
        // Append a new row to the table
        $("#imageTable tbody").append(`
            <tr>
                <td style="display: flex; align-items: center; justify-content: center;">
                    <div style="text-align: center;">
                        <input type="file" name="image[]" class="form-control image-input" accept="image/*">
                        <img class="image-preview" height="130px">
                    </div>
                </td>
                <td>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong class="float-start mb-2">Select Category or Service for Link</strong>
                                <select name="link_type[]" class="form-control col-9 link-type">
                                    <option></option>
                                    <option value="category">Categories</option>
                                    <option value="service">Services</option>
                                    <option value="customLink">Custom Link</option>
                                </select>
                                <div class="category" style="display:none">
                                    <select name="linked_item[]" class="form-control col-9 mt-2 linked-item category-option">
                                        <option></option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" data-type="category">{{ $category->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="service" style="display:none">
                                    <select name="linked_item[]" class="form-control col-9 mt-2 linked-item service-option">
                                        <option></option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" data-type="service">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="customLink" style="display:none">
                                    <input name="linked_item[]" type="text" class="form-control col-9 mt-2 linked-item customLink-option">
                                </div>
                            </div>
                        </div>
                    </td>
                <td>
                    <button type="button" class="btn btn-danger remove-image"><i class="fa fa-minus-circle"></i></button>
                </td>
            </tr>
        `);
    });

    $(document).on("click", ".remove-image", function() {
        var row = $(this).closest("tr");
        var imageFilename = row.data('image-filename');
        var id = row.data('id');

        // Make an AJAX call to remove the image from the database
        if(imageFilename) {
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
        }else{
            row.remove();
        }
    });

    $('#image').on('change', function(e) {
        var preview = $('#preview');
        preview.attr('src', URL.createObjectURL(e.target.files[0]));
    });

    $(document).on('change', '.link-type', function() {
        var selectedType = $(this).val();
        var row = $(this).closest('.form-group'); // Updated selector to find the closest form-group container
        row.find('.linked-item').val(null); // Clear previous selections

        if (selectedType === "category") {
            row.find('.service-option').prop('disabled', true); // Use prop instead of attr
            row.find('.customLink-option').prop('disabled', true); // Use prop instead of attr
            row.find('.category-option').prop('disabled', false); // Use prop instead of attr
            row.find('.category').show();
            row.find('.customLink').hide();
            row.find('.service').hide();
        } else if (selectedType === "service") {
            row.find('.category-option').prop('disabled', true); // Use prop instead of attr
            row.find('.customLink-option').prop('disabled', true); // Use prop instead of attr
            row.find('.service-option').prop('disabled', false); // Use prop instead of attr
            row.find('.service').show();
            row.find('.customLink').hide();
            row.find('.category').hide();
        } else if (selectedType === "customLink") {
            row.find('.category-option').prop('disabled', true); // Use prop instead of attr
            row.find('.customLink-option').prop('disabled', false); // Use prop instead of attr
            row.find('.service-option').prop('disabled', true); // Use prop instead of attr
            row.find('.customLink').show();
            row.find('.service').hide();
            row.find('.category').hide();
        }
    });

    $(document).on("change", ".image-input", function(e) {
        var preview = $(this).siblings('.image-preview')[0];
        preview.src = URL.createObjectURL(e.target.files[0]);
    });

    $(document).on('change', '.link-type', function() {
    var selectedType = $(this).val();
    var row = $(this).closest('tr');
    row.find('.linked-item').val(null); // Clear previous selections

    if (selectedType === "category") {
        row.find('.service-option').attr('disabled', 'true');
        row.find('.category-option').removeAttr('disabled');
        row.find('.category').show();
        row.find('.service').hide();
    } else if (selectedType === "service") {
        row.find('.category-option').attr('disabled', 'true');
        row.find('.service-option').removeAttr('disabled');
        row.find('.service').show();
        row.find('.category').hide();
    }
});
</script>
<script>
    $("#search-services").keyup(function() {
        let value = $(this).val().toLowerCase();

        $(".services-table tr").hide();

        $(".services-table tr").each(function() {
            let $row = $(this);

            let name = $row.find("td:nth-child(2)").text().toLowerCase();
            let price = $row.find("td:nth-child(3)").text().toLowerCase();
            let duration = $row.find("td:last").text().toLowerCase();

            if (name.indexOf(value) !== -1 || price.indexOf(value) !== -1 || duration.indexOf(value) !== -1) {
                $row.show();
            }
        });
    });

    $("#search-categories").keyup(function() {
        let value = $(this).val().toLowerCase();

        $(".categories-table tr").hide();

        $(".categories-table tr").each(function() {
            let $row = $(this);

            let name = $row.find("td:nth-child(2)").text().toLowerCase();

            if (name.indexOf(value) !== -1) {
                $row.show();
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        toggleLinkedItemSelect();

        $('.link-type').on('change', function () {
            toggleLinkedItemSelect();
        });

        function toggleLinkedItemSelect() {
            var linkType = $('.link-type').val();

            $('.linked-item').prop('disabled', true);

            if (linkType === '') {
                $('.category-option').prop('disabled', true);
                $('.service-option').prop('disabled', true);
            }
        }
    });
</script>
@endsection
