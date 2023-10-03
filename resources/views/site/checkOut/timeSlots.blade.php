<div class="col-md-12">
    <div class="form-group">
        <strong>Date:</strong>
        <input required type="date" name="date" id="date" min="{{ date('Y-m-d') }}" value="{{ isset($date) ? $date : date('Y-m-d') }}" class="form-control" placeholder="Date">


    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
        <strong>Zone:</strong>
        <select name="zone" id="zone" class="form-control">
            <option value=""></option>
            @foreach($allZones as $zone)
            <option value="{{ $zone->name }}" data-transport-charges="{{ isset($staffZone) &&  $staffZone->transport_charges ? $staffZone->transport_charges : 0 }}" {{  $area == $zone->name ? 'selected' : '' }}>
                {{ $zone->name }}
            </option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-md-12 scroll-div">
    <strong>Time Slots : {{ isset($order) ? $order->area : $area }}</strong>
    <!-- <input type="hidden" name="area" value="{{ isset($order) ? $order->area : $area }}"> -->
    <input type="hidden" name="order_id" value="{{ isset($order) ? $order->id : null }}">
    <div class="list-group" id="time-slots-container">
        @if(isset($staffZone))
        @if(!count($holiday))
        @if(count($timeSlots))
        @foreach($timeSlots as $timeSlot)
        <div class="row">
            <div class="col-md-12 text-center">
                @if(!$timeSlot->space_availability > 0 )
                <p class="badge badge-unavailable">Unavailable</p>
                @else
                <p class="badge badge-available">Available</p>
                @endif

                <h4 id="selected_time"><i class="fa fa-clock"></i> {{ date('h:i A', strtotime($timeSlot->time_start)) }} -- {{ date('h:i A', strtotime($timeSlot->time_end)) }} </h4>
                @if(isset($timeSlot->space_availability))
                <span style="font-size: 13px;">Space Availability:{{ $timeSlot->space_availability }}</span>
                @endif
                <div class="col">
                    <div class="d-flex flex-row">
                        @php
                        $staff_counter = 0;
                        $holiday_counter = 0;
                        $booked_counter = 0;
                        @endphp
                        @foreach($timeSlot->staffs as $staff)
                        @auth
                        @if((auth()->user()->getRoleNames() == '["Staff"]' && $staff->id != auth()->user()->id))
                        @continue
                        @endif
                        @if(auth()->user()->getRoleNames() == '["Supervisor"]' && !in_array($staff->id, auth()->user()->getSupervisorStaffIds()))
                        @continue
                        @endif
                        @if(auth()->user()->getRoleNames() == '["Manager"]' && !in_array($staff->id, auth()->user()->getManagerStaffIds()))
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
                        <input style="display: none;" type="radio" id="staff-{{$staff->id}}-{{$timeSlot->id}}" class="form-check-input" name="service_staff_id" data-staff="{{ $staff->name }}" data-staff-charges="{{ $staff->staff->charges ? $staff->staff->charges : 0 }}" data-slot="{{ date('h:i A', strtotime($timeSlot->time_start)) }} -- {{ date('h:i A', strtotime($timeSlot->time_end)) }}" value="{{ $timeSlot->id }}:{{$staff->id}}" @if(isset($order) && $order->service_staff_id == $staff->id && $order->time_slot_id == $timeSlot->id ) checked @endif >
                        <label class="staff-label" for="staff-{{$staff->id}}-{{$timeSlot->id}}">
                            <div class="p-2">
                                <img src="/staff-images/{{$staff->staff->image}}" alt="@if(!$timeSlot->space_availability > 0) Not Available @endif" class="rounded-circle shadow-image" width="100">
                                <p class="text-center">{{ $staff->name }}</p>
                                @for($i = 1; $i <= 5; $i++) @if($i <=$staff->averageRating()) <span class="text-warning">&#9733;</span>
                                    @else
                                    <span class="text-muted">&#9734;</span>
                                    @endif
                                    @endfor
                                    <br>
                                    ({{ count($staff->reviews)}} Reviews)
                            </div>
                        </label>
                        @endif
                        @endforeach
                        @if($staff_counter == 0)
                        <div class="alert alert-danger">
                            @if( auth()->user() && (auth()->user()->getRoleNames() == '["Supervisor"]' || auth()->user()->getRoleNames() == '["Manager"]'))
                            <strong>Whoops!</strong>All of Your Staff is Booked.
                            @else
                            <strong>Whoops!</strong>Staff is Booked Already for this slot.
                            @endif
                            ( On Holiday : {{$holiday_counter}})

                            ( On Booking : {{$booked_counter}})
                        </div>
                        @endif
                    </div>
                    <hr>
                </div>
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
<div class="col-md-12">
    <div class="form-group" id="detail-container">
        <strong>Selected Time Slot:</strong><span id="selected-time-slot">
            @if(isset($order))
            @if(isset($order->time_slot))
            {{ $order->time_slot_value }}
            @endif
            @endif
        </span>
        <br>
        <strong>Selected Staff:</strong><span id="selected-staff">{{ isset($order) ? $order->staff_name : null }}</span>
    </div>
</div>