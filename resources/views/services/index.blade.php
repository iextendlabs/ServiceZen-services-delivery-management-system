@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-6">
            <h2>Services</h2>
        </div>
        <div class="col-6">
            @can('service-create')
            <a class="btn btn-success  float-end" href="{{ route('services.create') }}"> Create New Service</a>
            @endcan
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
            <th>Name</th>
            <th>Price</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($services as $service)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $service->name }}</td>
            <td>{{ $service->price }}</td>
            <td>
                <form action="{{ route('services.destroy',$service->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('services.show',$service->id) }}">Show</a>
                    @can('service-edit')
                    <a class="btn btn-primary" href="{{ route('services.edit',$service->id) }}">Edit</a>
                    @endcan
                    @csrf
                    @method('DELETE')
                    @can('service-delete')
                    <button type="submit" class="btn btn-danger">Delete</button>
                    @endcan
                </form>
            </td>
        </tr>
        @endforeach
    </table>
    {!! $services->links() !!}
@endsection