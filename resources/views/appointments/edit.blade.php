@extends('layouts.app')
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
@section('content')
<div class="row">
    <div class="col-md-12 margin-tb">
        <div class="float-start">
            <h2>Edit Appointment</h2>
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
<form action="{{ route('appointments.update',$appointment->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Date:</strong>
                <input type="date" name="date" id="date" value="{{ $appointment->date }}" class="form-control" placeholder="Date">
            </div>
        </div>
        <div class="col-md-8">
            <strong>Time Slots</strong>
            <div class="list-group" id="time-slots-container">
                @foreach($timeSlots as $timeSlot)
                @php
                $slot = date('h:i A', strtotime($timeSlot->time_start)).' -- '.date('h:i A', strtotime($timeSlot->time_end));
                @endphp
                @if($slot == $appointment->time)
                <label>
                    <div class="list-group-item d-flex justify-content-between align-items-center time-slot active">
                        <input style="display: none;" type="radio" class="form-check-input" name="time_slot" data-group="{{ $timeSlot->group_id }}" value="{{ date('h:i A', strtotime($timeSlot->time_start)) }} -- {{ date('h:i A', strtotime($timeSlot->time_end)) }}" @if($timeSlot->active == "Unavailable") disabled @endif>
                        {{ date('h:i A', strtotime($timeSlot->time_start)) }} -- {{ date('h:i A', strtotime($timeSlot->time_end)) }}
                        @if($timeSlot->active == "Unavailable")
                        <span class="badge badge-unavailable">Unavailable</span>
                        @else
                        <span class="badge badge-available">Available</span>
                        @endif
                    </div>
                </label>
                @else
                <label>
                    <div class="list-group-item d-flex justify-content-between align-items-center time-slot">
                        <input style="display: none;" type="radio" class="form-check-input" name="time_slot" data-group="{{ $timeSlot->group_id }}" value="{{ date('h:i A', strtotime($timeSlot->time_start)) }} -- {{ date('h:i A', strtotime($timeSlot->time_end)) }}" @if($timeSlot->active == "Unavailable") disabled @endif>
                        {{ date('h:i A', strtotime($timeSlot->time_start)) }} -- {{ date('h:i A', strtotime($timeSlot->time_end)) }}
                        @if($timeSlot->active == "Unavailable")
                        <span class="badge badge-unavailable">Unavailable</span>
                        @else
                        <span class="badge badge-available">Available</span>
                        @endif
                    </div>
                </label>
                @endif
                @endforeach
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
                <input type="text" name="selected_time" class="form-control" value="{{ $appointment->time }}" disabled placeholder="Select the Time Slots">
                <input type="hidden" name="time" class="form-control" value="{{ $appointment->time }}">
            </div>
        </div>
        
        <div class="col-md-12">
            <div class="form-group">
                <strong>Status:</strong>
                <select name="status" class="form-control">
                    @foreach ($statuses as $status)
                    @if($status == $appointment->status)
                    <option value="{{ $status }}" selected>{{ $status }}</option>
                    @else
                    <option value="{{ $status }}">{{ $status }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        var group = $('#time-slots-container').find('.active').find('input[name="time_slot"]').attr('data-group');

        if (group) {
            // Make AJAX call to retrieve time slots for selected date
            $.ajax({
                url: '/staff-group',
                method: 'GET',
                data: {
                    group: group
                },
                success: function(response) {
                    var staffs = response;

                    var staffContainer = $('#staff-container');
                    staffContainer.empty();

                    staffs.forEach(function(staff) {
                        var service_staff = {!! json_encode($appointment->service_staff_id) !!};
                        if(staff[0].id == service_staff){
                            var html = '<label><div class="list-group-item d-flex justify-content-between align-items-center staff active"><input style="display: none;" type="radio" class="form-check-input" name="service_staff_id" checked value="' + staff[0].id + '">';
                            html += '<img src="/staff-images/' + staff[0].image + '" height="100px" alt="Staff Image" class="rounded-circle">'
                            html += '<span>' + staff[0].name + '</span>'
                            html += '</div></label>'
                            staffContainer.append(html);
                        }else{
                            var html = '<label><div class="list-group-item d-flex justify-content-between align-items-center staff"><input style="display: none;" type="radio" class="form-check-input" name="service_staff_id" value="' + staff[0].id + '">';
                            html += '<img src="/staff-images/' + staff[0].image + '" height="100px" alt="Staff Image" class="rounded-circle">'
                            html += '<span>' + staff[0].name + '</span>'
                            html += '</div></label>'
                            staffContainer.append(html);
                        }
                        
                    });
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
    var time = $('input[name="time"]');

    $('#time-slots-container').on('change', 'input[name="time_slot"]', function() {
        if ($(this).is(':checked')) {
            $(".time-slot").removeClass("active");
            $(this).parents().addClass('active');
            selected_time.val($(this).val());
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
<script>
    $('#date').change(function() {
        var selectedDate = $(this).val();

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
                    $('input[name="time"]').val('');

                    var html = '<div class="alert alert-danger"><strong>Whoops!</strong> There were Holiday on your selected date.</div>';
                    timeSlotsContainer.append(html);
                } else {

                    var timeSlotsContainer = $('#time-slots-container');
                    timeSlotsContainer.empty();
                    $('input[name="selected_time"]').val('');
                    $('input[name="time"]').val('');

                    timeSlots.forEach(function(timeSlot) {
                        var html = '<label><div class="list-group-item d-flex justify-content-between align-items-center time-slot">';
                        if (timeSlot.active == "Unavailable") {
                            html += '<input style="display: none;" type="radio" class="form-check-input" name="time_slot" data-group="' + timeSlot.group_id + '" value="' + convertTo12Hour(timeSlot.time_start) + ' -- ' + convertTo12Hour(timeSlot.time_end) + '" disabled>' + convertTo12Hour(timeSlot.time_start) + ' -- ' + convertTo12Hour(timeSlot.time_end) + ' <span class="badge badge-unavailable">Unavailable</span>'
                        } else {
                            html += '<input style="display: none;" type="radio" class="form-check-input" name="time_slot" data-group="' + timeSlot.group_id + '" value="' + convertTo12Hour(timeSlot.time_start) + ' -- ' + convertTo12Hour(timeSlot.time_end) + '">' + convertTo12Hour(timeSlot.time_start) + ' -- ' + convertTo12Hour(timeSlot.time_end) + '<span class="badge badge-available">Available</span> '
                        }
                        html += '</div></label>'
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
    $('#time-slots-container').on('change', 'input[name="time_slot"]', function() {
        var group = $(this).attr('data-group');
        if (group) {
            $.ajax({
                url: '/staff-group',
                method: 'GET',
                data: {
                    group: group
                },
                success: function(response) {
                    var staffs = response;

                    var staffContainer = $('#staff-container');
                    staffContainer.empty();

                    staffs.forEach(function(staff) {
                        var html = '<label><div class="list-group-item d-flex justify-content-between align-items-center staff"><input style="display: none;" type="radio" class="form-check-input" name="service_staff_id" value="' + staff[0].id + '">';
                        html += '<img src="/staff-images/' + staff[0].image + '" height="100px" alt="Staff Image" class="rounded-circle">'
                        html += '<span>' + staff[0].name + '</span>'
                        html += '</div></label>'
                        staffContainer.append(html);
                    });
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
@endsection