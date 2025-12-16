<div class="row mb-4">
    <div class="col-12 col-md-6 mb-3">
        <label class="form-label">Date</label>
        <input required type="date" name="date" id="date" @if(!auth()->user() || !auth()->user()->hasRole('Admin')) min="{{ date('Y-m-d') }}" @endif value="{{ isset($selected_booking) && isset($selected_booking['date']) ? $selected_booking['date'] : (isset($date) ? $date : date('Y-m-d')) }}" class="form-control" placeholder="Date">
    </div>
    <div class="col-12 col-md-6 mb-3" @if(isset($zoneShow) && $zoneShow == 0) style="display:none;"  @endif>
        <label class="form-label">Zone</label>
        <select name="zone" id="zone" class="form-select" required>
            <option value=""></option>
            @foreach($allZones as $zone)
            <option value="{{ $zone->name }}" data-transport-charges="{{ isset($staffZone) &&  $staffZone->transport_charges ? $staffZone->transport_charges : 0 }}" @if(isset($staffZone) && $staffZone && $zone->id == $staffZone->id) selected @endif>
                {{ $zone->name }}
            </option>
            @endforeach
        </select>
    </div>
</div>
<style>
    /* Modal slot styles */
    .slot-card {
        border: 0;
        box-shadow: 0 2px 8px rgba(16,24,40,0.06);
        border-radius: 8px;
        overflow: hidden;
    }

    .slot-header {
        background: linear-gradient(90deg,#e6f8ef,#ffffff);
        padding: 8px 16px;
        display:flex;
        align-items:center;
        justify-content:space-between;
    }

    .availability-bar {
        height:8px;
        background:#1abc9c;
        border-radius:4px;
        width:100%;
        display:block;
    }

    .slot-body { padding: 18px; }

    .staff-card {
        cursor: pointer;
        border-radius:8px;
        transition: box-shadow .15s ease, transform .08s ease;
        margin-bottom: 12px;
    }

    .staff-card:hover,
    .staff-card:focus {
        box-shadow: 0 6px 18px rgba(16,24,40,0.12);
        transform: translateY(-2px);
    }

    .staff-avatar { width:72px; height:72px; object-fit:cover; border-radius:50%; border:1px solid #eee; }

    .staff-info { margin-left:14px; }

    .slot-time { font-weight:600; color:#111827; }

    .slot-meta { color:#6b7280; font-size:13px }

    .staff-details small { color:#6b7280; }

    .staff-card .extra-charges { font-size:13px; color:#374151; }

    .staff-selection-radio { display:none; }

    .staff-card-selected { outline: 2px solid #7c3aed; }

    @media (max-width:767px) {
        .staff-info { margin-left:10px; }
    }
</style>
<div class="text-center mb-4">
    <h3 class="h5">Available Staff based on Zone and Date Selected</h3>
</div>
<div class="col-12 text-center">
    @php
    $staff_displayed = [];
    $staff_slots = [];
    @endphp
    @foreach($timeSlots as $timeSlot)
    @php
    $staff_counter = 0;
    $holiday_counter = 0;
    $booked_counter = 0;
    @endphp
    @foreach($timeSlot->staffs as $staff)
    @auth
    @if((auth()->user()->hasRole("Staff")  && $staff->id != auth()->user()->id))
    @continue
    @endif
    @if(auth()->user()->hasRole("Supervisor")  && !in_array($staff->id, auth()->user()->getSupervisorStaffIds()))
    @continue
    @endif
    @if(auth()->user()->hasRole("Manager")  && !in_array($staff->id, auth()->user()->getManagerStaffIds()))
    @continue
    @endif

    @endauth

    @if(!in_array($staff->id, $staff_ids))
    @php
    $booked_counter ++;
    @endphp

    @endif


    @if(!in_array($staff->id, $timeSlot->excluded_staff))
    @php
    $holiday_counter ++
    @endphp

    @endif


    @if(!in_array($staff->id, $staff_ids) && !in_array($staff->id, $timeSlot->excluded_staff))
    @php
    $staff_counter ++;
    $current_slot = [$timeSlot->id,  date('h:i A', strtotime($timeSlot->time_start)).'-- '.date('h:i A', strtotime($timeSlot->time_end)),$timeSlot->id];

    if (isset($staff_slots[$staff->id])) {
        array_push($staff_slots[$staff->id], $current_slot);
    } else {
        $staff_slots[$staff->id] = [$current_slot];
    }
    @endphp
    @if (!in_array($staff->id, $staff_displayed))
    @php 
    $staff_displayed[] = $staff->id;
    @endphp
        <input required class="d-none" onchange="$('.staff-time-drop').hide().removeAttr('required');$('#staff-time-{{$staff->id}}').show().attr('required',true)" type="radio" id="staff-{{$staff->id}}" name="service_staff_id" data-staff="{{ $staff->name }}" data-staff-charges="{{ $staff->staff->charges ? $staff->staff->charges : 0 }}" data-serviceIds='@json($staff->services ? $staff->services->pluck('id') : [])' data-categoryIds='@json($staff->categories ? $staff->categories->pluck('id') : [])' value="{{$staff->id}}" @if(isset($order) && $order->service_staff_id == $staff->id) checked @elseif(isset($selected_booking) && $selected_booking['service_staff_id'] == $staff->id) checked @endif >
        <label class="staff-card card p-3 d-flex align-items-center" for="staff-{{$staff->id}}">
            <img src="/staff-images/{{$staff->staff->image}}" alt="@if(!$timeSlot->space_availability > 0) Not Available @endif" class="staff-avatar" />
            <div class="staff-info d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between" style="min-width:200px">
                    <div>
                        <div class="fw-bold">{{ $staff->name }}</div>
                        <div class="small text-muted">{{ $staff->subTitles->pluck('name')->implode('/') }}</div>
                    </div>
                    @if($staff->staff->charges)
                        <div class="extra-charges">+ @currency($staff->staff->charges,$isAdmin)</div>
                    @endif
                </div>
                <div class="d-flex align-items-center mt-2">
                    @php
                        $rating = $staff->averageRating();
                        $fullStars = floor($rating);
                        $halfStar = $rating - $fullStars >= 0.5;
                        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                    @endphp
                    <div class="me-2">
                        @for ($i = 0; $i < $fullStars; $i++)
                            <i class="fas fa-star text-warning"></i>
                        @endfor
                        @if ($halfStar)
                            <i class="fas fa-star-half-alt text-warning"></i>
                        @endif
                        @for ($i = 0; $i < $emptyStars; $i++)
                            <i class="far fa-star text-muted"></i>
                        @endfor
                    </div>
                    <div class="small text-muted">({{ count($staff->reviews)}} Reviews)</div>
                </div>
            </div>
        </label>
    @endif
    @endif
    @endforeach
    @endforeach
    @if(count($staff_displayed ) == 0)
        <div class="rounded-md bg-red-50 p-4 text-center">No Staff Available</div>
    @endif
    <hr class="my-4">
    <h3 class="h5">Available Time Slot for Selected Staff</h3>
    <div class="mt-3"> 
        @if(count($staff_slots ) == 0)
            <div class="small text-muted">No Staff Available for the Selected Date / Zone</div>
        @endif
        @foreach($staff_slots as $staff_id=>$staff_single_slot)
        <select @if(isset($order) && $staff_id == $order->service_staff_id) style="display: block"  @elseif(isset($selected_booking) && $staff_id == $selected_booking['service_staff_id']) style="display: block"  @else  style="display: none"  @endif class="form-select mt-2 staff-time-drop" name="time_slot_id[{{$staff_id}}]" id="staff-time-{{$staff_id}}">
            <option value="">Select Slot</option>
            @foreach($staff_single_slot as $staff_single_values)
            <option value="{{$staff_single_values[2]}}" @if(isset($order)  && $order->time_slot_id == $staff_single_values[2]) selected @elseif(isset($selected_booking)  && $selected_booking['time_slot_id'] == $staff_single_values[2]) selected @endif>{{$staff_single_values[1]}}</option>
            @endforeach
        </select>
        @endforeach
    </div>
</div>
<br>
<div class="text-center mt-8">
    <h3 class="text-lg font-medium">All Slot Data</h3>
    <button onclick="$('#scheduleInfo,#showBtn').toggle()" id="showBtn" type="button" class="mt-3 inline-flex items-center px-4 py-2 border rounded-md">Show</button>
</div>
<div class="mt-4" id="scheduleInfo" style="display: none">
    <strong class="small text-muted">Time Slots : {{ isset($order) ? $order->area : $area }}</strong>
    <input type="hidden" name="order_id" value="{{ isset($order) ? $order->id : null }}">
    <div id="time-slots-container row" class="space-y-4">
        @if(isset($staffZone))
        @if(!count($holiday))
        @if(count($timeSlots))
        @foreach($timeSlots as $timeSlot)
            <div class="card col-md-12 mb-3 p-3">
                @if(!$timeSlot->space_availability > 0 )
                    <span class="badge bg-danger">Unavailable</span>
                @else
                    <span class="badge bg-success">Available</span>
                @endif

                <h4 id="selected_time" class="mt-3"><i class="fa fa-clock me-2"></i> {{ date('h:i A', strtotime($timeSlot->time_start)) }} -- {{ date('h:i A', strtotime($timeSlot->time_end)) }} </h4>
                @if(isset($timeSlot->space_availability))
                    <div class="small text-muted">Space Availability: {{ $timeSlot->space_availability }}</div>
                @endif
                <div class="mt-3">
                    @php
                    $staff_counter = 0;
                    $holiday_counter = 0;
                    $booked_counter = 0;
                    @endphp
                    @foreach($timeSlot->staffs as $staff)
                    @auth
                    @if((auth()->user()->hasRole("Staff")  && $staff->id != auth()->user()->id))
                    @continue
                    @endif
                    @if(auth()->user()->hasRole("Supervisor")  && !in_array($staff->id, auth()->user()->getSupervisorStaffIds()))
                    @continue
                    @endif
                    @if(auth()->user()->hasRole("Manager")  && !in_array($staff->id, auth()->user()->getManagerStaffIds()))
                    @continue
                    @endif

                    @endauth

                    @if(!in_array($staff->id, $staff_ids))
                    @php
                    $booked_counter ++
                    @endphp

                    @endif


                    @if(!in_array($staff->id, $timeSlot->excluded_staff))
                    @php
                    $holiday_counter ++
                    @endphp

                    @endif


                    @if(!in_array($staff->id, $staff_ids) && !in_array($staff->id, $timeSlot->excluded_staff))
                    @php
                    $staff_counter ++
                    @endphp
                    <label onclick="$('#staff-time-{{$staff->id}}').val('{{$timeSlot->id}}')" class="staff-card card p-2 d-flex align-items-center" for="staff-{{$staff->id}}">
                        <img src="/staff-images/{{$staff->staff->image}}" alt="@if(!$timeSlot->space_availability > 0) Not Available @endif" class="staff-avatar" />
                        <div class="staff-info d-flex flex-column">
                            <div class="fw-medium">{{ $staff->name }}</div>
                            <div class="small text-muted">{{ $staff->subTitles->pluck('name')->implode('/') }}</div>
                            @if($staff->staff->charges)<div class="extra-charges mt-1">Extra: <span class="fw-medium">@currency($staff->staff->charges,$isAdmin)</span></div>@endif
                            <div class="mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $staff->averageRating())
                                        <span class="text-warning">&#9733;</span>
                                    @else
                                        <span class="text-muted">&#9734;</span>
                                    @endif
                                @endfor
                            </div>
                            <div class="small text-muted">({{ count($staff->reviews)}} Reviews)</div>
                        </div>
                    </label>
                    @endif
                    @endforeach
                    @if($staff_counter == 0)
                    <div class="p-3 bg-light rounded">
                        @if( auth()->user() && (auth()->user()->hasRole("Supervisor")  || auth()->user()->hasRole("Manager") ))
                        <strong>Whoops!</strong> All of Your Staff is Booked.
                        @else
                        <strong>Whoops!</strong> No Staff Available!
                        @endif
                        <div class="small text-muted mt-2">( On Holiday : {{$holiday_counter}}) ( On Booking : {{$booked_counter}})</div>
                    </div>
                    @endif
                    <hr class="mt-4">
                </div>
            </div>
        @endforeach
        @else
        <div class="alert alert-danger">
            <strong>Whoops!</strong>No Staff Available in Your Area.
        </div>
        @endif
        @else
        <div class="alert alert-danger">
            <strong>Whoops!</strong>Holiday on selected date. Please choose another date.
        </div>
        @endif

        @else
        <div class="alert alert-danger">
            <strong>Whoops!</strong>Service Unavailable in Your Area.
        </div>
        <h3>Available Zone</h3>
        <ul>
            @foreach($allZones as $zone)
            <li><a href="/updateZone?zone={{ $zone->name }}">{{ $zone->name }}</a></li>

            @endforeach
        </ul>
        <div class="mt-3">
            <button type="button" class="btn btn-primary" onclick="$('#locationPopup').modal('show')">Change Zone</button>
        </div>
        @endif
    </div>
</div>
<script>
    $('[name=service_staff_id]:checked').length === 0 && $('[name=service_staff_id]').first().attr('checked', true).trigger('change');
</script>