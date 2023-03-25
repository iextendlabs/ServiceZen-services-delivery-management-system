@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-6">
            <h2>Service Staff</h2>
        </div>
        <div class="col-6">
            @can('service-staff-create')
            <a class="btn btn-success float-end" href="{{ route('serviceStaff.create') }}"> Create New Service</a>
            @endcan
        </div>
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Email</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($serviceStaff as $service)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $service->name }}</td>
            <td>{{ $service->email }}</td>
            <td>
                <form action="{{ route('serviceStaff.destroy',$service->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('serviceStaff.show',$service->id) }}">Show</a>
                    @can('service-staff-edit')
                    <a class="btn btn-primary" href="{{ route('serviceStaff.edit',$service->id) }}">Edit</a>
                    @endcan
                    @csrf
                    @method('DELETE')
                    @can('service-staff-delete')
                    <button type="submit" class="btn btn-danger">Delete</button>
                    @endcan
                </form>
            </td>
        </tr>
        @endforeach
    </table>
    {!! $serviceStaff->links() !!}
@endsection