@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row align-items-center mb-4">
        <div class="col-md-4 margin-tb">
            <h2>Service Staff Time Slots</h2>
        </div>
        <div class="col-md-8 mt-2 mt-md-0">
            <div class="d-flex flex-wrap justify-content-md-end gap-2">
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.general', $serviceStaff->id) }}">
                    General
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.zones', $serviceStaff->id) }}">
                    Zones
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.social-links', $serviceStaff->id) }}">
                    Social Links
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.gallery', $serviceStaff->id) }}">
                    Gallery
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.categories-and-services', $serviceStaff->id) }}">
                     Categories & Services
                </a>
                <a class="btn btn-outline-primary px-4 py-2 shadow-sm"
                href="{{ route('serviceStaff.documents', $serviceStaff->id) }}">
                     Documents
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
    <form action="{{ route('serviceStaff.time-slots.update',$serviceStaff->id) }}" method="POST" enctype="multipart/form-data">
        <input type="hidden" value="{{ $serviceStaff->staff->id ?? '' }}" name="staff_id">
        @csrf
        @method('POST')
        <input type="hidden" name="url" value="{{ url()->previous() }}">
        <div class="tab-pane fade show active" id="slot" role="tabpanel" aria-labelledby="slot-tab">
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Time Slots</h5>
                            <div class="form-group">
                                <input type="text" id="timeSlotSearch" class="form-control" placeholder="Search time slots...">
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="startTimeFilter" placeholder="Start time (e.g. 9:00 AM)">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="endTimeFilter" placeholder="End time (e.g. 5:00 PM)">
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-sm btn-secondary w-100" id="clearTimeFilters">Clear</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            <div class="mb-2">
                                <button type="button" id="addSlotBtn" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> Add New Time Slot
                                </button>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="50px">
                                            <input type="checkbox" id="selectAllTimeSlots">
                                        </th>
                                        <th>Name</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th width="50px">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="timeSlotTable">
                                    @foreach($timeSlots as $timeSlot)
                                    <tr class="time-slot-row">
                                        <td>
                                            <input type="checkbox" name="time_slots[]" class="time-slot-checkbox" 
                                                value="{{ $timeSlot->id }}" {{ in_array($timeSlot->id, old('time_slots', $serviceStaff->staffTimeSlots->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
                                        </td>
                                        <td class="slot-name">{{ $timeSlot->name }}</td>
                                        <td class="start-time">{{ \Carbon\Carbon::parse($timeSlot->time_start)->format('h:i A') }}</td>
                                        <td class="end-time">{{ \Carbon\Carbon::parse($timeSlot->time_end)->format('h:i A') }}</td>
                                        <td></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 text-center mt-3">
            <button type="submit" class="btn btn-block btn-primary">Update</button>
        </div>
    </div>
</form>
</div>
<script>
    $(document).ready(function() {
        // Time Slot Search and Filter
        $('#timeSlotSearch, #startTimeFilter, #endTimeFilter').on('keyup', function() {
            filterTimeSlots();
        });

        $('#clearTimeFilters').click(function(e) {
            e.preventDefault();
            $('#timeSlotSearch').val('');
            $('#startTimeFilter').val('');
            $('#endTimeFilter').val('');
            filterTimeSlots();
        });

        function filterTimeSlots() {
            const nameSearch = $('#timeSlotSearch').val().toLowerCase();
            const startTime = $('#startTimeFilter').val().toLowerCase();
            const endTime = $('#endTimeFilter').val().toLowerCase();

            $('.time-slot-row').each(function() {
                const $row = $(this);
                const name = $row.find('.slot-name').text().toLowerCase();
                const start = $row.find('.start-time').text().toLowerCase();
                const end = $row.find('.end-time').text().toLowerCase();

                const nameMatch = name.includes(nameSearch);
                const startMatch = startTime ? start.includes(startTime) : true;
                const endMatch = endTime ? end.includes(endTime) : true;

                $row.toggle(nameMatch && startMatch && endMatch);
            });
            
            $('#selectAllTimeSlots').prop('checked', false);
        }
    });
</script>
<script>
$(document).ready(function() {
    function sortCheckedToTop(tableId, checkboxClass) {
        const $table = $(tableId);
        const $rows = $table.find('tr');
        
        $rows.sort(function(a, b) {
            const aChecked = $(a).find(checkboxClass).is(':checked');
            const bChecked = $(b).find(checkboxClass).is(':checked');
            
            if (aChecked && !bChecked) return -1;
            if (!aChecked && bChecked) return 1;
            return 0;
        });
        
        $table.append($rows);
    }

    sortCheckedToTop('#timeSlotTable', '.time-slot-checkbox');
});
</script>

<script>
    $(document).ready(function() {
        // Add new time slot row
        $('#addSlotBtn').click(function() {
            const newSlotCount = $('.new-time-slot').length;
            const newRow = $(`
                <tr class="time-slot-row new-time-slot">
                    <td>
                        <input type="checkbox" class="time-slot-checkbox" disabled>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span style="color: red; margin-right: 4px;">*</span>
                            <input type="text" required name="new_time_slots[${newSlotCount}][name]" class="form-control form-control-sm new-slot-name" placeholder="Slot name">
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span style="color: red; margin-right: 4px;">*</span>
                            <input type="time" required name="new_time_slots[${newSlotCount}][time_start]" class="form-control form-control-sm new-start-time">
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span style="color: red; margin-right: 4px;">*</span>
                            <input type="time" required name="new_time_slots[${newSlotCount}][time_end]" class="form-control form-control-sm new-end-time">
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-time-slot-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
            
            $('#timeSlotTable').prepend(newRow);
            
            // Add event listener to remove button
            newRow.find('.remove-time-slot-btn').click(function() {
                newRow.remove();
                // Re-index remaining time slots if needed
                $('.new-time-slot').each(function(index) {
                    $(this).find('[name^="new_time_slots"]').each(function() {
                        const name = $(this).attr('name').replace(/\[\d+\]/, `[${index}]`);
                        $(this).attr('name', name);
                    });
                });
            });
        });

        // Search functionality
        $('#timeSlotSearch').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.time-slot-row').each(function() {
                const $row = $(this);
                const name = $row.find('.slot-name').text().toLowerCase() || 
                            $row.find('.new-slot-name').val().toLowerCase();
                $row.toggle(name.includes(searchTerm));
            });
        });

        // Time filter functionality
        function filterByTime() {
            const startFilter = $('#startTimeFilter').val().toLowerCase();
            const endFilter = $('#endTimeFilter').val().toLowerCase();
            
            $('.time-slot-row').each(function() {
                const $row = $(this);
                const startTime = $row.find('.start-time').text().toLowerCase() || 
                                $row.find('.new-start-time').val().toLowerCase();
                const endTime = $row.find('.end-time').text().toLowerCase() || 
                                $row.find('.new-end-time').val().toLowerCase();
                
                const matchesStart = !startFilter || (startTime && startTime.includes(startFilter));
                const matchesEnd = !endFilter || (endTime && endTime.includes(endFilter));
                
                $row.toggle(matchesStart && matchesEnd);
            });
        }
        
        $('#startTimeFilter, #endTimeFilter').on('input', filterByTime);
        
        // Select all functionality
        $('#selectAllTimeSlots').change(function() {
            const isChecked = $(this).prop('checked');
            $('.time-slot-checkbox:not(:disabled)').prop('checked', isChecked);
        });
    });
</script>
@endsection