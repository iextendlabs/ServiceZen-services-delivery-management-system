@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <h2>Service Staff ({{ $total_staff }})</h2>
                <div class="d-flex">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Bulk Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                    data-bs-target="#assignTimeSlotsModal">Assign Time Slots</a></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                    data-bs-target="#assignZonesModal">Assign Zones</a></li>
                        </ul>
                    </div>
                    @can('service-staff-create')
                        <a class="btn btn-success" href="{{ route('serviceStaff.create') }}">
                            <i class="fa fa-plus"></i> Add Staff
                        </a>
                    @endcan
                </div>
            </div>
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <hr>
        <div class="row">
            <div class="col-md-9">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll" />
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('serviceStaff.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('serviceStaff.index', array_merge(request()->query(), ['sort' => 'email', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Email</a>
                            @if (request('sort') === 'email')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('serviceStaff.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Status</a>
                            @if (request('sort') === 'status')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>

                        <th>Sub Title / Designation</th>
                        {{-- <th>Driver</th> --}}
                        <th width="280px">Action</th>
                    </tr>
                    @if (count($serviceStaff))
                        @foreach ($serviceStaff as $staff)
                            <tr>
                                <td>
                                    <input type="checkbox" class="rowCheckbox" value="{{ $staff->id }}">
                                </td>
                                <td>{{ $staff->name }}</td>
                                <td>{{ $staff->email }}</td>
                                <td>
                                    @if ($staff->staff->status == 1)
                                        Enabled
                                    @else
                                        Disabled
                                    @endif
                                </td>
                                <td>
                                    @foreach ($staff->subTitles as $subTitle)
                                        <span class="badge badge-info m-2">{{ $subTitle->name }}</span>
                                    @endforeach
                                </td>

                                {{-- <td>
                                    @if ($staff->staff->driver)
                                        <a href="{{ route('drivers.index', ['id' => $staff->staff->driver->id]) }}">{{ $staff->staff->driver ? $staff->staff->driver->name : '' }}</a>
                                    @endif
                                </td> --}}
                                <td>
                                    <form id="deleteForm{{ $staff->id }}"
                                        action="{{ route('serviceStaff.destroy', $staff->id) }}" method="POST">
                                        <a class="btn btn-warning" href="{{ route('serviceStaff.show', $staff->id) }}"><i
                                                class="fa fa-eye"></i></a>
                                        @can('service-staff-edit')
                                            <a class="btn btn-primary" href="{{ route('serviceStaff.edit', $staff->id) }}"><i
                                                    class="fa fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('service-staff-delete')
                                            <button type="button" onclick="confirmDelete('{{ $staff->id }}')"
                                                class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                        @endcan
                                        <a class="btn btn-primary"
                                            href="{{ route('staffHolidays.create', ['staff' => $staff->id]) }}"
                                            title="Add Holiday"><i class="fas fa-calendar"></i></a>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">There is no Staff.</td>
                        </tr>
                    @endif
                </table>
                {!! $serviceStaff->links() !!}

            </div>
            <div class="col-md-3">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('serviceStaff.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input type="text" name="name" value="{{ $filter['name'] ?? '' }}"
                                    class="form-control" placeholder="Name">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Email:</strong>
                                <input type="text" name="email" value="{{ $filter['email'] ?? '' }}"
                                    class="form-control" placeholder="Email">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Zone Assignment:</strong></label>
                                <select name="assignedZone" class="form-control">
                                    <option value="">All Staff</option>
                                    <option value="0" {{ ($filter['assignedZone'] ?? '') == '0' ? 'selected' : '' }}>
                                        With Assigned Zone
                                    </option>
                                    <option value="1" {{ ($filter['assignedZone'] ?? '') == '1' ? 'selected' : '' }}>
                                        Without Assigned Zone
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>TimeSlot Assignment:</strong></label>
                                <select name="assignedTimeSlot" class="form-control">
                                    <option value="">All Staff</option>
                                    <option value="0"
                                        {{ ($filter['assignedTimeSlot'] ?? '') == '0' ? 'selected' : '' }}>
                                        With Assigned TimeSlot
                                    </option>
                                    <option value="1"
                                        {{ ($filter['assignedTimeSlot'] ?? '') == '1' ? 'selected' : '' }}>
                                        Without Assigned TimeSlot
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Sub Title / Designation:</strong></label>
                                <select name="sub_title" class="form-control select2" id="sub_title">
                                    <option value="">Select Designation</option>
                                    @foreach ($sub_titles as $sub_title)
                                        <option value="{{ $sub_title->id }}"
                                            {{ $sub_title->id == $filter['sub_title'] ? 'selected' : '' }}>
                                            {{ $sub_title->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Location:</strong></label>
                                <select name="location" class="form-control select2" id="location">
                                    <option value="">Select Location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location }}"
                                            {{ $location == $filter['location'] ? 'selected' : '' }}>
                                            {{ $location }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Zones:</strong></label>
                                <select name="zone_id" class="form-control select2" id="zone_id">
                                    <option value="">Select Zone</option>
                                    @foreach ($staffZones as $zone)
                                        <option value="{{ $zone->id }}"
                                            {{ $zone->id == $filter['zone_id'] ? 'selected' : '' }}>
                                            {{ $zone->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Minimum Order Value:</strong></label>
                                <input type="number" class="form-control" name="min_order_value"
                                    value="{{ $filter['min_order_value'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Services:</strong></label>
                                <select name="service_id" class="form-control select2" id="service_id">
                                    <option value="">Select Service</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            {{ $service->id == $filter['service_id'] ? 'selected' : '' }}>
                                            {{ $service->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Categories:</strong></label>
                                <select name="category_id" class="form-control select2" id="category_id">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $category->id == $filter['category_id'] ? 'selected' : '' }}>
                                            {{ $category->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                                <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-sync-alt"></i> Reset
                                </a>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="assignTimeSlotsModal" tabindex="-1" aria-labelledby="assignTimeSlotsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="assignTimeSlotsForm" action="{{ route('serviceStaff.assignTimeSlots') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignTimeSlotsModalLabel">Assign Time Slots</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="staff_ids" id="timeSlotStaffIds">

                        <!-- Filter inputs for time slots -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <input type="text" id="timeSlotNameFilter" class="form-control"
                                    placeholder="Filter by name...">
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="timeStartFilter" class="form-control"
                                    placeholder="Filter by start time...">
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="timeEndFilter" class="form-control"
                                    placeholder="Filter by end time...">
                            </div>
                        </div>

                        <div class="table-container" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-bordered table-striped">
                                <thead class="sticky-top bg-white">
                                    <tr>
                                        <th width="50px"><input type="checkbox" id="selectAllTimeSlots"></th>
                                        <th>Time Slot Name</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                    </tr>
                                </thead>
                                <tbody id="timeSlotTableBody">
                                    @foreach ($timeSlots as $timeSlot)
                                        <tr class="time-slot-row">
                                            <td><input type="checkbox" name="time_slots[]" class="timeSlotCheckbox"
                                                    value="{{ $timeSlot->id }}"></td>
                                            <td class="slot-name">{{ $timeSlot->name }}</td>
                                            <td class="start-time">
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $timeSlot->time_start)->format('h:i A') }}
                                            </td>
                                            <td class="end-time">
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $timeSlot->time_end)->format('h:i A') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="replace_existing"
                                id="replaceTimeSlots">
                            <label class="form-check-label" for="replaceTimeSlots">
                                Replace existing time slots
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Zones Assignment Modal -->
    <div class="modal fade" id="assignZonesModal" tabindex="-1" aria-labelledby="assignZonesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="assignZonesForm" action="{{ route('serviceStaff.assignZones') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignZonesModalLabel">Assign Zones</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="staff_ids" id="zoneStaffIds">

                        <!-- Search input for zones -->
                        <div class="form-group mb-3">
                            <input type="text" id="zoneSearch" class="form-control" placeholder="Search zones...">
                        </div>

                        <div class="table-container" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-bordered table-striped">
                                <thead class="sticky-top bg-white">
                                    <tr>
                                        <th width="50px"><input type="checkbox" id="selectAllZones"></th>
                                        <th>Zone Name</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody id="zoneTableBody">
                                    @foreach ($staffZones as $zone)
                                        <tr class="zone-row">
                                            <td><input type="checkbox" name="zones[]" class="zoneCheckbox"
                                                    value="{{ $zone->id }}"></td>
                                            <td class="zone-name">{{ $zone->name }}</td>
                                            <td>{{ $zone->description ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="replace_existing" id="replaceZones">
                            <label class="form-check-label" for="replaceZones">
                                Replace existing zones
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function confirmDelete(Id) {
            var result = confirm("Are you sure you want to delete this Item?");
            if (result) {
                document.getElementById('deleteForm' + Id).submit();
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            function checkTableResponsive() {
                var viewportWidth = $(window).width();
                var $table = $('table');

                if (viewportWidth < 768) {
                    $table.addClass('table-responsive');
                } else {
                    $table.removeClass('table-responsive');
                }
            }

            checkTableResponsive();

            $(window).resize(function() {
                checkTableResponsive();
            });

            $('#assignTimeSlotsModal').on('show.bs.modal', function() {
                let selectedIds = getSelectedStaffIds();
                if (selectedIds.length === 0) {
                    alert('Please select at least one staff member');
                    return false;
                }
                $('#timeSlotStaffIds').val(selectedIds.join(','));
            });

            // When assign zones modal is shown
            $('#assignZonesModal').on('show.bs.modal', function() {
                let selectedIds = getSelectedStaffIds();
                if (selectedIds.length === 0) {
                    alert('Please select at least one staff member');
                    return false;
                }
                $('#zoneStaffIds').val(selectedIds.join(','));
            });

            // Initialize select2
            $('.select2').select2({
                width: '100%'
            });

            $('#selectAllTimeSlots').change(function() {
                $('.timeSlotCheckbox').prop('checked', $(this).prop('checked'));
            });

            $('#selectAllZones').change(function() {
                $('.zoneCheckbox').prop('checked', $(this).prop('checked'));
            });

            $('.timeSlotCheckbox').change(function() {
                if ($('.timeSlotCheckbox:checked').length == $('.timeSlotCheckbox').length) {
                    $('#selectAllTimeSlots').prop('checked', true);
                } else {
                    $('#selectAllTimeSlots').prop('checked', false);
                }
            });

            $('.zoneCheckbox').change(function() {
                if ($('.zoneCheckbox:checked').length == $('.zoneCheckbox').length) {
                    $('#selectAllZones').prop('checked', true);
                } else {
                    $('#selectAllZones').prop('checked', false);
                }
            });

            $('#zoneSearch').on('keyup', function() {
                const searchText = $(this).val().toLowerCase();
                $('.zone-row').each(function() {
                    const zoneName = $(this).find('.zone-name').text().toLowerCase();
                    $(this).toggle(zoneName.includes(searchText));
                });
            });

            // Time slot filtering
            $('#timeSlotNameFilter').on('keyup', function() {
                const filterText = $(this).val().toLowerCase();
                filterTimeSlots();
            });

            $('#timeStartFilter').on('keyup', function() {
                filterTimeSlots();
            });

            $('#timeEndFilter').on('keyup', function() {
                filterTimeSlots();
            });

            function filterTimeSlots() {
                const nameFilter = $('#timeSlotNameFilter').val().toLowerCase();
                const startFilter = $('#timeStartFilter').val().toLowerCase();
                const endFilter = $('#timeEndFilter').val().toLowerCase();

                $('.time-slot-row').each(function() {
                    const slotName = $(this).find('.slot-name').text().toLowerCase();
                    const startTime = $(this).find('.start-time').text().toLowerCase();
                    const endTime = $(this).find('.end-time').text().toLowerCase();

                    const nameMatch = slotName.includes(nameFilter);
                    const startMatch = startTime.includes(startFilter);
                    const endMatch = endTime.includes(endFilter);

                    $(this).toggle(nameMatch && startMatch && endMatch);
                });
            }
        });


        $('#selectAll').on('change', function() {
            $('.rowCheckbox').prop('checked', this.checked);
        });

        function getSelectedStaffIds() {
            let selectedIds = [];
            $('.rowCheckbox:checked').each(function() {
                selectedIds.push($(this).val());
            });
            return selectedIds;
        }
    </script>
@endsection
