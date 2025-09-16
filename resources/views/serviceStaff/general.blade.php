@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row align-items-center mb-4">
        <div class="col-md-4 margin-tb">
            <h2>Service Staff General</h2>
        </div>
        <div class="col-md-8 mt-2 mt-md-0">
            <div class="d-flex flex-wrap justify-content-md-end gap-2">
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.time-slots', $serviceStaff->id) }}">
                    <i class="bi bi-clock"></i> Time Slots
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.zones', $serviceStaff->id) }}">
                    <i class="bi bi-geo-alt"></i> Zones
                </a>
                @if($socialLinks)
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.social-links', $serviceStaff->id) }}">
                    <i class="bi bi-share"></i> Social Links
                </a>
                @endif
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.gallery', $serviceStaff->id) }}">
                    <i class="bi bi-image"></i> Gallery
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.categories-and-services', $serviceStaff->id) }}">
                    <i class="bi bi-grid"></i> Categories & Services
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.documents', $serviceStaff->id) }}">
                    <i class="bi bi-file-earmark-text"></i> Documents
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
    <form action="{{ route('serviceStaff.general.update',$serviceStaff->id) }}" method="POST" enctype="multipart/form-data">
        <input type="hidden" value="{{ $serviceStaff->staff->id ?? '' }}" name="staff_id">
        <input type="hidden" value="{{ $freelancer_join }}" name="freelancer_join" />
        @csrf
        @method('POST')
        <input type="hidden" name="url" value="{{ url()->previous() }}">
        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Name:</strong>
                        <input type="text" name="name" value="{{ old('name',$serviceStaff->name) }}" class="form-control" placeholder="Name">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Email:</strong>
                        <input type="email" name="email" value="{{ old('email',$serviceStaff->email) }}" class="form-control" placeholder="abc@gmail.com">
                    </div>
                </div>                
                <div class="col-md-6">
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
                <div class="col-md-3">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Phone Number:</strong>
                        <input id="number_country_code" type="hidden" name="number_country_code" />
                        <input type="tel" id="number" name="phone" value="{{ old('phone',$serviceStaff->staff->phone ?? "") }}" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Whatsapp Number:</strong>
                        <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                        <input type="tel" id="whatsapp" name="whatsapp" value="{{ old('whatsapp',$serviceStaff->staff->whatsapp ?? "") }}" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
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
                <div class="col-md-6">
                    <div class="form-group">
                        <strong>Sort Order:</strong>
                        <input type="number" name="sort" class="form-control"
                            value="{{ old('sort', $serviceStaff->staff->sort ?? 0) }}">
                        <small class="text-muted">Lower numbers appear first</small>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="form-check form-switch">
                            <!-- Hidden field ensures a value is sent when checkbox is unchecked -->
                            <input type="hidden" name="feature" value="0">

                            <input class="form-check-input" type="checkbox" name="feature" id="feature" value="1"
                                {{ old('feature', $serviceStaff->staff->feature ?? null) == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="feature">Enable featured staff On Web:</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="form-check form-switch">
                            <!-- Hidden field ensures a value is sent when checkbox is unchecked -->
                            <input type="hidden" name="feature_on_app" value="0">

                            <input class="form-check-input" type="checkbox" name="feature_on_app" id="feature_on_app" value="1"
                                {{ old('feature_on_app', $serviceStaff->staff->feature_on_app ?? null) == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="feature_on_app">Enable featured staff On App:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">                  
                            <div class="col-md-6">
                                <div class="form-group">
                                    <strong>Online:</strong>
                                    <select name="online" class="form-control">
                                        <option value="1" {{ old('online', $serviceStaff->staff->online ?? null) == '1' ? 'selected' : '' }}>Online</option>
                                        <option value="0" {{ old('online', $serviceStaff->staff->online ?? null) == '0' ? 'selected' : '' }}>Offline</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <strong>Get Quote:</strong>
                                    <select name="get_quote" class="form-control">
                                        <option value="1" {{ old('get_quote', $serviceStaff->staff->get_quote ?? null) == '1' ? 'selected' : '' }}>Enable</option>
                                        <option value="0" {{ old('get_quote', $serviceStaff->staff->get_quote ?? null) == '0' ? 'selected' : '' }}>Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <strong>Show Quote Detail:</strong>
                                    <select name="show_quote_detail" class="form-control">
                                        <option value="1" {{ old('show_quote_detail', $serviceStaff->staff->show_quote_detail ?? null) == '1' ? 'selected' : '' }}>Enable</option>
                                        <option value="0" {{ old('show_quote_detail', $serviceStaff->staff->show_quote_detail ?? null) == '0' ? 'selected' : '' }}>Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <strong>Quote Amount:</strong>
                                    <input type="number" step="0.01" name="quote_amount" class="form-control" value="{{ old('quote_amount',$serviceStaff->staff->quote_amount ?? "") }}" placeholder="Quote Amount">
                                    <small class="form-text text-muted">Minimum value: 0.01</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <strong>Quote Commission:</strong>
                                    <input type="number" name="quote_commission" class="form-control" value="{{ old('quote_commission',$serviceStaff->staff->quote_commission ?? "") }}" placeholder="Quote Commission In %">
                                </div>
                            </div>
                        </div>
                    </div>   
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong for="image">Upload Image</strong>
                            <input type="file" name="image" class="form-control image-input" accept="image/*">
                            <img class="image-preview" src="/staff-images/{{$serviceStaff->staff->image ?? ''}}" height="130px">
                        </div>
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
                <div class="col-md-4">
                    <div class="form-group">
                        <strong>Password:</strong>
                        <input type="password" name="password" class="form-control" placeholder="Password">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <strong>Confirm Password:</strong>
                        <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Commission:</strong>
                        <input type="number" name="commission" value="{{ old('commission',$serviceStaff->staff->commission ?? "") }}" class="form-control" placeholder="Commission In %">
                    </div>
                </div>                
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Assign Drivers for Each Day:</strong>
                    </div>
                    <div class="form-group">
                        @if ($serviceStaff->staffTimeSlots->isEmpty())
                            <span class="alert alert-danger">
                                This staff member doesn't have any assigned time slots. Please assign time slots first before assigning a driver.
                            </span>
                        @endif
                    </div>

                    @php
                        $dayColors = [
                            'Monday' => 'bg-monday',
                            'Tuesday' => 'bg-tuesday',
                            'Wednesday' => 'bg-wednesday',
                            'Thursday' => 'bg-thursday',
                            'Friday' => 'bg-friday',
                            'Saturday' => 'bg-saturday',
                            'Sunday' => 'bg-sunday',
                        ];
                    @endphp

                    <table id="weekly-drivers" class="table table-bordered supervisor-table w-100 mb-4">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Driver</th>
                                <th>Time Slot</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                @php 
                                    $driversForDay = $assignedDrivers[$day] ?? [];
                                @endphp

                                @if (count($driversForDay) > 0)
                                    {{-- First row with "Select Driver" option and Add button --}}
                                    <tr data-day="{{ $day }}" class="{{ $dayColors[$day] }}">
                                        <td rowspan="{{ count($driversForDay) + 1 }}" class="day-name">{{ $day }}</td>
                                        <td>
                                            <select name="drivers[{{ $day }}][new][driver_id]" class="form-control driver-select">
                                                <option value="">Select Driver</option>
                                                @foreach ($drivers as $driver)
                                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="drivers[{{ $day }}][new][time_slot_id]" class="form-control slot-select">
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
                                            <button type="button" class="btn btn-primary add-driver" data-day="{{ $day }}">Add</button>
                                        </td>
                                    </tr>
                                    
                                    {{-- Subsequent rows with assigned drivers and Remove buttons --}}
                                    @foreach ($driversForDay as $index => $driverData)
                                        <tr data-day="{{ $day }}" class="{{ $dayColors[$day] }}">
                                            <td>
                                                <select name="drivers[{{ $day }}][{{ $index }}][driver_id]" class="form-control driver-select">
                                                    <option value="">Select Driver</option>
                                                    @foreach ($drivers as $driver)
                                                        <option value="{{ $driver->id }}" @selected($driverData['driver_id'] == $driver->id)>
                                                            {{ $driver->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="drivers[{{ $day }}][{{ $index }}][time_slot_id]" class="form-control slot-select">
                                                    <option value="">Select Time Slot</option>
                                                    @foreach ($serviceStaff->staffTimeSlots as $slot)
                                                        <option value="{{ $slot['id'] }}" @selected($driverData['time_slot_id'] == $slot['id'])>
                                                            {{ \Carbon\Carbon::parse($slot['time_start'])->format('h:i A') }} -
                                                            {{ \Carbon\Carbon::parse($slot['time_end'])->format('h:i A') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger remove-driver">Remove</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    {{-- No drivers assigned yet: show row with Add button --}}
                                    <tr data-day="{{ $day }}" class="{{ $dayColors[$day] }}">
                                        <td class="day-name">{{ $day }}</td>
                                        <td>
                                            <select name="drivers[{{ $day }}][0][driver_id]" class="form-control driver-select">
                                                <option value="">Select Driver</option>
                                                @foreach ($drivers as $driver)
                                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="drivers[{{ $day }}][0][time_slot_id]" class="form-control slot-select">
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
                                            <button type="button" class="btn btn-primary add-driver" data-day="{{ $day }}">Add</button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            <div class="col-md-12 py-2">
                <div class="form-group scroll-div">
                    <strong>Supervisor:</strong>
                    <input type="text" name="search-supervisor" id="search-supervisor" class="form-control" placeholder="Search Supervisor By Name And Email">
                    <table class="table table-striped table-bordered supervisor-table">
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                        @foreach ($supervisors as $user)
                        <tr>
                            <td>
                                <input type="checkbox" {{ in_array($user->id, old('ids', $serviceStaff->supervisors()->pluck('supervisor_id')->toArray() ?? [])) ? 'checked' : '' }} name="ids[]" value="{{ $user->id }}">
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="col-md-12 py-4">
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
            <div class="col-md-4">
                <div class="form-group">
                    <strong>Location:</strong>
                    <input type="text" name="location" class="form-control" placeholder="Location" value="{{ old('location',$serviceStaff->staff->location ?? "") }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <strong>Nationality:</strong>
                    <input type="text" name="nationality" class="form-control" placeholder="Nationality" value="{{ old('nationality',$serviceStaff->staff->nationality ?? "") }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <strong>Additional Charges:</strong>
                    <input type="number" name="charges" value="{{ old('charges',$serviceStaff->staff->charges ?? "") }}" class="form-control" placeholder="Additional Charges">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <strong>Commission Salary:</strong>
                    <input type="number" name="fix_salary" class="form-control" value="{{ old('fix_salary',$serviceStaff->staff->fix_salary ?? "") }}" placeholder="Commission Salary">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <strong>Minimum Order Value:</strong>
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
        <div class="col-md-12 text-center mt-3">
            <button type="submit" class="btn btn-block btn-primary">Update</button>
        </div>
</div>
</form>
</div>
<style>
    .bg-monday { background-color: #f8d7da; }
    .bg-tuesday { background-color: #d4edda; }
    .bg-wednesday { background-color: #d1ecf1; }
    .bg-thursday { background-color: #fff3cd; }
    .bg-friday { background-color: #cce5ff; }
    .bg-saturday { background-color: #e2e3e5; }
    .bg-sunday { background-color: #f5c6cb; }
</style>
<script>
$(function () {
    const driverOptions = `<option value="">Select Driver</option>
        @foreach ($drivers as $driver)
            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
        @endforeach`;

    const slotOptions = `<option value="">Select Time Slot</option>
        @foreach ($serviceStaff->staffTimeSlots as $slot)
            <option value="{{ $slot['id'] }}">{{ \Carbon\Carbon::parse($slot['time_start'])->format('h:i A') }} - {{ \Carbon\Carbon::parse($slot['time_end'])->format('h:i A') }}</option>
        @endforeach`;

    // Add driver row
    $(document).on("click", ".add-driver", function () {
        const row = $(this).closest("tr");
        const day = row.data("day");
        const index = $(`tr[data-day="${day}"]`).length;

        const driverVal = row.find(".driver-select").val();
        const slotVal = row.find(".slot-select").val();

        if (!driverVal || !slotVal) {
            alert("Please select both Driver and Time Slot before adding.");
            return;
        }

        // Turn current row into an assigned row
        row.find(".add-driver")
            .removeClass("btn-primary add-driver")
            .addClass("btn-danger remove-driver")
            .text("Remove");

        // Insert new empty row for next assignment
        const newRow = $(`
            <tr data-day="${day}">
                <td>
                    <select name="drivers[${day}][${index}][driver_id]" class="form-control driver-select">
                        ${driverOptions}
                    </select>
                </td>
                <td>
                    <select name="drivers[${day}][${index}][time_slot_id]" class="form-control slot-select">
                        ${slotOptions}
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-primary add-driver">Add</button>
                </td>
            </tr>
        `);

        row.after(newRow);
        updateDayRowspan(day);
    });

    // Remove driver row
    $(document).on("click", ".remove-driver", function () {
        const row = $(this).closest("tr");
        const day = row.data("day");

        // If removing leaves only "Remove" rows, make sure there's always an "Add" row
        const rows = $(`tr[data-day="${day}"]`);
        if (rows.length === 1) {
            // replace it with empty Add row
            row.html(`
                <td>
                    <select name="drivers[${day}][0][driver_id]" class="form-control driver-select">
                        ${driverOptions}
                    </select>
                </td>
                <td>
                    <select name="drivers[${day}][0][time_slot_id]" class="form-control slot-select">
                        ${slotOptions}
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-primary add-driver">Add</button>
                </td>
            `);
        } else {
            row.remove();
        }

        updateDayRowspan(day);
    });

    // Update rowspan for the day name cell
    function updateDayRowspan(day) {
        const rows = $(`tr[data-day="${day}"]`);
        const rowspan = rows.length;

        rows.find(".day-name").remove(); // remove old
        if (rowspan > 0) {
            rows.first().prepend(`<td rowspan="${rowspan}" class="day-name">${day}</td>`);
        }
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
@endsection