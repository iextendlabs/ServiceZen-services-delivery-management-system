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
        <span class="text-danger">Note: To add multiple values in a single input field, use \ as a separator. For example, for "Sub Title": Skin\Hair\Massage, etc.</span>
        @csrf
        @method('PUT')
        <input type="hidden" name="url" value="{{ url()->previous() }}">
        <ul class="nav nav-tabs" id="myTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="slot-tab" data-toggle="tab" href="#slot" role="tab" aria-controls="slot" aria-selected="false">Time Slot</a>
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
                            <input type="text" name="name" value="{{ old('name',$serviceStaff->name) }}" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Sub Title / Designation</strong>
                            <select class="form-control selectpicker" id="sub_titles" name="sub_titles[]"
                                multiple data-live-search="true" data-actions-box="true">
                                @foreach ($subTitles as $subTitle)
                                    <option value="{{ $subTitle->id }}" {{ in_array($subTitle->id, old('sub_titles', $serviceStaff->subTitles->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>{{ $subTitle->name }}
                                    </option>
                                @endforeach
                            </select>
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
                                <option value="1" {{ old('status', $serviceStaff->staff->status ?? null) == '1' ? 'selected' : '' }}>
                                    Enable
                                </option>
                                <option value="0" {{ old('status', $serviceStaff->staff->status ?? null) == '0' ? 'selected' : '' }}>
                                    Disable
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Online:</strong>
                            <select name="online" class="form-control">
                                <option value="1"  {{ old('online', $serviceStaff->staff->online ?? null) == '1' ? 'selected' : '' }}>Online</option>
                                <option value="0"  {{ old('online', $serviceStaff->staff->online ?? null) == '0' ? 'selected' : '' }}>Offline</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Get Quote:</strong>
                            <select name="get_quote" class="form-control">
                                <option value="1"  {{ old('get_quote', $serviceStaff->staff->get_quote ?? null) == '1' ? 'selected' : '' }}>Enable</option>
                                <option value="0"  {{ old('get_quote', $serviceStaff->staff->get_quote ?? null) == '0' ? 'selected' : '' }}>Disable</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Show Quote Detail:</strong>
                            <select name="show_quote_detail" class="form-control">
                                <option value="1"  {{ old('show_quote_detail', $serviceStaff->staff->show_quote_detail ?? null) == '1' ? 'selected' : '' }}>Enable</option>
                                <option value="0"  {{ old('show_quote_detail', $serviceStaff->staff->show_quote_detail ?? null) == '0' ? 'selected' : '' }}>Disable</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Quote Amount:</strong>
                            <input type="number" step="0.01" name="quote_amount" class="form-control" value="{{ old('quote_amount',$serviceStaff->staff->quote_amount ?? "") }}" placeholder="Quote Amount">
                            <small class="form-text text-muted">Minimum value: 0.01</small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Quote Commission:</strong>
                            <input type="number" name="quote_commission" class="form-control" value="{{ old('quote_commission',$serviceStaff->staff->quote_commission ?? "") }}" placeholder="Quote Commission In %">
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
                            <strong>Assign Drivers for Each Day:</strong>
                            @if (count($serviceStaff->staffTimeSlots) <= 0)
                                <div class="alert alert-danger">
                                    This staff member doesn't have any assigned time slots. Please assign time slots first before assigning a driver.
                                </div>
                            @endif
                            <table id="weekly-drivers" class="table table-bordered supervisor-table" style="width: 100%; margin-bottom: 20px;">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Driver</th>
                                        <th>Time Slot</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                    @php
                                        $dayColors = [
                                            'Monday' => '#f8d7da',
                                            'Tuesday' => '#d4edda',
                                            'Wednesday' => '#d1ecf1',
                                            'Thursday' => '#fff3cd',
                                            'Friday' => '#cce5ff',
                                            'Saturday' => '#e2e3e5',
                                            'Sunday' => '#f5c6cb',
                                        ];
                                        $backgroundColor = $dayColors[$day] ?? '#ffffff';
                                    @endphp
                                        @php
                                            $driversForDay = $assignedDrivers[$day] ?? []; 
                                            $firstRow = true;
                                        @endphp
                                        @foreach($driversForDay as $index => $driverData )
                                            <tr id="{{ $day }}-first-row" data-day="{{ $day }}" style="background-color: {{ $dayColors[$day] ?? '#ffffff' }}">
                                                @if($firstRow)
                                                    <td rowspan="{{ count($driversForDay) }}" class="day-name">{{ $day }}</td>
                                                    @php $firstRow = false; @endphp
                                                @endif
                                                <td>
                                                    <select name="drivers[{{ $day }}][{{ $index }}][driver_id]" class="form-control">
                                                        <option value="">Select Driver</option>
                                                        @foreach ($users as $driver)
                                                            @if ($driver->hasRole("Driver"))
                                                                <option value="{{ $driver->id }}" {{ $driverData['driver_id'] == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="drivers[{{ $day }}][{{ $index }}][time_slot_id]" class="form-control">
                                                        <option value="">Select Time Slot</option>
                                                        @foreach ($serviceStaff->staffTimeSlots as $slot)
                                                            <option value="{{ $slot['id'] }}" {{ $driverData['time_slot_id'] == $slot['id'] ? 'selected' : '' }}>
                                                                {{ \Carbon\Carbon::parse($slot['time_start'])->format('h:i A') }} - 
                                                                {{ \Carbon\Carbon::parse($slot['time_end'])->format('h:i A') }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    @if($index == 0)
                                                    <button type="button" class="btn btn-primary" onclick="addDriverRow('{{ $day }}')">Add</button>
                                                    @endif
                                                    @if($index > 0)
                                                    <button type="button" class="btn btn-danger remove-button">Remove</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if(count($driversForDay) === 0)
                                            <tr id="{{ $day }}-first-row" data-day="{{ $day }}" style="background-color: {{ $dayColors[$day] ?? '#ffffff' }}">
                                                <td rowspan="1" class="day-name">{{ $day }}</td>
                                                <td>
                                                    <select name="drivers[{{ $day }}][0][driver_id]" class="form-control">
                                                        <option value="">Select Driver</option>
                                                        @foreach ($users as $driver)
                                                            @if ($driver->hasRole("Driver"))
                                                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="drivers[{{ $day }}][0][time_slot_id]" class="form-control">
                                                        <option value="">Select Time Slot</option>
                                                        @foreach ($serviceStaff->staffTimeSlots as $slot)
                                                            <option value="{{ $slot['id'] }}">
                                                                {{ \Carbon\Carbon::parse($slot['time_start'])->format('h:i A') }} - 
                                                                {{ \Carbon\Carbon::parse($slot['time_end'])->format('h:i A') }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-primary" onclick="addDriverRow('{{ $day }}')">Add</button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
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
                                        <input type="checkbox" {{ in_array($user->id, old('ids', $supervisor_ids)) ? 'checked' : '' }} name="ids[]" value="{{ $user->id }}">
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
                                    @if($serviceStaff->affiliateCategories)
                                        @foreach ($serviceStaff->affiliateCategories as $index => $staffCategory)
                                        <tr>
                                            <td>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <select name='categories[{{ $index }}][category_id]' class="form-control category-select" required>
                                                            <option value="">Select Category</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}" 
                                                                    @if($staffCategory->category_id == $category->id) selected @endif>
                                                                    {{ $category->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="col-md-12">
                                                    <div class="form-group d-flex">
                                                        <input type="number" name="categories[{{ $index }}][category_commission]" 
                                                            value="{{ $staffCategory->commission }}" class="form-control category-commission" 
                                                            placeholder="Commission" required min="1">
                                                        <select name="categories[{{ $index }}][commission_type]" class="form-control commission-type">
                                                            <option value="percentage" {{ $staffCategory->commission_type == 'percentage' ? 'selected' : '' }}>%</option>
                                                            <option value="fixed" {{ $staffCategory->commission_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-success add-service" data-category-row="{{ $index }}">
                                                    <i class="fa fa-plus-circle"></i> Add Service
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger remove-category">
                                                    <i class="fa fa-minus-circle"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr id="service-container-{{ $index }}">
                                            <td colspan="4">
                                                <div class="service-wrapper d-flex flex-wrap">
                                                    @if($staffCategory->services)
                                                        @foreach ($staffCategory->services as $serviceIndex => $service)
                                                        <div class="service-box col-md-6 border-bottom mb-3 py-3">
                                                            <div class="form-group">
                                                                <select name="categories[{{ $index }}][services][{{ $serviceIndex }}][service_id]" 
                                                                    class="form-control service-select select2" required>
                                                                    <option value="">Select Service</option>
                                                                    @foreach ($services as $serviceOption)
                                                                        <option value="{{ $serviceOption->id }}" 
                                                                            @if($service->service_id == $serviceOption->id) selected @endif>
                                                                            {{ $serviceOption->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group d-flex">
                                                                <input type="number" name="categories[{{ $index }}][services][{{ $serviceIndex }}][service_commission]" 
                                                                    value="{{ $service->commission }}" class="form-control service-commission" required min="1">
                                                                <select name="categories[{{ $index }}][services][{{ $serviceIndex }}][commission_type]" class="form-control commission-type">
                                                                    <option value="percentage" {{ $service->commission_type == 'percentage' ? 'selected' : '' }}>%</option>
                                                                    <option value="fixed" {{ $service->commission_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                                </select>
                                                            </div>
                                                            <button type="button" class="btn btn-danger remove-service"><i class="fa fa-minus-circle"></i></button>
                                                        </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <button id="addCategoryBtn" onclick="addCategoryRow();" type="button" class="btn btn-primary float-right"><i class="fa fa-plus-circle"></i></button>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Location:</strong>
                            <input type="text" name="location" class="form-control" placeholder="Location" value="{{ old('location',$serviceStaff->staff->location ?? "") }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Nationality:</strong>
                            <input type="text" name="nationality" class="form-control" placeholder="Nationality" value="{{ old('nationality',$serviceStaff->staff->nationality ?? "") }}">
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
                                        {{ old('membership_plan_id', $serviceStaff->staff->membership_plan_id ?? null) == $membership_plan->id ? 'selected' : '' }}>
                                        {{ $membership_plan->plan_name }} (AED{{ $membership_plan->membership_fee }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Expiry Date:</strong>
                            <input type="date" name="expiry_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('expiry_date', $serviceStaff->staff->expiry_date ?? '') }}">
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
                                        {{ (old('affiliate_id', $serviceStaff->staff->affiliate_id ?? '') == $affiliate->id) ? 'selected' : '' }}>
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
            <div class="tab-pane fade" id="slot" role="tabpanel" aria-labelledby="slot-tab">
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Time Slots</h5>
                                <div class="form-group">
                                    <input type="text" id="timeSlotSearch" class="form-control" placeholder="Search time slots...">
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="startTimeFilter" placeholder="Start time (e.g. 9:00 AM)">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="endTimeFilter" placeholder="End time (e.g. 5:00 PM)">
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-sm btn-secondary w-100" id="clearTimeFilters">Clear</button>
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
                                                    value="{{ $timeSlot->id }}" {{ in_array($timeSlot->id, old('time_slots', $serviceStaff->staffTimeSlots->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
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
                                                    value="{{ $zone->id }}" {{ in_array($zone->id, old('zones', $serviceStaff->staffZones->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
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
                                <thead>
                                    <tr>
                                        <th> <input type="checkbox" class="category-checkbox" name="category" value="all"> </th>
                                        <th>Title</th>
                                    </tr>
                                </thead>
                                <tbody id="category-table-body">
                                    @foreach ($categories as $category)
                                    <tr>
                                        <td>

                                            <input type="checkbox" class="category-checkbox" name="category_ids[]" value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', $category_ids)) ? 'checked' : '' }}>
                                        </td>
                                        <td>{{ $category->title }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group scroll-div">
                            <span style="color: red;">*</span><strong>Services:</strong>
                            <input type="text" name="search-services" id="search-services" class="form-control" placeholder="Search Services By Name, Price And Duration">
                            <table class="table table-striped table-bordered services-table">
                                <thead>
                                    <tr>
                                        <th> <input type="checkbox" class="service-checkbox" name="service" value="all"> </th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Duration</th>
                                    </tr>
                                </thead>
                                <tbody id="service-table-body">
                                    @foreach ($services as $service)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="service-checkbox" name="service_ids[]" value="{{ $service->id }}" data-category="{{ $service->category_id }}" {{ in_array($service->id, old('service_ids', $service_ids)) ? 'checked' : '' }}>
                                        </td>
                                        <td>{{ $service->name }}</td>

                                        <td>{{ isset($service->discount) ? 
                                        $service->discount : $service->price }}</td>
                                        <td>{{ $service->duration ?? ""}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
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
    $(document).ready(function () {
        $('.service-select').select2();

        $('.category-select').each(function () {
            var categoryId = $(this).val();
            var categoryRow = $(this).closest('tr').next('tr').attr('id');
            var serviceWrapper = $(`#${categoryRow} .service-wrapper`);

            if (categoryId) {
                serviceWrapper.find('.service-select').each(function () {
                    var selectedServiceId = $(this).val();
                    var dropdown = $(this);
                    
                    fetchServices(categoryId, dropdown, selectedServiceId);
                });
            }
        });
    });
    var category_row = {{ $serviceStaff->affiliateCategories->count() ?? 0 }};
    

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

    $(document).on('click', '.remove-category', function () {
        $(this).closest('tr').next('tr').remove();
        $(this).closest('tr').remove();
    });

    $(document).on('click', '.add-service', function () {
        var categoryRow = $(this).data('category-row');
        var serviceWrapper = $(`#service-container-${categoryRow} .service-wrapper`);
        var serviceIndex = serviceWrapper.find('.service-box').length;
        var categoryId = $(`select[name="categories[${categoryRow}][category_id]"]`).val();

        var newServiceRow = `
            <div class="service-box col-md-6 border-bottom mb-3 py-3">
                <div class="form-group">
                    <select name="categories[${categoryRow}][services][${serviceIndex}][service_id]" class="form-control service-select" required>
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

        serviceWrapper.find('.service-select').last().select2();

        if (categoryId) {
            fetchServices(categoryId, serviceWrapper.find('.service-select').last());
        }
    });

    $(document).on('click', '.remove-service', function () {
        $(this).closest('.service-box').remove();
    });

    $(document).on('change', '.category-select', function () {
        var categoryId = $(this).val();
        var categoryRow = $(this).closest('tr').next('tr').attr('id');
        var serviceWrapper = $(`#${categoryRow} .service-wrapper`);

        if (categoryId) {
            serviceWrapper.find('.service-select').each(function () {
                fetchServices(categoryId, $(this));
            });
        } else {
            serviceWrapper.find('.service-select').html('<option value="">Select Service</option>').select2();
        }
    });

    function fetchServices(categoryId, dropdown, selectedServiceId = null) {
        $.ajax({
            url: "{{ route('getServicesByCategory') }}",
            type: "GET",
            data: { category_id: categoryId },
            success: function (data) {
                dropdown.html('<option value="">Select Service</option>');
                $.each(data, function (index, service) {
                    var selected = (selectedServiceId && selectedServiceId == service.id) ? "selected" : "";
                    dropdown.append(`<option value="${service.id}" ${selected}>${service.name}</option>`);
                });
                dropdown.select2();
            }
        });
    }
</script>
<script>
    // Initialize row counts based on the existing table
    let rowCounts = {
        Monday: $("tr[data-day='Monday']").length,
        Tuesday: $("tr[data-day='Tuesday']").length,
        Wednesday: $("tr[data-day='Wednesday']").length,
        Thursday: $("tr[data-day='Thursday']").length,
        Friday: $("tr[data-day='Friday']").length,
        Saturday: $("tr[data-day='Saturday']").length,
        Sunday: $("tr[data-day='Sunday']").length
    };

    const dayColors = {
        Monday: '#f8d7da',
        Tuesday: '#d4edda',
        Wednesday: '#d1ecf1',
        Thursday: '#fff3cd',
        Friday: '#cce5ff',
        Saturday: '#e2e3e5',
        Sunday: '#f5c6cb',
    };

    // Add a new driver row for the selected day
    function addDriverRow(day) {
        rowCounts[day]++;

        const newRow = $(`
            <tr data-day="${day}" class="driver-row" style="background-color: ${dayColors[day] || '#ffffff'};">
                <td>
                    <select name="drivers[${day}][${rowCounts[day] - 1}][driver_id]" class="form-control">
                        <option value="" disabled selected>Select Driver</option>
                        ${generateDriverOptions()}
                    </select>
                </td>
                <td>
                    <select name="drivers[${day}][${rowCounts[day] - 1}][time_slot_id]" class="form-control">
                        <option value="" disabled selected>Select Time Slot</option>
                        ${generateTimeSlotOptions()}
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove-button">Remove</button>
                </td>
            </tr>
        `);

        $(`tr[data-day="${day}"]:last`).after(newRow);

        // Update rowspan for the day header cell
        $(`#${day}-first-row .day-name`).attr('rowspan', rowCounts[day]);
    }

    // Generate driver options
    function generateDriverOptions() {
        let options = '';
        @foreach ($users as $driver)
            @if ($driver->hasRole("Driver"))
                options += `<option value="{{ $driver->id }}">{{ $driver->name }}</option>`;
            @endif
        @endforeach
        return options;
    }

    // Generate time slot options
    function generateTimeSlotOptions() {
        let options = '';
        @foreach ($serviceStaff->staffTimeSlots as $slot)
            options += `<option value="{{ $slot['id'] }}">
                            {{ \Carbon\Carbon::parse($slot['time_start'])->format('h:i A') }} - 
                            {{ \Carbon\Carbon::parse($slot['time_end'])->format('h:i A') }}
                        </option>`;
        @endforeach
        return options;
    }

    // Remove a driver row
    $("#weekly-drivers").on("click", ".remove-button", function () {
        const row = $(this).closest("tr");
        const day = row.data("day");

        row.remove();

        rowCounts[day]--;

        // Update rowspan for the day header cell
        $(`#${day}-first-row .day-name`).attr("rowspan", rowCounts[day]);
    });

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
<script>
    $(document).ready(function() {
        // Time Slot Search and Filter
        $('#timeSlotSearch, #startTimeFilter, #endTimeFilter').on('keyup', function() {
            filterTimeSlots();
        });

        $('#clearTimeFilters').click(function(e) {
            e.preventDefault();
            $('#timeSlotSearch').val('');
            $('#startTimeFilter').val('');
            $('#endTimeFilter').val('');
            filterTimeSlots();
        });

        function filterTimeSlots() {
            const nameSearch = $('#timeSlotSearch').val().toLowerCase();
            const startTime = $('#startTimeFilter').val().toLowerCase();
            const endTime = $('#endTimeFilter').val().toLowerCase();

            $('.time-slot-row').each(function() {
                const $row = $(this);
                const name = $row.find('.slot-name').text().toLowerCase();
                const start = $row.find('.start-time').text().toLowerCase();
                const end = $row.find('.end-time').text().toLowerCase();

                const nameMatch = name.includes(nameSearch);
                const startMatch = startTime ? start.includes(startTime) : true;
                const endMatch = endTime ? end.includes(endTime) : true;

                $row.toggle(nameMatch && startMatch && endMatch);
            });
            
            $('#selectAllTimeSlots').prop('checked', false);
        }

        // Zone Search
        $('#zoneSearch').on('keyup', function() {
            const searchText = $(this).val().toLowerCase();
            $('.zone-row').each(function() {
                const name = $(this).find('.zone-name').text().toLowerCase();
                $(this).toggle(name.includes(searchText));
            });
            $('#selectAllZones').prop('checked', false);
        });

        // Select All Checkboxes
        $('#selectAllTimeSlots').change(function() {
            $('.time-slot-row:visible .time-slot-checkbox').prop('checked', this.checked);
        });

        $('#selectAllZones').change(function() {
            $('.zone-row:visible .zone-checkbox').prop('checked', this.checked);
        });
    });
</script>
<script>
$(document).ready(function() {
    function sortCheckedToTop(tableId, checkboxClass) {
        const $table = $(tableId);
        const $rows = $table.find('tr');
        
        $rows.sort(function(a, b) {
            const aChecked = $(a).find(checkboxClass).is(':checked');
            const bChecked = $(b).find(checkboxClass).is(':checked');
            
            if (aChecked && !bChecked) return -1;
            if (!aChecked && bChecked) return 1;
            return 0;
        });
        
        $table.append($rows);
    }

    sortCheckedToTop('#timeSlotTable', '.time-slot-checkbox');
    sortCheckedToTop('#zoneTable', '.zone-checkbox');
    sortCheckedToTop('#category-table-body', '.category-checkbox');
    sortCheckedToTop('#service-table-body', '.service-checkbox');
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