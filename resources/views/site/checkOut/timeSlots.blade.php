<div class="col-md-6 offset-md-3 col-sm-12">
    <div class="form-group">
        <strong>Date:</strong>
        <input required type="date" name="date" id="date" min="{{ date('Y-m-d') }}" value="{{ 
            isset($selected_booking) && isset($selected_booking['date']) 
            ? $selected_booking['date'] 
            : (isset($date) ? $date : date('Y-m-d')) 
        }}" class="form-control" placeholder="Date">
    </div>
</div>
<div class="col-md-12" @if(isset($zoneShow) && $zoneShow == 0) style="display:none;"  @endif>
    <div class="form-group">
        <strong>Zone:</strong>
        <select name="zone" id="zone" class="form-control" required>
            <option value=""></option>
            @foreach($allZones as $zone)
            <option value="{{ $zone->name }}" data-transport-charges="{{ isset($staffZone) &&  $staffZone->transport_charges ? $staffZone->transport_charges : 0 }}"
                @if(isset($staffZone) && $staffZone && $zone->id == $staffZone->id)
                selected
                @endif
                >
                {{ $zone->name }}
            </option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-md-12 text-center">
    <h3>Available Staff based on Zone and Date Selected!</h3>
</div>
<div class="col-md-12 text-center">
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
        <input required style="display: none;" onchange="$('.staff-time-drop').hide().removeAttr('required');$('#staff-time-{{$staff->id}}').show().attr('required',true)" type="radio" id="staff-{{$staff->id}}" class="form-check-input" name="service_staff_id" data-staff="{{ $staff->name }}" data-staff-charges="{{ $staff->staff->charges ? $staff->staff->charges : 0 }}" value="{{$staff->id}}" @if(isset($order) && $order->service_staff_id == $staff->id) checked @elseif(isset($selected_booking) && $selected_booking['service_staff_id'] == $staff->id) checked @endif >
        <label class="staff-label" for="staff-{{$staff->id}}">
            <div class="p-2">
                <img src="/staff-images/{{$staff->staff->image}}" alt="@if(!$timeSlot->space_availability > 0) Not Available @endif" class="rounded-circle shadow-image" width="100"><br>
                <span class="text-center">{{ $staff->name }}</span><br>
                <span class="text-center">{{ $staff->staff->sub_title }}</span><br>
                @if($staff->staff->charges)<span class="card-title">Extra Charges:<b>@currency($staff->staff->charges)</b></span>@endif <br>
                @for($i = 1; $i <= 5; $i++)
                @if($i <=$staff->averageRating())
                    <span class="text-warning">&#9733;</span>
                @else
                    <span class="text-muted">&#9734;</span>
                @endif
                @endfor
                <br>
                ({{ count($staff->reviews)}} Reviews)
            </div>
        </label>
    @endif
    @endif
    @endforeach
    @endforeach
    @if(count($staff_displayed ) == 0)
        <div class="alert alert-danger">
            No Staff Available
        </div>
    @endif
    <hr>
    <h3>Available Time Slot for Selected Staff</h3>
    <div class="row" > 
        @if(count($staff_slots ) == 0)
        No Staff Availalbe for the Selected Date / Zone
        @endif
        @foreach($staff_slots as $staff_id=>$staff_single_slot)
        <select @if(isset($order) && $staff_id == $order->service_staff_id) style="display: block"  @elseif(isset($selected_booking) && $staff_id == $selected_booking['service_staff_id']) style="display: block"  @else  style="display: none"  @endif class="form-control staff-time-drop" name="time_slot_id[{{$staff_id}}]" id="staff-time-{{$staff_id}}">
            <option value="">Select Slot</option>
            @foreach($staff_single_slot as $staff_single_values)
            <option value="{{$staff_single_values[2]}}" @if(isset($order)  && $order->time_slot_id == $staff_single_values[2]) selected @elseif(isset($selected_booking)  && $selected_booking['time_slot_id'] == $staff_single_values[2]) selected @endif>{{$staff_single_values[1]}}</option>
            @endforeach
        </select>
        @endforeach
    </div>
</div>
<br>
<div class="col-12 text-center">
    <h3 class="text-center">All Slot Data</h3>
    <button onclick="$('#scheduleInfo,#showBtn').toggle()" id="showBtn" type="button" class="btn btn-outline-primary">Show</button>
</div>
<div class="col-md-12 scroll-div" id="scheduleInfo" style="display: none">
    <strong>Time Slots : {{ isset($order) ? $order->area : $area }}</strong>
    <input type="hidden" name="order_id" value="{{ isset($order) ? $order->id : null }}">
    <div class="list-group" id="time-slots-container">
        @if(isset($staffZone))
        @if(!count($holiday))
        @if(count($timeSlots))
        @foreach($timeSlots as $timeSlot)
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
                    <label onclick="$('#staff-time-{{$staff->id}}').val('{{$timeSlot->id}}')" class="staff-label staff-only" for="staff-{{$staff->id}}">
                        <div class="p-2">
                            <img src="/staff-images/{{$staff->staff->image}}" alt="@if(!$timeSlot->space_availability > 0) Not Available @endif" class="rounded-circle shadow-image" width="100"><br>
                            <span class="text-center">{{ $staff->name }}</span><br>
                            <span class="text-center">{{ $staff->staff->sub_title }}</span><br>
                            @if($staff->staff->charges)<span class="card-title">Extra Charges:<b>@currency($staff->staff->charges)</b></span>@endif <br>
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
                        @if( auth()->user() && (auth()->user()->hasRole("Supervisor")  || auth()->user()->hasRole("Manager") ))
                        <strong>Whoops!</strong>All of Your Staff is Booked.
                        @else
                        <strong>Whoops! </strong> No Staff Available! 
                        @endif
                        ( On Holiday : {{$holiday_counter}})

                        ( On Booking : {{$booked_counter}})
                    </div>
                    @endif
                    <hr>
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