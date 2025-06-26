@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Add New Service Staff</h2>
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
    <form action="{{ route('serviceStaff.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <ul class="nav nav-tabs" id="myTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="slot-tab" data-toggle="tab" href="#slot" role="tab" aria-controls="slot-zone" aria-selected="false">Time Slot</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="zone-tab" data-toggle="tab" href="#zone" role="tab" aria-controls="zone" aria-selected="false">Zone</a>
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
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Sub Title / Designation</strong>
                            <select class="form-control selectpicker" id="sub_titles" name="sub_titles[]"
                                multiple data-live-search="true" data-actions-box="true">
                                @foreach ($subTitles as $subTitle)
                                    <option value="{{ $subTitle->id }}" {{ in_array($subTitle->id, old('sub_titles', [])) ? 'selected' : '' }}>{{ $subTitle->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Email:</strong>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="abc@gmail.com">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Phone Number:</strong>
                            <input id="number_country_code" type="hidden" name="number_country_code" />
                            <input type="tel" id="number" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Whatsapp Number:</strong>
                            <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                            <input type="tel" id="whatsapp" name="whatsapp" class="form-control" value="{{ old('whatsapp') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Status:</strong>
                            <select name="status" class="form-control">
                                <option value="1"  {{ old('status') == '1' ? 'selected' : '' }}>Enable</option>
                                <option value="0"  {{ old('status') == '0' ? 'selected' : '' }}>Disable</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Online:</strong>
                            <select name="online" class="form-control">
                                <option value="1"  {{ old('online') == '1' ? 'selected' : '' }}>Online</option>
                                <option value="0"  {{ old('online') == '0' ? 'selected' : '' }}>Offline</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Get Quote:</strong>
                            <select name="get_quote" class="form-control">
                                <option value="1"  {{ old('get_quote') == '1' ? 'selected' : '' }}>Enable</option>
                                <option value="0"  {{ old('get_quote') == '0' ? 'selected' : '' }}>Disable</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Show Quote Detail:</strong>
                            <select name="show_quote_detail" class="form-control">
                                <option value="1"  {{ old('show_quote_detail') == '1' ? 'selected' : '' }}>Enable</option>
                                <option value="0"  {{ old('show_quote_detail') == '0' ? 'selected' : '' }}>Disable</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Quote Amount:</strong>
                            <input type="number" step="0.01" name="quote_amount" class="form-control" value="{{ old('quote_amount') }}" placeholder="Quote Amount">
                            <small class="form-text text-muted">Minimum value: 0.01</small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Quote Commission:</strong>
                            <input type="number" name="quote_commission" class="form-control" value="{{ old('quote_commission') }}" placeholder="Quote Commission In %">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>About:</strong>
                            <textarea name="about" id="summernote" class="form-control">{{ old('about')}}</textarea>
                            <script>
                                (function($) {
                                    $('#summernote').summernote({
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
                            <img class="image-preview" height="130px" src="/staff-images/default.png">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Password:</strong>
                            <input type="password" name="password" class="form-control" placeholder="Password">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Confirm Password:</strong>
                            <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password">
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
                                        <input type="checkbox" name="ids[]" value="{{ $user->id }}" {{ in_array($user->id, old('ids', [])) ? 'checked' : '' }}>
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
                            <input type="number" name="commission" class="form-control" value="{{ old('commission') }}" placeholder="Commission In %">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Categories base commission:</strong>
                            <table id="categoryTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Category Commission</th>
                                        <th>Services</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                            <button id="addCategoryBtn" onclick="addCategoryRow();" type="button" class="btn btn-primary float-right"><i class="fa fa-plus-circle"></i></button>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Location:</strong>
                            <input type="text" name="location" class="form-control" placeholder="Location" value="{{ old('location') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Nationality:</strong>
                            <input type="text" name="nationality" class="form-control" placeholder="Nationality" value="{{ old('nationality') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Additional Charges:</strong>
                            <input type="number" name="charges" class="form-control" value="{{ old('charges') }}" placeholder="Additional Charges">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Commission Salary:</strong>
                            <input type="number" name="fix_salary" class="form-control" value="{{ old('fix_salary') }}" placeholder="Commission Salary">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Minmum Order Value:</strong>
                            <input type="number" name="min_order_value" class="form-control" value="{{ old('min_order_value') }}" placeholder="Minmum Order Value">
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="slot" role="tabpanel" aria-labelledby="slot-tab">
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Time Slots</h5>
                                <div class="form-group">
                                    <input type="text" id="timeSlotSearch" class="form-control" placeholder="Search by name...">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" id="startTimeFilter" class="form-control" 
                                            placeholder="Start time (e.g. 9:00 AM)">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" id="endTimeFilter" class="form-control" 
                                            placeholder="End time (e.g. 5:00 PM)">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <div class="mb-2">
                                    <button type="button" id="addSlotBtn" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Add New Time Slot
                                    </button>
                                </div>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="50px">
                                                <input type="checkbox" id="selectAllTimeSlots">
                                            </th>
                                            <th>Name</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th width="50px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="timeSlotTable">
                                        @foreach($timeSlots as $timeSlot)
                                        <tr class="time-slot-row">
                                            <td>
                                                <input type="checkbox" name="time_slots[]" class="time-slot-checkbox" 
                                                    value="{{ $timeSlot->id }}" {{ in_array($timeSlot->id, old('time_slots', [])) ? 'checked' : '' }}>
                                            </td>
                                            <td class="slot-name">{{ $timeSlot->name }}</td>
                                            <td class="start-time">{{ \Carbon\Carbon::parse($timeSlot->time_start)->format('h:i A') }}</td>
                                            <td class="end-time">{{ \Carbon\Carbon::parse($timeSlot->time_end)->format('h:i A') }}</td>
                                            <td></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="zone" role="tabpanel" aria-labelledby="zone-tab">
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Zones</h5>
                                <div class="form-group">
                                    <input type="text" id="zoneSearch" class="form-control" placeholder="Search zones...">
                                </div>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <div class="mb-2">
                                    <button type="button" id="addZoneBtn" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Add New Zone
                                    </button>
                                </div>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="50px">
                                                <input type="checkbox" id="selectAllZones">
                                            </th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th width="50px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="zoneTable">
                                        @foreach($staffZones as $zone)
                                        <tr class="zone-row">
                                            <td>
                                                <input type="checkbox" name="zones[]" class="zone-checkbox" 
                                                    value="{{ $zone->id }}" {{ in_array($zone->id, old('zones', [])) ? 'checked' : '' }}>
                                            </td>
                                            <td class="zone-name">{{ $zone->name }}</td>
                                            <td>{{ $zone->description ?? 'N/A' }}</td>
                                            <td></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if($socialLinks)
            <div class="tab-pane fade" id="social-links" role="tabpanel" aria-labelledby="social-links-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Instagram:</strong>
                            <input type="text" name="instagram" class="form-control" placeholder="Instagram" value="{{ old('instagram') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Snapchat:</strong>
                            <input type="text" name="snapchat" class="form-control" placeholder="Snapchat" value="{{ old('snapchat') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Facebook:</strong>
                            <input type="text" name="facebook" class="form-control" placeholder="Facebook" value="{{ old('facebook') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Youtube:</strong>
                            <input type="text" name="youtube" class="form-control" placeholder="Youtube" value="{{ old('youtube') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Tiktok:</strong>
                            <input type="text" name="tiktok" class="form-control" placeholder="Tiktok" value="{{ old('tiktok') }}">
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="tab-pane fade" id="gallery" role="tabpanel" aria-labelledby="gallery-tab">
                <div class="row">
                    <div class="col-md-12">
                        <strong>Youtube Video:</strong>

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
                                        <th>Images</th>
                                    </tr>
                                </thead>
                                <tbody>

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
                                        <input type="checkbox" class="category-checkbox"  name="category" value="all">
                                    </td>
                                    <td>All</td>
                                </tr>
                                @foreach ($categories as $category)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="category-checkbox" name="category_ids[]" value="{{ $category->id }}" {{ (is_array(old('category_ids')) && in_array($category->id, old('category_ids'))) ? 'checked' : '' }}>
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
                                        <input type="checkbox" class="service-checkbox" name="service_ids[]" {{ (is_array(old('service_ids')) && in_array($service->id, old('service_ids'))) ? 'checked' : '' }} value="{{ $service->id }}" data-category="{{ $service->category_id }}">
                                    </td>
                                    <td>{{ $service->name }}</td>

                                    <td>{{ isset($service->discount) ? 
                                    $service->discount : $service->price }}</td>
                                    <td>{{ $service->duration ?? "" }}</td>
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
                                @if(in_array($field, ['id_card_front','id_card_back', 'passport']))
                                    <span style="color: red;">*</span>
                                @endif
                                <strong>{{ $label }}:</strong>
                                <input type="file" name="{{ $field }}" class="form-control">
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
    var category_row = 0;

    function addCategoryRow() {
        var newRow = `
            <tr>
                <td>
                    <div class="col-md-12">
                        <div class="form-group">
                            <select name='categories[${category_row}][category_id]' class="form-control category-select" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="col-md-12">
                        <div class="form-group d-flex">
                            <input type="number" name="categories[${category_row}][category_commission]" class="form-control category-commission" placeholder="Commission" required min="1">
                            <select name="categories[${category_row}][commission_type]" class="form-control commission-type">
                                <option value="percentage">%</option>
                                <option value="fixed">Fixed</option>
                            </select>
                        </div>
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-success add-service" data-category-row="${category_row}">
                        <i class="fa fa-plus-circle"></i> Add Service
                    </button>
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove-category">
                        <i class="fa fa-minus-circle"></i>
                    </button>
                </td>
            </tr>
            <tr id="service-container-${category_row}">
                <td colspan="4">
                    <div class="service-wrapper d-flex flex-wrap"></div>
                </td>
            </tr>
        `;

        $('#categoryTable tbody').append(newRow);
        category_row++;
    }

    $(document).on('click', '.remove-category', function() {
        $(this).closest('tr').next('tr').remove();
        $(this).closest('tr').remove();
    });

    $(document).on('click', '.add-service', function() {
        var categoryRow = $(this).data('category-row');
        var serviceWrapper = $(`#service-container-${categoryRow} .service-wrapper`);
        var serviceIndex = serviceWrapper.find('.service-box').length;

        var categoryId = $(`#service-container-${categoryRow}`).prev('tr').find('.category-select').val();

        var newServiceRow = `
            <div class="service-box col-md-6 border-bottom mb-3 py-3">
                <div class="form-group">
                    <select name="categories[${categoryRow}][services][${serviceIndex}][service_id]" class="form-control service-select select2" required>
                        <option value="">Select Service</option>
                    </select>
                </div>
                <div class="form-group d-flex">
                    <input type="number" name="categories[${categoryRow}][services][${serviceIndex}][service_commission]" class="form-control service-commission" placeholder="Service Commission" required min="1">
                    <select name="categories[${categoryRow}][services][${serviceIndex}][commission_type]" class="form-control commission-type">
                        <option value="percentage">%</option>
                        <option value="fixed">Fixed</option>
                    </select>
                </div>
                <button type="button" class="btn btn-danger remove-service"><i class="fa fa-minus-circle"></i></button>
            </div>
        `;

        serviceWrapper.append(newServiceRow);

        if (categoryId) {
            $.ajax({
                url: "{{ route('getServicesByCategory') }}",
                type: "GET",
                data: { category_id: categoryId },
                success: function(data) {
                    var $dropdown = serviceWrapper.find('.service-select').last();
                    $dropdown.html('<option value="">Select Service</option>');
                    $.each(data, function(index, service) {
                        $dropdown.append(`<option value="${service.id}">${service.name}</option>`);
                    });
                    $dropdown.select2();
                }
            });
        } else {
            serviceWrapper.find('.service-select').last().html('<option value="">Select Service</option>').select2();
        }
    });

    $(document).on('click', '.remove-service', function() {
        $(this).closest('.service-box').remove();
    });

    $(document).on('change', '.category-select', function() {
        var categoryId = $(this).val();
        var categoryRow = $(this).closest('tr').next('tr').attr('id');
        var serviceWrapper = $(`#${categoryRow} .service-wrapper`);

        if (categoryId) {
            $.ajax({
                url: "{{ route('getServicesByCategory') }}",
                type: "GET",
                data: { category_id: categoryId },
                success: function(data) {
                    serviceWrapper.find('.service-select').each(function() {
                        var $dropdown = $(this);
                        $dropdown.html('<option value="">Select Service</option>');
                        $.each(data, function(index, service) {
                            $dropdown.append(`<option value="${service.id}">${service.name}</option>`);
                        });
                        $dropdown.select2();
                    });
                }
            });
        } else {
            serviceWrapper.find('.service-select').html('<option value="">Select Service</option>').select2();
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
                        <input type="file" name="gallery_images[]" class="class="form-control" image-input" accept="image/*">
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
            $(this).closest("tr").html('');
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
        // Add new time slot row
        $('#addSlotBtn').click(function() {
            const newSlotCount = $('.new-time-slot').length;
            const newRow = $(`
                <tr class="time-slot-row new-time-slot">
                    <td>
                        <input type="checkbox" class="time-slot-checkbox" disabled>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span style="color: red; margin-right: 4px;">*</span>
                            <input type="text" required name="new_time_slots[${newSlotCount}][name]" class="form-control form-control-sm new-slot-name" placeholder="Slot name">
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span style="color: red; margin-right: 4px;">*</span>
                            <input type="time" required name="new_time_slots[${newSlotCount}][time_start]" class="form-control form-control-sm new-start-time">
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span style="color: red; margin-right: 4px;">*</span>
                            <input type="time" required name="new_time_slots[${newSlotCount}][time_end]" class="form-control form-control-sm new-end-time">
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-time-slot-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
            
            $('#timeSlotTable').prepend(newRow);
            
            // Add event listener to remove button
            newRow.find('.remove-time-slot-btn').click(function() {
                newRow.remove();
                // Re-index remaining time slots if needed
                $('.new-time-slot').each(function(index) {
                    $(this).find('[name^="new_time_slots"]').each(function() {
                        const name = $(this).attr('name').replace(/\[\d+\]/, `[${index}]`);
                        $(this).attr('name', name);
                    });
                });
            });
        });

        // Search functionality
        $('#timeSlotSearch').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.time-slot-row').each(function() {
                const $row = $(this);
                const name = $row.find('.slot-name').text().toLowerCase() || 
                            $row.find('.new-slot-name').val().toLowerCase();
                $row.toggle(name.includes(searchTerm));
            });
        });

        // Time filter functionality
        function filterByTime() {
            const startFilter = $('#startTimeFilter').val().toLowerCase();
            const endFilter = $('#endTimeFilter').val().toLowerCase();
            
            $('.time-slot-row').each(function() {
                const $row = $(this);
                const startTime = $row.find('.start-time').text().toLowerCase() || 
                                $row.find('.new-start-time').val().toLowerCase();
                const endTime = $row.find('.end-time').text().toLowerCase() || 
                                $row.find('.new-end-time').val().toLowerCase();
                
                const matchesStart = !startFilter || (startTime && startTime.includes(startFilter));
                const matchesEnd = !endFilter || (endTime && endTime.includes(endFilter));
                
                $row.toggle(matchesStart && matchesEnd);
            });
        }
        
        $('#startTimeFilter, #endTimeFilter').on('input', filterByTime);
        
        // Select all functionality
        $('#selectAllTimeSlots').change(function() {
            const isChecked = $(this).prop('checked');
            $('.time-slot-checkbox:not(:disabled)').prop('checked', isChecked);
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Add new zone row
        $('#addZoneBtn').click(function() {
            const newZoneCount = $('.new-zone').length;
            const newRow = $(`
                <tr class="zone-row new-zone">
                    <td>
                        <input type="checkbox" class="zone-checkbox" disabled>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span style="color: red; margin-right: 4px;">*</span>
                            <input type="text" name="new_zones[${newZoneCount}][name]" required class="form-control form-control-sm new-zone-name" placeholder="Zone name">
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span style="color: red; margin-right: 4px;">*</span>
                            <input type="text" name="new_zones[${newZoneCount}][description]" required class="form-control form-control-sm new-zone-desc" placeholder="Description">
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-zone-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
            
            $('#zoneTable').prepend(newRow);
            
            // Add event listener to remove button
            newRow.find('.remove-zone-btn').click(function() {
                newRow.remove();
                // Re-index remaining zones if needed
                $('.new-zone').each(function(index) {
                    $(this).find('[name^="new_zones"]').each(function() {
                        const name = $(this).attr('name').replace(/\[\d+\]/, `[${index}]`);
                        $(this).attr('name', name);
                    });
                });
            });
        });

        // Search functionality
        $('#zoneSearch').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.zone-row').each(function() {
                const $row = $(this);
                const name = $row.find('.zone-name').text().toLowerCase() || 
                            $row.find('.new-zone-name').val().toLowerCase();
                const desc = $row.find('td:eq(2)').text().toLowerCase() || 
                            $row.find('.new-zone-desc').val().toLowerCase();
                
                $row.toggle(name.includes(searchTerm) || desc.includes(searchTerm));
            });
        });

        // Select all functionality
        $('#selectAllZones').change(function() {
            const isChecked = $(this).prop('checked');
            $('.zone-checkbox:not(:disabled)').prop('checked', isChecked);
        });
    });
</script>
@endsection