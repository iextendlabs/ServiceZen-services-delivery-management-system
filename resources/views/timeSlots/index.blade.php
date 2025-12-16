@extends('layouts.app')
@section('content')
    @section('page_title')
    <h3 class="">Time Slots</h3>
    @endsection
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h2>Time Slots ({{ $total_time_slot }})</h2>
        </div>
        <div class="col-md-6">
            @can('time-slot-create')
            <a class="btn btn-outline-primary rounded-3  float-end" href="{{ route('timeSlots.create') }}"> Create New Time Slot</a>
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
        <tr  class="text-center bg-primary text-white">
            <th>Sr#</th>
            <th>
                <div class="d-flex">
                    <a class="ml-3  text-decoration-none text-white" href="{{ route('timeSlots.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                    @if (request('sort') === 'name')
                    <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                    @endif

                </div>
            </th>
            <th>
                <div class="d-flex">
                    <a class="ml-3  text-decoration-none text-white" href="{{ route('timeSlots.index', array_merge(request()->query(), ['sort' => 'time_start', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Time Start -- Time End</a>
                    @if (request('sort') === 'time_start')
                    <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                    @endif

                </div>
            </th>
            <th>Type</th>
            <th>No. of Seats</th>
            <th width="280px">Action</th>
        </tr>
        @if(count($time_slots))
        @foreach ($time_slots as $time_slot)
        <tr>
            <td>{{ ++$i }}</td>
            <td>@if($time_slot->status == 1)
                <span class="text-info">{{ $time_slot->name }}</span>
                @else
                <span class="text-dark">{{ $time_slot->name }}</span>
                @endif</td>
            <td>{{ date('h:i A', strtotime($time_slot->time_start)) }} -- {{ date('h:i A', strtotime($time_slot->time_end)) }}</td>
            <td>{{ $time_slot->type }}</td>
            <td>{{ $time_slot->seat }}</td>
            <td>
                <form id="deleteForm{{ $time_slot->id }}" action="{{ route('timeSlots.destroy',$time_slot->id) }}" method="POST">
                    <a class="btn text-dark" href="{{ route('timeSlots.show',$time_slot->id) }}"><i class="fa fa-eye"></i></a>
                    @can('time-slot-edit')
                    <a class="btn text-dark" href="{{ route('timeSlots.edit',$time_slot->id) }}"><i class="fa fa-edit"></i></a>
                    @endcan
                    @csrf
                    @method('DELETE')
                    @can('time-slot-delete')
                    <button type="button" onclick="confirmDelete('{{ $time_slot->id }}')" class="btn text-danger"><i class="fa fa-trash"></i></button>
                    @endcan
                </form>
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="7" class="text-center">There is no time slots.</td>
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