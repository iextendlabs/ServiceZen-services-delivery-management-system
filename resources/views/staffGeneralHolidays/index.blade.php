@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-6">
            <h2>Staff General Holidays</h2>
        </div>
        <div class="col-md-6">
            @can('staff-holiday-create')
            <a class="btn btn-success float-end" href="{{ route('staffGeneralHolidays.create') }}"> Create New Staff General Holiday</a>
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
        <div class="col-md-12">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Sr#</th>
                    <th>Day</th>
                    <th>Staff Name</th>
                    <th width="280px">Action</th>
                </tr>
                @if(count($staffGeneralHolidays))
                @foreach ($staffGeneralHolidays as $staffGeneralHoliday)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $staffGeneralHoliday->day }}</td>
                    <td>{{ $staffGeneralHoliday->staff->name }}</td>
                    <td>
                        <form action="{{ route('staffGeneralHolidays.destroy',$staffGeneralHoliday->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            @can('staff-holiday-delete')
                            <button type="submit" class="btn btn-danger">Delete</button>
                            @endcan
                        </form>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="4" class="text-center">There is no staff General Holiday.</td>
                </tr>
                @endif  
            </table>
            {!! $staffGeneralHolidays->links() !!}
        </div>
    </div>
    
@endsection