    @if(count($holiday) == 0)
    @if(count($timeSlots))
    @foreach($timeSlots as $timeSlot)
    <div class="row">
        <div class="col-md-12 text-center">
            @if($timeSlot->active == "Unavailable")
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
                    <input style="display: none;" type="radio" id="staff-{{$staff->id}}-{{$timeSlot->id}}" class="form-check-input" name="service_staff_id" data-staff="{{ $staff->name }}" data-slot="{{ date('h:i A', strtotime($timeSlot->time_start)) }} -- {{ date('h:i A', strtotime($timeSlot->time_end)) }}" value="{{ $timeSlot->id }}:{{$staff->id}}" @if($timeSlot->active == "Unavailable") disabled @endif >
                    <label class="staff-label" for="staff-{{$staff->id}}-{{$timeSlot->id}}">
                        <div class="p-2">
                            <img src="/staff-images/{{$staff->staff->image}}" alt="@if($timeSlot->active == " Unavailable") Not Available @endif" class="rounded-circle shadow-image" width="100">
                            <p class="text-center">{{ $staff->name }}</p>
                        </div>
                    </label>

                    @endforeach
                </div>
                <hr>
            </div>
        </div>
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