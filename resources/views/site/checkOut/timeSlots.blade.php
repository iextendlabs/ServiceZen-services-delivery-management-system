    @if(count($holiday) == 0)
    @if(count($timeSlots))
    @foreach($timeSlots as $timeSlot)
    <div class="list-group-item d-flex justify-content-between align-items-center time-slot">
        <!-- <input style="display: none;" type="radio" class="form-check-input" name="time_slot" data-group="{{ $timeSlot->group_id }}" value="{{ $timeSlot->id }}" @if($timeSlot->active == "Unavailable") disabled @endif> -->
        <div>
            <h6 id="selected_time">{{ date('h:i A', strtotime($timeSlot->time_start)) }} -- {{ date('h:i A', strtotime($timeSlot->time_end)) }} </h6>
            @if(isset($timeSlot->space_availability))
            <span style="font-size: 13px;">Space Availability:{{ $timeSlot->space_availability }}</span>
            @endif
            <div class="col">
                <div class="d-flex flex-row">
                    @foreach($timeSlot->staffs as $staff)
                    <input style="display: none;" type="radio" id="staff-{{$staff->id}}-{{$timeSlot->id}}" class="form-check-input" name="service_staff_id" data-staff="{{ $staff->name }}" data-slot="{{ date('h:i A', strtotime($timeSlot->time_start)) }} -- {{ date('h:i A', strtotime($timeSlot->time_end)) }}" value="{{ $timeSlot->id }}:{{$staff->id}}" @if($timeSlot->active == "Unavailable") disabled @endif >
                    <label class="staff-label" for="staff-{{$staff->id}}-{{$timeSlot->id}}">
                        <div class="p-2">
                            <img src="/staff-images/{{$staff->staff->image}}" alt="Staff 1" class="rounded-circle" width="100">
                            <p class="text-center">{{ $staff->name }}</p>
                        </div>
                    </label>

                    @endforeach
                </div>
            </div>
        </div>

        @if($timeSlot->active == "Unavailable")
        <span class="badge badge-unavailable">Unavailable</span>
        @else
        <span class="badge badge-available">Available</span>
        @endif
    </div>
    @endforeach
    @else
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were no time slot available in your selected zone.
    </div>
    @endif
    @else
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were Holiday on your selected date.
    </div>
    @endif