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
                @if(!in_array($staff->id, $staff_ids) && !in_array($staff->id, $timeSlot->excluded_staff))
                <input style="display: none;" type="radio" id="staff-{{$staff->id}}-{{$timeSlot->id}}" class="form-check-input" name="service_staff_id" data-staff="{{ $staff->name }}" data-slot="{{ date('h:i A', strtotime($timeSlot->time_start)) }} -- {{ date('h:i A', strtotime($timeSlot->time_end)) }}" value="{{ $timeSlot->id }}:{{$staff->id}}" @if(isset($order) && $order->service_staff_id == $staff->id && $order->time_slot_id == $timeSlot->id ) checked @endif >
                <label class="staff-label" for="staff-{{$staff->id}}-{{$timeSlot->id}}">
                    <div class="p-2">
                        <img src="/staff-images/{{$staff->staff->image}}" alt="@if(!$timeSlot->space_availability > 0) Not Available @endif" class="rounded-circle shadow-image" width="100">
                        <p class="text-center">{{ $staff->name }}</p>
                    </div>
                </label>
                @endif
                @endforeach
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
    <li>{{ $zone->name }}</li>
    @endforeach
</ul>
<div class="mt-3">
    <button type="button" class="btn btn-primary" onclick="$('#locationPopup').modal('show')">Change Zone</button>
</div>
@endif

