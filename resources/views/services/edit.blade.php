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
    <div class="card shadow-sm mb-4">
        <div class="card-body">
<div class="row">
    <div class="col-md-12">
        <div class="float-left">
            <h2>Update Service</h2>
        </div>
        <div class="float-right">
            <button type="submit" form="services-form" class="btn btn-primary float-end">Update</button>
            <a class="btn btn-warning mr-2" href="/service/{{ $service->slug }}">Store View</a>

        </div>
    </div>
    <!-- TODO Create and edit form in single form -->
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
<form action="{{ route('services.update',$service->id) }}" id="services-form" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="url" value="{{ url()->previous() }}">

    <ul class="nav nav-tabs" id="myTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="seo-tab" data-bs-toggle="tab" href="#seo" role="tab" aria-controls="seo" aria-selected="false">SEO</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="package-services-tab" data-bs-toggle="tab" href="#package-services" role="tab" aria-controls="package-services" aria-selected="false">Package Services</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="add-ons-tab" data-bs-toggle="tab" href="#add-ons" role="tab" aria-controls="add-ons" aria-selected="false">Add ONs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="variant-tab" data-bs-toggle="tab" href="#variant" role="tab" aria-controls="variant" aria-selected="false">Variant Services</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="options-tab" data-bs-toggle="tab" href="#options" role="tab" aria-controls="options" aria-selected="false">Price Options</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="specifications-tab" data-bs-toggle="tab" href="#specifications" role="tab" aria-controls="options" aria-selected="false">Service Attribute / Specifications</a>
        </li> 
        <li class="nav-item">
            <a class="nav-link" id="additionalImages-tab" data-bs-toggle="tab" href="#additionalImages" role="tab" aria-controls="additionalImages" aria-selected="false">Additional Images</a>
        </li>
    </ul>
    <div class="tab-content mt-2" id="myTabsContent">
        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="mb-2">
                        <label class="form-label"><span class="text-danger">*</span> Name</label>
                        <input type="text" name="name" value="{{ old('name',$service->name) }}" class="form-control rounded-3" placeholder="Name">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-2">
                        <label class="form-label"><span class="text-danger">*</span> Upload Image <small class="text-muted">(1005 x 600px)</small></label>
                        <input type="file" name="image" id="image" class="form-control">
                        <div class="mt-2">
                            <img id="preview" src="/service-images/{{$service->image}}" class="img-fluid rounded" style="max-height:140px">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select rounded-3">
                            <option value="1"  {{ old('status', $service->status) == '1' ? 'selected' : '' }}>Enable</option>
                            <option value="0" {{ old('status', $service->status) == '0' ? 'selected' : '' }}>Disable</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="form-check form-switch">
                        <input type="hidden" name="feature" value="0">
                        <input class="form-check-input" type="checkbox" name="feature" id="feature" value="1" {{ old('feature', $service->feature) == '1' ? 'checked' : '' }}>
                        <label class="form-check-label ms-2" for="feature">Enable featured service</label>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-2">
                        <label class="form-label">Quote</label>
                        <select name="quote" class="form-select rounded-3">
                            <option value="1"  {{ old('quote', $service->quote) == '1' ? 'selected' : '' }}>Enable</option>
                            <option value="0" {{ old('quote', $service->quote) == '0' ? 'selected' : '' }}>Disable</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-2">
                        <label class="form-label">Pipeline Id</label>
                        <input type="number" value="{{old('pipelineId',$service->pipelineId)}}" name="pipelineId" class="form-control rounded-3" placeholder="Pipeline Id">
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-2">
                        <label class="form-label">Description</label>
                        <textarea class="form-control rounded-3" id="description_summernote" name="description" placeholder="Description">{{old('description',$service->description)}}</textarea>
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
                <div class="col-12">
                    <div class="mb-2">
                        <label class="form-label">Short Description</label>
                        <textarea class="form-control rounded-3" style="height:150px" id="short_description_summernote" name="short_description" placeholder="Short Description">{{old('short_description',$service->short_description)}}</textarea>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-2">
                        <label class="form-label"><span class="text-danger">*</span> Price</label>
                        <input type="number" value="{{ old('price',$service->price) }}" name="price" class="form-control rounded-3" placeholder="Price">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-2">
                        <label class="form-label">Discount Price</label>
                        <input type="number" value="{{ old('discount', $service->discount) }}" name="discount" class="form-control rounded-3" placeholder="Discount Price">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-2">
                        <label class="form-label">Duration</label>
                        <input type="text" value="{{old('duration',$service->duration)}}" name="duration" class="form-control rounded-3" placeholder="Duration">
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-2">
                        <label class="form-label"><span class="text-danger">*</span> Category</label>
                        <input type="text" name="categories-search" id="categories-search" class="form-control rounded-3" placeholder="Search Category By Name">
                        <div class="table-responsive mt-2">
                            <table class="table table-striped table-bordered categories-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:48px"></th>
                                        <th>Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($service_categories as $category)
                                    <tr>
                                        <td>
                                            <input type="checkbox" 
                                                   name="categoriesId[{{ $i++ }}]" 
                                                   value="{{ $category->id }}" 
                                                   {{ in_array($category->id, old('categoriesId', $category_ids)) ? 'checked' : '' }}>
                                        </td>
                                        <td>{{ $category->title }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Category:</strong>
                        <select name="category_id" class="form-control">
                            <option></option>
                            @foreach($service_categories as $category)
                            @if($category->id == $service->category_id)
                            <option value="{{$category->id}}" selected>{{$category->title}}</option>
                            @else
                            <option value="{{$category->id}}">{{$category->title}}</option>
                            @endif
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
                        <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $service->slug ?? '') }}">
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
                        <input type="text" name="meta_title" id="meta_title" class="form-control" value="{{ old('meta_title', $service->meta_title ?? '') }}" maxlength="60">
                        <small class="text-muted">
                            • Recommended: 50-60 characters
                        </small>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="meta_description"><strong>Meta Description</strong></label>
                        <textarea name="meta_description" id="meta_description" class="form-control" rows="4" maxlength="160">{{ old('meta_description', $service->meta_description ?? '') }}</textarea>
                        <small class="text-muted">
                            • Recommended: 150-160 characters
                        </small>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="meta_keywords"><strong>Meta Keywords</strong> (comma separated)</label>
                        <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" value="{{ old('meta_keywords', $service->meta_keywords ?? '') }}" placeholder="keyword1, keyword2, keyword3">
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="package-services" role="tabpanel" aria-labelledby="package-services-tab">
            <div class="row">
                <div class="col-md-12 scroll-div">
                    <div class="form-group">
                        <strong>Package Services:</strong>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Search Services By Name And Price">
                        <table class="table table-striped table-bordered services-table">
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Price</th>
                            </tr>
                            @foreach ($all_services as $single_service)
                            <tr>
                                <td>
                                    <input type="checkbox" name="packageId[{{ ++$i }}]"  {{ in_array($single_service->id, old('packageId', $package_services)) ? 'checked' : '' }} value="{{ $single_service->id }}">
                                </td>
                                <td>{{ $single_service->name }}</td>
                                <td>{{ $single_service->price }}</td>

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
                    <div class="form-group">
                        <strong>Add ONs Services:</strong>
                        <input type="text" name="add-ons-search" id="add-ons-search" class="form-control" placeholder="Search Services By Name And Price">
                        <table class="table table-striped table-bordered add-ons-table">
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Price</th>
                            </tr>
                            @foreach ($all_services as $single_service)
                            <tr>
                                <td>
                                    <input type="checkbox" name="addONsId[{{ ++$i }}]"  {{ in_array($single_service->id, old('addONsId', $add_on_services)) ? 'checked' : '' }} value="{{ $single_service->id }}">
                                </td>
                                <td>{{ $single_service->name }}</td>
                                <td>{{ $single_service->price }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="variant" role="tabpanel" aria-labelledby="variant-tab">
            <div class="row">
                <div class="col-md-8">
                    <input type="hidden" name="id" value="{{ $service->id }}" class="form-control">

                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>New Variant:</strong>
                            <input type="text" name="new_variant" class="form-control" value="{{ old('new_variant') }}" placeholder="New Variant">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Price:</strong>
                            <input type="number" name="new_variant_price" value="{{ old('new_variant_price') }}" class="form-control" placeholder="Price">
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        <button type="button" id="bulkCopyBtn" class="btn btn-secondary">Add New Variant</button>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group scroll-div">
                        <strong>Variant Services:</strong>
                        <input type="text" name="variant-search" id="variant-search" class="form-control" placeholder="Search Services By Name And Price">
                        <table class="table table-striped table-bordered variant-table">
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                            @foreach ($all_services as $single_service)
                            <tr>
                                <td>
                                    <input type="checkbox" name="variantId[{{ ++$i }}]" value="{{ $single_service->id }}" {{ in_array($single_service->id, old('variantId', $variant_services)) ? 'checked' : '' }}>
                                </td>
                                <td>{{ $single_service->name }}</td>
                                <td>{{ $single_service->price }}</td>
                                <td class="text-right">
                                    <a href="{{ route('service.delete',$single_service->id) }}" class="btn btn-danger">Delete</a>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="options" role="tabpanel" aria-labelledby="options-tab">
            <div class="row mt-3">
                @php
                    $option_row = 0;
                @endphp
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
                                <th width="350">Short Description</th>
                                <th>Option Price (AED)</th>
                                <th>Option Duration</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($service->serviceOption))
                                @foreach ($service->serviceOption as $option_row => $option)
                                    @php
                                        $option_row = $option_row;
                                    @endphp
                                    <tr>
                                    <input type="hidden" name="option_id[{{ $option_row }}]" value="{{ $option->id ?? '' }}">
                                        <td>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <input type="text" required name="option_name[{{ $option_row }}]" class="form-control" value="{{ old('option_name.'.$option_row, $option->option_name) }}"  placeholder="Option Name">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <textarea name="option_description[{{ $option_row }}]" class="form-control" placeholder="Description" maxlength="100" rows="4" oninput="optionDescriptionLengthCheck(this)">{{ old('option_description.'.$option_row, $option->description ?? '') }}</textarea>
                                                    <small class="text-muted"><span class="desc-count">0</span>/100 characters</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <input type="number" required name="option_price[{{ $option_row }}]" class="form-control"  value="{{ old('option_price.'.$option_row, $option->option_price) }}"  placeholder="Option Price">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <input type="text" name="option_duration[{{ $option_row }}]" class="form-control"  value="{{ old('option_duration.'.$option_row, $option->option_duration) }}"  placeholder="Option Duration">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <input type="file" name="option_image[{{ $option_row }}]" class="form-control option-image-input" accept="image/*" data-preview="image-preview-{{ $option_row }}">
                                                    <img id="image-preview-{{ $option_row }}" 
                                                        src="{{ $option->image ? asset('service-images/options/' . $option->image) : '' }}" 
                                                        alt="Preview" 
                                                        height="130px" 
                                                        style="{{ $option->image ? '' : 'display: none;' }} margin-top: 5px;">
                                                </div>
                                                @if ($option->image)
                                                    <div class="form-group">
                                                        <label>
                                                            <input type="checkbox" name="remove_option_image[{{ $option_row }}]" value="1"> Remove Image
                                                        </label>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger remove-option"><i class="fa fa-minus-circle"></i></button>
                                        </td>
                                    </tr>
                                    @php
                                        $option_row++;
                                    @endphp 
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    <button id="addOptionBtn" onclick="addOptionrow();" type="button" class="btn btn-primary float-right"><i class="fa fa-plus-circle"></i></button>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="specifications" role="tabpanel" aria-labelledby="specifications-tab">
            <h5 class="mb-3">Product Specifications</h5>
            <div id="specifications-wrapper">
                @if($service->specifications && count($service->specifications))
                    @foreach($service->specifications as $index => $spec)
                        <div class="form-row align-items-center specification-row p-2">
                            <div class="col-md-5 mb-2 mb-md-0">
                                <input type="text" name="specifications[{{ $index }}][title]" class="form-control" placeholder="Specification Title" value="{{ $spec->title }}">
                            </div>
                            <div class="col-md-6 mb-2 mb-md-0">
                                <input type="text" name="specifications[{{ $index }}][value]" class="form-control" placeholder="Specification Value" value="{{ $spec->value }}">
                            </div>
                            <div class="col-md-1 text-md-right">
                                <button type="button" class="btn btn-danger btn-sm remove-spec">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="form-row align-items-center specification-row p-2">
                        <div class="col-md-5 mb-2 mb-md-0">
                            <input type="text" name="specifications[0][title]" class="form-control" placeholder="Specification Title">
                        </div>
                        <div class="col-md-6 mb-2 mb-md-0">
                            <input type="text" name="specifications[0][value]" class="form-control" placeholder="Specification Value">
                        </div>
                        <div class="col-md-1 text-md-right">
                            <button type="button" class="btn btn-danger btn-sm remove-spec">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
            <div class="d-flex justify-content-end">
                <button type="button" id="add-spec" class="btn btn-success btn-sm mt-2">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="tab-pane fade" id="additionalImages" role="tabpanel" aria-labelledby="additionalImages-tab">
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Additional Images</strong>
                            <p class="text-danger"><strong>Note: </strong>Upload image with dimensions 1080 x 1080px Thank you!</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <div id="image-upload-wrapper">
                                @foreach($service->images as $index => $image)
                                <div class="form-group image-upload-group p-3 border border-secondary rounded" id="image-group-{{ $image->id }}">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <img id="previewServiceImage{{ $index }}" src="{{ asset('service-images/additional/' . $image->image) }}" alt="Image Preview" class="img-preview mt-3">
                                        <button type="button" class="btn btn-danger mt-3 remove-existing-image-btn" data-image-id="{{ $image->id }}">Remove</button>
                                    </div>
                                </div>
                                @endforeach
                                <button type="button" id="add-image-btn" class="btn btn-primary mt-3 mb-4">Add Image</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {{-- <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Note:</strong>
                    <textarea class="form-control" style="height:150px" name="note" placeholder="Note">@if(isset($userNote)){{$userNote->note}}@endif</textarea>
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
            <button type="submit" class="btn btn-block btn-primary">Update</button>
        </div>
    </div>
</form>
</div>
<script>
    $(document).ready(function(){
        let imageIndex = {{ $service->images->count() }};

        $('#add-image-btn').click(function(){
            $(`<div class="form-group image-upload-group p-3 border border-secondary rounded">
                    <div class="d-flex align-items-center justify-content-between">
                        <input type="file" name="images[]" id="serviceImage${imageIndex}" class="form-control-file" onchange="previewImage(this, ${imageIndex})" accept="image/*">
                        <button type="button" class="btn btn-danger mt-3 remove-image-btn">Remove</button>
                    </div>
                    <img id="previewServiceImage${imageIndex}" src="#" alt="Image Preview" class="img-preview mt-3" style="display:none;">
                </div>`).insertBefore('#add-image-btn');
            imageIndex++;
        });

        // Remove an image that's already stored in the database
        $(document).on('click', '.remove-existing-image-btn', function(){
            let imageId = $(this).data('image-id');
            $(`#image-group-${imageId}`).remove(); // Remove the image preview and input group
            $('#image-upload-wrapper').append(`<input type="hidden" name="remove_images[]" value="${imageId}">`);
        });

        // Remove an image that has not been uploaded yet
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
    var option_row = {{ $option_row}};
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
                            <textarea name="option_description[${option_row}]" class="form-control" placeholder="Description" maxlength="100" rows="4" oninput="optionDescriptionLengthCheck(this)"></textarea>
                            <small class="text-muted"><span class="desc-count">0</span>/100 characters</small>
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

    function optionDescriptionLengthCheck(textarea) {
        var count = textarea.value.length;
        $(textarea).siblings('small').find('.desc-count').text(count);
    }

    $(document).ready(function() {
        $('textarea[name^="option_description"]').each(function() {
            optionDescriptionLengthCheck(this);
        });
    });

    $(document).on("change", ".option-image-input", function () {
        let input = this;
        let previewId = $(this).attr("data-preview"); // Get correct preview ID
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
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('bulkCopyBtn').addEventListener('click', function() {
            const newVariant = document.querySelector('input[name="new_variant"]').value; // Get the new_variant value
            const serviceId = document.querySelector('input[name="id"]').value; // Get the id value
            const price = document.querySelector('input[name="new_variant_price"]').value; // Get the id value

            if (newVariant && serviceId && price) {
                if (confirm('Are you sure you want to create new variant?')) {
                    copySelectedItems(newVariant, serviceId, price);
                }
            } else {
                alert('Please Set New Variant Name and Price.');
            }
        });

        function copySelectedItems(newVariant, serviceId, price) {
            fetch('{{ route('services.bulkCopy') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            newVariant,
                            serviceId,
                            price
                        })
                    })
                .then(response => response.json())
                .then(data => {
                    var editUrl = "{{ route('services.edit', ['service' => ':id']) }}";
                    editUrl = editUrl.replace(':id', data.service_id);

                    window.location.href = editUrl;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
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
        
        $("#search").keyup(function() {
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
        $("#variant-search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".variant-table tr").hide();

            $(".variant-table tr").each(function() {

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

    });

    $(document).ready(function() {
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
<script>
$(document).ready(function() {
    let specIndex = {{ $service->specifications && $service->specifications->count() > 0 ? $service->specifications->count() : 1 }};

    $('#add-spec').click(function() {
        let newRow = `
            <div class="form-row align-items-center specification-row p-2">
                <div class="col-md-5 mb-2 mb-md-0">
                    <input type="text" name="specifications[${specIndex}][title]" class="form-control" placeholder="Specification Title">
                </div>
                <div class="col-md-6 mb-2 mb-md-0">
                    <input type="text" name="specifications[${specIndex}][value]" class="form-control" placeholder="Specification Value">
                </div>
                <div class="col-md-1 text-md-right">
                    <button type="button" class="btn btn-danger btn-sm remove-spec">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#specifications-wrapper').append(newRow);
        specIndex++;
    });

    $(document).on('click', '.remove-spec', function() {
        $(this).closest('.specification-row').remove();
    });
});
</script>
@endsection