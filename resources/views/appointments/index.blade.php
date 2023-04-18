@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-6">
            <h2>Appointments</h2>
        </div>
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <span>{{ $message }}</span>
            <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Service</th>
            <th>Price</th>
            <th>Status</th>
            <th>date</th>
            <th>Time</th>
            <th>Action</th>
        </tr>
        @foreach ($appointments as $appointment)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $appointment->service->name }}</td>
            <td>{{ $appointment->service->price }}</td>
            <td>{{ $appointment->status }}</td>
            <td>{{ $appointment->date }}</td>
            <td>{{ $appointment->time }}</td>
            <td>
                <form action="{{ route('appointments.destroy',$appointment->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('appointments.show',$appointment->id) }}">Show</a>
                    <a class="btn btn-primary" href="{{ route('appointments.edit',$appointment->id) }}">Edit</a>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
    {!! $appointments->links() !!}
@endsection