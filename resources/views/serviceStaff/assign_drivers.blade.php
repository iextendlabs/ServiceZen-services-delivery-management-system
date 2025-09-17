@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Service Staff Assign Drivers</h2>
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
    <form action="{{ route('serviceStaff.assign-drivers.update',$serviceStaff->id) }}" method="POST" enctype="multipart/form-data">
        <input type="hidden" value="{{ $serviceStaff->staff->id ?? '' }}" name="staff_id">
        @csrf
        @method('POST')
        <input type="hidden" name="url" value="{{ url()->previous() }}">
        <div class="tab-pane fade show active" id="assign-drivers" role="tabpanel" aria-labelledby="assign-drivers-tab">
            <div class="row">
                <div class="col-md-12">
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
            </div>
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
@endsection