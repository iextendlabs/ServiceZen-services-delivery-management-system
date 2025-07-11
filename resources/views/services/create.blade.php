@extends('layouts.app')
<style>
    .img-preview {
        max-width: 100%;
        height: auto;
        max-height: 200px;
    }
</style>    
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6 margin-tb">
            <h2>Add New Service</h2>
        </div>
        <div class="col-md-6">
            <button type="submit" form="services-form" class="btn btn-primary float-end">Save</button>
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
    <form action="{{ route('services.store') }}" method="POST" id="services-form" enctype="multipart/form-data">
        @csrf
        <ul class="nav nav-tabs" id="myTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="seo-tab" data-toggle="tab" href="#seo" role="tab" aria-controls="seo" aria-selected="false">SEO</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="package-services-tab" data-toggle="tab" href="#package-services" role="tab" aria-controls="package-services" aria-selected="false">Package Services</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="add-ons-tab" data-toggle="tab" href="#add-ons" role="tab" aria-controls="add-ons" aria-selected="false">Add ONs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="variant-tab" data-toggle="tab" href="#variant" role="tab" aria-controls="variant" aria-selected="false">Variant Services</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="options-tab" data-toggle="tab" href="#options" role="tab" aria-controls="options" aria-selected="false">Price Options</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="additionalImages-tab" data-toggle="tab" href="#additionalImages" role="tab" aria-controls="additionalImages" aria-selected="false">Additional Images</a>
            </li>
        </ul>
        <div class="tab-content mt-2" id="myTabsContent">
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Name:</strong>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong for="image">Upload Image</strong>
                            <p class="text-danger"><strong>Note: </strong>Upload image with dimensions 1005 x 600px Thank you!</p>
                            <input type="file" name="image" id="image" class="form-control-file ">
                            <br>
                            <img id="preview" src="/service-images/" height="130px">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Status:</strong>
                            <select name="status" class="form-control">
                                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Enable</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Disable</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Feature Service:</strong>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="feature" id="feature" value="1" {{ old('feature') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="feature">Enable featured service</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Quote:</strong>
                            <select name="quote" class="form-control">
                                <option value="1" {{ old('quote') == '1' ? 'selected' : '' }}>Enable</option>
                                <option value="0" {{ old('quote') == '0' ? 'selected' : '' }}>Disable</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Pipeline Id:</strong>
                            <input type="number" value="{{ old('pipelineId') }}" name="pipelineId" class="form-control" placeholder="Pipeline Id">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Description:</strong>
                            <textarea class="form-control" id="description_summernote" name="description" placeholder="Description">{{ old('description') }}</textarea>
                            <script>
                                (function($) {
                                    $('#description_summernote').summernote({
                                        tabsize: 2,
                                        height: 250,
                                        toolbar: [
                                            ['style', ['style']],
                                            ['font', ['bold', 'italic', 'underline', 'clear']],
                                            ['fontname', ['fontname']],
                                            ['fontsize', ['fontsize']],
                                            ['color', ['color']],
                                            ['para', ['ul', 'ol', 'paragraph']],
                                            ['height', ['height']],
                                            ['insert', ['picture', 'link', 'video', 'table']],
                                            ['misc', ['undo', 'redo']], 
                                            ['view', ['fullscreen', 'codeview', 'help']]
                                        ],
                                        popover: {
                                            image: [
                                                ['custom', ['imageAttributes']],
                                                ['resize', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
                                                ['float', ['floatLeft', 'floatRight', 'floatNone']],
                                                ['remove', ['removeMedia']]
                                            ]
                                        },
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
                                                $('#description_summernote').summernote('insertImage', response.url);
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
                            <strong>Short Description:</strong>
                            <textarea class="form-control" style="height:150px" id="short_description_summernote" name="short_description" placeholder="Short Description">{{old('short_description') }}</textarea>
                            <script>
                                (function($) {
                                    $('#short_description_summernote').summernote({
                                        tabsize: 2,
                                        height: 250,
                                        toolbar: [
                                            ['style', ['style']],
                                            ['font', ['bold', 'italic', 'underline', 'clear']],
                                            ['fontname', ['fontname']],
                                            ['fontsize', ['fontsize']],
                                            ['color', ['color']],
                                            ['para', ['ul', 'ol', 'paragraph']],
                                            ['height', ['height']],
                                            ['insert', ['picture', 'link', 'video', 'table']],
                                            ['misc', ['undo', 'redo']], 
                                            ['view', ['fullscreen', 'codeview', 'help']]
                                        ],
                                        popover: {
                                            image: [
                                                ['custom', ['imageAttributes']],
                                                ['resize', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
                                                ['float', ['floatLeft', 'floatRight', 'floatNone']],
                                                ['remove', ['removeMedia']]
                                            ]
                                        },
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
                                                $('#short_description_summernote').summernote('insertImage', response.url);
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
                            <span style="color: red;">*</span><strong>Price:</strong>
                            <input type="number" value="{{ old('price') }}" name="price" class="form-control" placeholder="Price">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Discount Price:</strong>
                            <input type="number" value="{{ old('discount') }}" name="discount" class="form-control" placeholder="Discount Price">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Duration:</strong>
                            <input type="text" value="{{ old('duration') }}" name="duration" class="form-control" placeholder="Duration">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group scroll-div">
                            <span style="color: red;">*</span><strong>Category:</strong>
                            <input type="text" name="categories-search" id="categories-search" class="form-control" placeholder="Search Category By Name">
                            <table class="table table-striped table-bordered categories-table">
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                </tr>
                                @foreach ($service_categories as $category)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="categoriesId[{{ ++$i }}]" value="{{ $category->id }}" {{ in_array($category->id, old('categoriesId', [])) ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ $category->title }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <!-- <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Category:</strong>
                            <select name="category_id" class="form-control">
                                <option></option>
                                @foreach($service_categories as $category)
                                <option value="{{$category->id}}">{{$category->title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> -->
                </div>
            </div>
            <div class="tab-pane fade show" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="slug"><span style="color: red;">*</span><strong>SEO URL (Slug)</strong></label>
                            <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug') }}">
                            <small class="text-muted">
                                • Should be lowercase with hyphens instead of spaces (e.g., "my-service")<br>
                                • Avoid special characters and punctuation<br>
                                • Should be unique across all services
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="meta_title"><span style="color: red;">*</span><strong>Meta Title</strong></label>
                            <input type="text" name="meta_title" id="meta_title" class="form-control" value="{{ old('meta_title') }}" maxlength="60">
                            <small class="text-muted">
                                • Recommended: 50-60 characters
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="meta_description"><strong>Meta Description</strong></label>
                            <textarea name="meta_description" id="meta_description" class="form-control" rows="4" maxlength="160">{{ old('meta_description') }}</textarea>
                            <small class="text-muted">
                                • Recommended: 150-160 characters
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="meta_keywords"><strong>Meta Keywords</strong> (comma separated)</label>
                            <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" value="{{ old('meta_keywords') }}" placeholder="keyword1, keyword2, keyword3">
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="package-services" role="tabpanel" aria-labelledby="package-services-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group scroll-div">
                            <strong>Package Services:</strong>
                            <input type="text" name="package-search" id="package-search" class="form-control" placeholder="Search Services By Name And Price">
                            <table class="table table-striped table-bordered services-table">
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Price</th>
                                </tr>
                                @foreach ($all_services as $service)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="packageId[{{ ++$i }}]" value="{{ $service->id }}" {{ in_array($service->id, old('packageId', [])) ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ $service->name }}</td>
                                    <td>{{ $service->price }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="add-ons" role="tabpanel" aria-labelledby="add-ons-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group scroll-div">
                            <strong>Add ONs Services:</strong>
                            <input type="text" name="add-ons-search" id="add-ons-search" class="form-control" placeholder="Search Services By Name And Price">
                            <table class="table table-striped table-bordered add-ons-table">
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Price</th>
                                </tr>
                                @foreach ($all_services as $service)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="addONsId[{{ ++$i }}]" value="{{ $service->id }}" {{ in_array($service->id, old('addONsId', [])) ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ $service->name }}</td>
                                    <td>{{ $service->price }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="variant" role="tabpanel" aria-labelledby="variant-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Note: Save the service before proceeding with variants.</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="options" role="tabpanel" aria-labelledby="options-tab">
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Service Options</strong>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table id="optionTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Option Name</th>
                                    <th>Option Price (AED)</th>
                                    <th>Option Duration</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <button id="addOptionBtn" onclick="addOptionrow();" type="button" class="btn btn-primary float-right"><i class="fa fa-plus-circle"></i></button>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="additionalImages" role="tabpanel" aria-labelledby="additionalImages-tab">
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Additional Images</strong>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div id="image-upload-wrapper">
                            <button type="button" id="add-image-btn" class="btn btn-primary mt-3 mb-4">Add Image</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Note:</strong>
                        <textarea class="form-control" style="height:150px" name="note" placeholder="Note">{{old('note') }}</textarea>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Select User For Note:</strong>
                        <input type="text" name="search_user" id="search-user" class="form-control" placeholder="Search User By Name And Price">
                        <table class="table table-striped table-bordered user-table">
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Email</th>
                            </tr>
                            @foreach ($users as $user)
                            @if($user->hasRole("Customer"))
                            <tr>
                                <td>
                                    @if(isset($userNote))
                                    @if(in_array($user->id,unserialize($userNote->user_ids)))
                                        <input type="checkbox" checked name="userIds[{{ ++$i }}]" value="{{ $user->id }}">
                                        @else
                                        <input type="checkbox" name="userIds[{{ ++$i }}]" value="{{ $user->id }}">
                                        @endif
                                    @else
                                    <input type="checkbox" name="userIds[{{ ++$i }}]" value="{{ $user->id }}">
                                    @endif
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            @endif
                            @endforeach
                        </table>
                    </div>
                </div>
            </div> --}}
            <div class="col-md-12 text-center mt-4">
                <button type="submit" class="btn btn-block btn-primary">Save</button>
            </div>
        </div>
    </form>
</div>
<script>
    $(document).ready(function(){
        let imageIndex = 1;

        $('#add-image-btn').click(function(){
            $(`
                <div class="form-group image-upload-group p-3 border border-secondary rounded">
                    <div class="d-flex align-items-center justify-content-between">
                        <input type="file" name="images[]" id="serviceImage${imageIndex}" class="form-control-file" onchange="previewImage(this, ${imageIndex})" accept="image/*">
                        <button type="button" class="btn btn-danger mt-3 remove-image-btn">Remove</button>
                    </div>
                    <img id="previewServiceImage${imageIndex}" src="#" alt="Image Preview" class="img-preview mt-3" style="display:none;">
                </div>
            `).insertBefore('#add-image-btn');
            imageIndex++;
        });

        $(document).on('click', '.remove-image-btn', function(){
            $(this).closest('.image-upload-group').remove();
        });
    });

    function previewImage(input, index = 0) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(`#previewServiceImage${index}`).attr('src', e.target.result).show();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
<script>
    var option_row = 1;
    function addOptionrow(){
        var newRow = `
            <tr>
                <td>
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="text" required name="option_name[${option_row}]" class="form-control" placeholder="Option Name">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="number" required name="option_price[${option_row}]" class="form-control" placeholder="Option Price">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="text" name="option_duration[${option_row}]" class="form-control" placeholder="Option Duration">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="file" name="option_image[${option_row}]" class="form-control option-image-input" accept="image/*" data-preview="image-preview-${option_row}">
                            <img id="image-preview-${option_row}" src="" alt="Preview" height="130px" style="display: none; margin-top: 5px;">
                        </div>
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove-option"><i class="fa fa-minus-circle"></i></button>
                </td>
            </tr>
        `;
        $('#optionTable tbody').append(newRow);
        option_row++
    }

    $(document).on("change", ".option-image-input", function () {
        let input = $(this)[0];
        let previewId = $(this).data("preview");
        let preview = $("#" + previewId);

        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                preview.attr("src", e.target.result).show();
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.hide();
        }
    });
    
    $(document).on('click', '.remove-option', function() {
        $(this).closest('tr').remove();
    });
</script>
<script>
    $(document).ready(function() {
        $("#categories-search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".categories-table tr").hide();

            $(".categories-table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();


                if (name.indexOf(value) != -1) {
                    $(this).show();
                }
            });
        });

        $("#package-search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".services-table tr").hide();

            $(".services-table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();

                var price = $row.find("td:last").text().toLowerCase();

                if (name.indexOf(value) != -1) {
                    $(this).show();
                } else if (price.indexOf(value) != -1) {
                    $(this).show();
                }
            });
        });

        $("#add-ons-search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".add-ons-table tr").hide();

            $(".add-ons-table tr").each(function() {

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

        $("#search-user").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".user-table tr").hide();

            $(".user-table tr").each(function() {

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
    document.getElementById('image').addEventListener('change', function(e) {
        var preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(e.target.files[0]);
    });
</script>
@endsection