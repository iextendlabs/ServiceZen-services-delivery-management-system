@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h2>Staff Holidays</h2>
        </div>
        <div class="col-md-6">
            @can('staff-holiday-create')
            <a class="btn btn-success  float-end" href="{{ route('staffHolidays.create') }}"> Create New Staff Holiday</a>
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
    <h3>Staff Holiday  ({{ $total_staffHoliday }})</h3>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Sr#</th>
                    <th>Date</th>
                    <th>Staff Name</th>
                    <th width="280px">Action</th>
                </tr>
                @if(count($staffHolidays))
                @foreach ($staffHolidays as $staffHoliday)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $staffHoliday->date }}({{ \Carbon\Carbon::parse($staffHoliday->date)->format('l') }})</td>
                    <td>{{ $staffHoliday->staff->name }}</td>
                    <td>
                        <form id="deleteForm{{ $staffHoliday->id }}" action="{{ route('staffHolidays.destroy',$staffHoliday->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            @can('staff-holiday-delete')
                            <button type="button" onclick="confirmDelete('{{ $staffHoliday->id }}')" class="btn btn-danger">Delete</button>
                            @endcan
                        </form>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="4" class="text-center">There is no staff Holiday.</td>
                </tr>
                @endif
            </table>
            {!! $staffHolidays->links() !!}
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
@endsection