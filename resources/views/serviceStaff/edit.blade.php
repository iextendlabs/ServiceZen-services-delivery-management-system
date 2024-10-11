@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Edit Service Staff</h2>
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
    <form action="{{ route('serviceStaff.update',$serviceStaff->id) }}" method="POST" enctype="multipart/form-data">
        <input type="hidden" value="{{ $serviceStaff->staff->id ?? "" }}" name="staff_id">
        <input type="hidden" value="{{ $freelancer_join }}" name="freelancer_join" />
        @csrf
        @method('PUT')
        <input type="hidden" name="url" value="{{ url()->previous() }}">
        <ul class="nav nav-tabs" id="myTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a>
            </li>
            @if($socialLinks)
            <li class="nav-item">
                <a class="nav-link" id="social-links-tab" data-toggle="tab" href="#social-links" role="tab" aria-controls="social-links" aria-selected="false">Social Links</a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" id="gallery-tab" data-toggle="tab" href="#gallery" role="tab" aria-controls="gallery" aria-selected="false">Gallery</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="category-services-tab" data-toggle="tab" href="#category-services" role="tab" aria-controls="category-services" aria-selected="false">Categories & Services</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="document-tab" data-toggle="tab" href="#document" role="tab" aria-controls="document" aria-selected="false">Document</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabsContent">
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Name:</strong>
                            <input type="text" name="name" value="{{ old('name',$serviceStaff->name) }}" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Sub Title / Designation</strong>
                            <input type="text" name="sub_title" class="form-control" value="{{ old('sub_title', $serviceStaff->staff->sub_title ?? "") }}" placeholder="Sub Title">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Email:</strong>
                            <input type="email" name="email" value="{{ old('email',$serviceStaff->email) }}" class="form-control" placeholder="abc@gmail.com">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Phone Number:</strong>
                            <input id="number_country_code" type="hidden" name="number_country_code" />
                            <input type="tel" id="number" name="phone" value="{{ old('phone',$serviceStaff->staff->phone ?? "") }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Whatsapp Number:</strong>
                            <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                            <input type="tel" id="whatsapp" name="whatsapp" value="{{ old('whatsapp',$serviceStaff->staff->whatsapp ?? "") }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Status:</strong>
                            <select name="status" class="form-control">
                                <option value="1" 
                                    {{ (old('status') == '1' || ($serviceStaff->staff && $serviceStaff->staff->status == 1)) ? 'selected' : '' }}>
                                    Enable
                                </option>
                                <option value="0" 
                                    {{ (old('status') == '0' || ($serviceStaff->staff && $serviceStaff->staff->status == 0)) ? 'selected' : '' }}>
                                    Disable
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>About:</strong>
                            <textarea name="about" id="summernote" class="form-control">{{ old('about',$serviceStaff->staff->about ?? "") }}</textarea>
                            <script>
                                (function($) {
                                    $('#summernote').summernote({
                                        tabsize: 2,
                                        height: 250,
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
                            <strong for="image">Upload Image</strong>
                            <input type="file" name="image" class="form-control image-input" accept="image/*">
                            <img class="image-preview" src="/staff-images/{{$serviceStaff->staff->image ?? ""}}" height="130px">
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
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Drivers:</strong>
                            <select name="driver_id" class="form-control">
                                <option></option>
                                @foreach ($users as $driver)
                                @if($driver->hasRole("Driver"))
                                <option value="{{ $driver->id }}" @if($serviceStaff->staff && $serviceStaff->staff->driver_id == $driver->id) selected @endif>{{ $driver->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group scroll-div">
                            <strong>Supervisor:</strong>
                            <input type="text" name="search-supervisor" id="search-supervisor" class="form-control" placeholder="Search Supervisor By Name And Email">
                            <table class="table table-striped table-bordered supervisor-table">
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                                @foreach ($users as $user)
                                @if($user->hasRole("Supervisor"))
                                <tr>
                                    <td>
                                        <input type="checkbox" @if(in_array($user->id,old('ids',$supervisor_ids))) checked @endif name="ids[]" value="{{ $user->id }}">
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                @endif
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Commission:</strong>
                            <input type="number" name="commission" value="{{ old('commission',$serviceStaff->staff->commission ?? "") }}" class="form-control" placeholder="Commission In %">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Additional Charges:</strong>
                            <input type="number" name="charges" value="{{ old('charges',$serviceStaff->staff->charges ?? "") }}" class="form-control" placeholder="Additional Charges">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Commission Salary:</strong>
                            <input type="number" name="fix_salary" class="form-control" value="{{ old('fix_salary',$serviceStaff->staff->fix_salary ?? "") }}" placeholder="Commission Salary">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Minmum Order Value:</strong>
                            <input type="number" name="min_order_value" class="form-control" value="{{ old('min_order_value',$serviceStaff->staff->min_order_value ?? "") }}" placeholder="Minmum Order Value">
                        </div>
                    </div>
                    @if($freelancer_join)
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Membership Plan:</strong>
                            <select name="membership_plan_id" class="form-control">
                                <option value=""></option>
                                @foreach ($membership_plans as $membership_plan)
                                    <option value="{{ $membership_plan->id }}" 
                                        {{ (old('membership_plan_id') == $membership_plan->id || 
                                        ($serviceStaff->staff && $serviceStaff->staff->membership_plan_id == $membership_plan->id)) ? 'selected' : '' }}>
                                        {{ $membership_plan->plan_name }} (AED{{ $membership_plan->membership_fee }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Expiry Date:</strong>
                            <input type="date" name="expiry_date" class="form-control" min="{{ date('Y-m-d') }}" value={{ $serviceStaff->staff->expiry_date ?? "" }}>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Affiliate:</strong>
                            <select name="affiliate_id" class="form-control">
                                <option value=""></option>
                                @foreach ($affiliates as $affiliate)
                                    @if($affiliate->affiliate->status == 1)
                                    <option value="{{ $affiliate->id }}" 
                                        {{ (old('affiliate_id') == $affiliate->id || 
                                           ($serviceStaff->staff && $serviceStaff->staff->affiliate_id == $affiliate->id)) ? 'selected' : '' }}>
                                        {{ $affiliate->name }}
                                    </option>                                    
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @if($socialLinks)
            <div class="tab-pane fade" id="social-links" role="tabpanel" aria-labelledby="social-links-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Instagram <i class="fa fa-instagram"></i>:</strong>
                            <input type="text" name="instagram" class="form-control" placeholder="Instagram" value="{{ old('instagram',$serviceStaff->staff->instagram ?? "") }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Snapchat:</strong>
                            <input type="text" name="snapchat" class="form-control" placeholder="Snapchat" value="{{ old('snapchat',$serviceStaff->staff->snapchat ?? "") }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Facebook:</strong>
                            <input type="text" name="facebook" class="form-control" placeholder="Facebook" value="{{ old('facebook', $serviceStaff->staff->facebook ?? "") }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Youtube:</strong>
                            <input type="text" name="youtube" class="form-control" placeholder="Youtube" value="{{ old('youtube',$serviceStaff->staff->youtube ?? "") }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Tiktok:</strong>
                            <input type="text" name="tiktok" class="form-control" placeholder="Tiktok" value="{{ old('tiktok',$serviceStaff->staff->tiktok ?? "") }}">
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="tab-pane fade" id="gallery" role="tabpanel" aria-labelledby="gallery-tab">
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
            <div class="tab-pane fade" id="category-services" role="tabpanel" aria-labelledby="category-services-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group scroll-div">
                            <span style="color: red;">*</span><strong>Category:</strong>
                            <input type="text" name="search-category" id="search-category" class="form-control" placeholder="Search category By Name, Price And Duration">
                            <table class="table table-striped table-bordered category-table">
                                <tr>
                                    <th></th>
                                    <th>Title</th>
                                </tr>
                                <tr>
                                    <td>

                                        <input type="checkbox" class="category-checkbox" name="category" value="all">
                                    </td>
                                    <td>All</td>
                                </tr>
                                @foreach ($categories as $category)
                                <tr>
                                    <td>

                                        <input type="checkbox" class="category-checkbox" name="category_ids[]" value="{{ $category->id }}" @if(in_array($category->id,old('category_ids',$category_ids))) checked @endif>
                                    </td>
                                    <td>{{ $category->title }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group scroll-div">
                            <span style="color: red;">*</span><strong>Services:</strong>
                            <input type="text" name="search-services" id="search-services" class="form-control" placeholder="Search Services By Name, Price And Duration">
                            <table class="table table-striped table-bordered services-table">
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Duration</th>
                                </tr>
                                <tr>
                                    <td>

                                        <input type="checkbox" class="service-checkbox" name="service" value="all">
                                    </td>
                                    <td>All</td>
                                </tr>
                                @foreach ($services as $service)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="service-checkbox" name="service_ids[]" value="{{ $service->id }}" data-category="{{ $service->category_id }}" @if(in_array($service->id,old('service_ids',$service_ids))) checked @endif>
                                    </td>
                                    <td>{{ $service->name }}</td>

                                    <td>{{ isset($service->discount) ? 
                                    $service->discount : $service->price }}</td>
                                    <td>{{ $service->duration }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="document" role="tabpanel" aria-labelledby="document-tab">
                <div class="row">
                    @foreach($documents as $field => $label)
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>{{ $label }}:</strong>
                                <input type="file" name="{{ $field }}" class="form-control document-upload" data-field="{{ $field }}">
                                @if($serviceStaff->document && $serviceStaff->document->$field)
                                <p>Current File: <a href="{{ asset('staff-document/' .$serviceStaff->document->$field) }}" target="_blank">{{ $serviceStaff->document->$field }}</a></p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-12 text-center mt-3">
                <button type="submit" class="btn btn-block btn-primary">Save</button>
            </div>
        </div>
    </form>
</div>
<script>
    $("#search-services").keyup(function() {
        let value = $(this).val().toLowerCase();

        $(".services-table tr").hide();

        $(".services-table tr").each(function() {
            let $row = $(this);

            let name = $row.find("td:nth-child(2)").text().toLowerCase();

            if (name.indexOf(value) !== -1) {
                $row.show();
            }
        });
    });
    $("#search-category").keyup(function() {
        let value = $(this).val().toLowerCase();

        $(".category-table tr").hide();

        $(".category-table tr").each(function() {
            let $row = $(this);

            let title = $row.find("td:nth-child(2)").text().toLowerCase();

            if (title.indexOf(value) !== -1) {
                $row.show();
            }
        });
    });

    $('.category-checkbox').click(function() {
        var categoryId = $(this).val();

        if (categoryId === 'all') {
            var allCheckboxState = $(this).prop('checked');
            $('.category-checkbox').prop('checked', allCheckboxState);
        }
    });

    $('.service-checkbox').click(function() {
        var serviceId = $(this).val();

        if (serviceId === 'all') {
            var allCheckboxState = $(this).prop('checked');
            $('.service-checkbox').prop('checked', allCheckboxState);
        }
    });
</script>
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
<script>
    $(document).ready(function() {
        $("#search-supervisor").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".supervisor-table tr").hide();

            $(".supervisor-table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();

                var email = $row.find("td:last").text().toLowerCase();

                if (name.indexOf(value) != -1) {
                    $(this).show();
                } else if (email.indexOf(value) != -1) {
                    $(this).show();
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('.document-upload').on('change', function() {
            var field = $(this).data('field');
            var formData = new FormData();
            formData.append(field, this.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route("serviceStaff.upload.document", $serviceStaff->id) }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    alert('File uploaded successfully');
                    // Optionally, update the UI with the new file info
                },
                error: function(xhr) {
                    alert('An error occurred while uploading the file');
                }
            });
        });
    });
</script>
@endsection