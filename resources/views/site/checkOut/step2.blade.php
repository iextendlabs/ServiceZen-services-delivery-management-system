@extends('site.layout.app')
<style>
    .list-group-item {
        font-family: 'Helvetica', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        color: #333;
        background-color: #fff;
        border-color: #333;
    }

    .list-group-item:hover {
        background-color: #333;
        color: #fff;
        border-color: #333;
    }

    .list-group-item.active {
        background-color: #b4b3b3 !important;
        color: #fff !important;
        border-color: #b4b3b3 !important;
    }

    .badge-available {
        background-color: #28a745;
    }

    .badge-unavailable {
        background-color: #dc3545;
    }

    .badge {
        color: white
    }
</style>

<base href="/public">
@section('content')
<div class="row">
    <div class="col-md-12 py-5 text-center">
        <h2>Step 2</h2>
    </div>
</div>
<div class="text-center" style="margin-bottom: 20px;">
    @if(Session::has('error'))
    <span class="alert alert-danger" role="alert">
        <strong>{{ Session::get('error') }}</strong>
    </span>
    @endif
    @if(Session::has('success'))
    <span class="alert alert-success" role="alert">
        <strong>{{ Session::get('success') }}</strong>
    </span>
    @endif
</div>
<div class="album bg-light">
    <div class="container">
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
        <form action="timeSlotsSession" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Date:</strong>
                        <input type="date" name="date" id="date" min="{{ date('Y-m-d'); }}" value="{{ date('Y-m-d'); }}" class="form-control" placeholder="Date">
                    </div>
                </div>
                <div class="col-md-8">
                    <strong>Time Slots</strong>
                    <div class="list-group" id="time-slots-container">
                        @if(count($holiday) == 0)
                            @if(count($timeSlots))
                                @foreach($timeSlots as $timeSlot)
                                <label>
                                    <div class="list-group-item d-flex justify-content-between align-items-center time-slot">
                                        <input style="display: none;" type="radio" class="form-check-input" name="time_slot" data-group="{{ $timeSlot->group_id }}" value="{{ $timeSlot->id }}" @if($timeSlot->active == "Unavailable") disabled @endif>
                                        <div>
                                            <h6><b>{{ strtoupper($timeSlot->name) }}</b></h6>
                                            <h6 id="selected_time">{{ date('h:i A', strtotime($timeSlot->time_start)) }} -- {{ date('h:i A', strtotime($timeSlot->time_end)) }} </h6>
                                            @if(isset($timeSlot->space_availability))
                                            <span style="font-size: 13px;">Space Availability:{{ $timeSlot->space_availability }}</span>
                                            @endif
                                        </div>

                                        @if($timeSlot->active == "Unavailable")
                                        <span class="badge badge-unavailable">Unavailable</span>
                                        @else
                                        <span class="badge badge-available">Available</span>
                                        @endif
                                    </div>
                                </label>
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
                    </div>
                </div>

                <div class="col-md-3"><br>
                    <strong>Available Staff</strong>
                    <div class="list-group" id="staff-container">
                        <div class="alert alert-danger">
                            <strong>Please!</strong> First select time slot.
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Time:</strong>
                        <input type="text" name="selected_time" class="form-control" disabled placeholder="Select the Time Slots">
                        <input type="hidden" name="time_slot_id" class="form-control">
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <a href="step1">
                        <button type="button" class="btn btn-primary">Back</button>
                    </a>
                    <button type="submit" class="btn btn-success">Next</button>
                </div>
                
            </div>
        </form>
    </div>
</div>
<script>
    // JavaScript Code
    $('#date').change(function() {
        var selectedDate = $(this).val();

        // Make AJAX call to retrieve time slots for selected date
        $.ajax({
            url: '/slots',
            method: 'GET',
            data: {
                date: selectedDate
            },
            success: function(response) {
                var timeSlots = response;
                if (typeof timeSlots === 'string') {

                    var timeSlotsContainer = $('#time-slots-container');
                    timeSlotsContainer.empty();
                    $('input[name="selected_time"]').val('');
                    $('input[name="time_slot_id"]').val('');

                    var html = '<div class="alert alert-danger"><strong>Whoops!</strong> There were Holiday on your selected date.</div>';
                    timeSlotsContainer.append(html);
                } else {

                    var timeSlotsContainer = $('#time-slots-container');
                    timeSlotsContainer.empty();
                    $('input[name="selected_time"]').val('');
                    $('input[name="time_slot_id"]').val('');

                    timeSlots.forEach(function(timeSlot) {
                        var html = '<label><div class="list-group-item d-flex justify-content-between align-items-center time-slot">';
                        if (timeSlot.active == "Unavailable") {
                            html += '<input style="display: none;" type="radio" class="form-check-input" name="time_slot" data-group="' + timeSlot.group_id + '" value="' + timeSlot.id + '" disabled>';
                        } else {
                            html += '<input style="display: none;" type="radio" class="form-check-input" name="time_slot" data-group="' + timeSlot.group_id + '" value="' + timeSlot.id + '">';
                        }
                        html += '<div><h6><b>' + timeSlot.name.toUpperCase() + '</b></h6>';
                        html += '<h6 id="selected_time">' + convertTo12Hour(timeSlot.time_start) + ' -- ' + convertTo12Hour(timeSlot.time_end) + '</h6>';
                        if (timeSlot.space_availability) {
                            html += '<span style="font-size: 13px;">Space Availability:' + timeSlot.space_availability + '</span>';
                        }
                        html += '</div>';
                        if (timeSlot.active == "Unavailable") {
                            html += '<span class="badge badge-unavailable">Unavailable</span>';
                        } else {
                            html += '</p> <span class="badge badge-available">Available</span>';
                        }
                        html += '</div></label>';
                        timeSlotsContainer.append(html);
                    });
                }
            },
            error: function() {
                alert('Error retrieving time slots.');
            }
        });
    });

    function convertTo12Hour(time) {
        var parts = time.split(':');
        var hours = parseInt(parts[0]);
        var minutes = parseInt(parts[1]);

        var suffix = hours >= 12 ? 'PM' : 'AM';

        hours = hours % 12;
        hours = hours ? hours : 12; // Convert 0 to 12

        var formattedTime = hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0') + ' ' + suffix;

        return formattedTime;
    }
</script>
<script>
    // JavaScript Code
    $('#time-slots-container').on('change', 'input[name="time_slot"]', function() {
        var group = $(this).attr('data-group');
        var time_slot = $(this).val();

        if (group) {
            // Make AJAX call to retrieve time slots for selected date
            $.ajax({
                url: '/staff-group',
                method: 'GET',
                data: {
                    group: group,
                    time_slot: time_slot
                },
                success: function(response) {
                    var staffs = response;
                    console.log(staffs[0]);
                    var staffContainer = $('#staff-container');
                    staffContainer.empty();
                    
                    if(staffs[0].length == 0){
                            var html = '<div class="alert alert-danger"><strong>Whoops!</strong> There is no staff on your select time slot.</div>';
                            staffContainer.append(html);
                    }else{
                        staffs.forEach(function(staff) {
                            var html = '<label><div class="list-group-item d-flex justify-content-between align-items-center staff"><input style="display: none;" type="radio" class="form-check-input" name="service_staff_id" value="' + staff[0].id + '">';
                            html += '<img src="/staff-images/' + staff[0].image + '" height="100px" width="100px" alt="Staff Image" class="rounded-circle">'
                            html += '<span>' + staff[0].name + '</span>'
                            html += '</div></label>'
                            staffContainer.append(html);
                        });
                    }
                    
                },
                error: function() {
                    alert('Error retrieving staffs.');
                }
            });
        } else {
            var timeSlotsContainer = $('#staff-container');
            timeSlotsContainer.empty();

            var html = '<div class="alert alert-danger"><strong>Whoops!</strong> There is no staff on your select time slot.</div>';
            timeSlotsContainer.append(html);
        }

    });
</script>
<script>
    var time_slot = $('input[name="time_slot"]');

    var selected_time = $('input[name="selected_time"]');
    var time = $('input[name="time_slot_id"]');

    $('#time-slots-container').on('change', 'input[name="time_slot"]', function() {

        if ($(this).is(':checked')) {
            $(".time-slot").removeClass("active");
            $(this).parents().addClass('active');
            selected_time.val($(this).parent().find('#selected_time').text());
            time.val($(this).val());
        }
    });

    $('#staff-container').on('change', 'input[name="service_staff_id"]', function() {
        if ($(this).is(':checked')) {
            $(".staff").removeClass("active");
            $(this).parents().addClass('active');
        }
    });
</script>
@endsection