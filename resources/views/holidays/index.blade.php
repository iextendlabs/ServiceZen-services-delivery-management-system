@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-6">
            <h2>Holiday</h2>
        </div>
        <div class="col-md-6">
            <a class="btn btn-success  float-end" href="{{ route('holidays.create') }}"> Create New Holiday</a>
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
            <th>Date</th>
            <th width="280px">Action</th>
        </tr>
        @if(count($holidays))
        @foreach ($holidays as $holiday)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{$holiday->date}}
                ({{ \Carbon\Carbon::parse($holiday->date)->format('l') }})
            </td>
            <td>
                <form action="{{ route('holidays.destroy',$holiday->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('holidays.show',$holiday->id) }}">Show</a>
                    <!-- <a class="btn btn-primary" href="{{ route('holidays.edit',$holiday->id) }}">Edit</a> -->
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="5" class="text-center">There is no Holidays.</td>
        </tr>
        @endif
    </table>
    {!! $holidays->links() !!}
@endsection