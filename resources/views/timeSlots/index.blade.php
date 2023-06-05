@extends('layouts.app')
@section('content')
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
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Time Start -- Time End</th>
            <th>Group</th>
            <th>Active</th>
            <th width="280px">Action</th>
        </tr>
        @if(count($time_slots))
        @foreach ($time_slots as $time_slot)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $time_slot->name }}</td>
            <td>{{ date('h:i A', strtotime($time_slot->time_start)) }} -- {{ date('h:i A', strtotime($time_slot->time_end)) }}</td>
            <td>{{ $time_slot->group->name }}</td>
            <td>@if($time_slot->active == "Available")
                <span class="badge rounded-pill bg-primary">{{ $time_slot->active }}</span>
                @else
                <span class="badge rounded-pill bg-danger">{{ $time_slot->active }}</span>
                @endif
            </td>
            <td>
                <form action="{{ route('timeSlots.destroy',$time_slot->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('timeSlots.show',$time_slot->id) }}">Show</a>
                        @can('time-slot-edit')
                        <a class="btn btn-primary" href="{{ route('timeSlots.edit',$time_slot->id) }}">Edit</a>
                        @endcan
                        @csrf
                        @method('DELETE')
                        @can('time-slot-delete')
                            <button type="submit" class="btn btn-danger">Delete</button>
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
@endsection