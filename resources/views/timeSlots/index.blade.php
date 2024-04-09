@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h2>Time Slots</h2>
        </div>
        <div class="col-md-6">
            @can('time-slot-create')
            <a class="btn btn-success  float-end" href="{{ route('timeSlots.create') }}"> Create New Time Slot</a>
            @endcan
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
        @if(!auth()->user()->hasRole("Staff"))
        <!-- Second Column (Filter Form) -->
        <div class="col-md-12">
            <h3>Filter</h3>
            <hr>
            <form action="{{ route('timeSlots.index') }}" method="GET" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Staff:</strong>
                            <select name="staff_id" class="form-control">
                                <option value="">Select</option>
                                @foreach ($staffs as $staff)
                                @if($staff->id == $filter['staff_id'])
                                <option value="{{ $staff->id }}" selected>{{ $staff->name }}</option>
                                @else
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 offset-md-8">
                        <div class="d-flex flex-wrap justify-content-md-end">
                            <div class="col-md-3 mb-3">
                                <a href="{{ url()->current() }}" class="btn btn-lg btn-secondary">Reset</a>
                            </div>
                            <div class="col-md-9 mb-3">
                                <button type="submit" class="btn btn-lg btn-block btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @endif

    </div>
    <h3>Time Slots  ({{ $total_time_slot }})</h3>
    <table class="table table-striped table-bordered">
        <tr>
            <th>Sr#</th>
            <th>Name</th>
            <th>Time Start -- Time End</th>
            <th>Group</th>
            <th>Staff</th>
            <th width="280px">Action</th>
        </tr>
        @if(count($time_slots))
        @foreach ($time_slots as $time_slot)
        <tr>
            <td>{{ ++$i }}</td>
            <td>@if($time_slot->status == 1)
                <span class="text-success">{{ $time_slot->name }}</span>
                @else
                <span class="text-danger">{{ $time_slot->name }}</span>
                @endif</td>
            <td>{{ date('h:i A', strtotime($time_slot->time_start)) }} -- {{ date('h:i A', strtotime($time_slot->time_end)) }}</td>
            <td>{{ $time_slot->group->name }}</td>
            <td>
                @foreach($time_slot->staffs as $staff)
                    {{ $staff->name }}
                @endforeach
            </td>
            <td>
                <form id="deleteForm{{ $time_slot->id }}" action="{{ route('timeSlots.destroy',$time_slot->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('timeSlots.show',$time_slot->id) }}">Show</a>
                    @can('time-slot-edit')
                    <a class="btn btn-primary" href="{{ route('timeSlots.edit',$time_slot->id) }}">Edit</a>
                    @endcan
                    @csrf
                    @method('DELETE')
                    @can('time-slot-delete')
                    <button type="button" onclick="confirmDelete('{{ $time_slot->id }}')" class="btn btn-danger">Delete</button>
                    @endcan
                </form>
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="6" class="text-center">There is no time slots.</td>
        </tr>
        @endif
    </table>
    {!! $time_slots->links() !!}
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
    $(document).ready(function () {
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

        $(window).resize(function () {
            checkTableResponsive();
        });
    });
</script>
@endsection