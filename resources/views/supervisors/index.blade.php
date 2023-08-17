@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Supervisor</h2>
            </div>
            <div class="float-end">
                @can('supervisor-create')
                    <a class="btn btn-success" href="{{ route('supervisors.create') }}"> Create New Supervisor</a>
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
    @php
        $i = 0;
    @endphp
    <div class="row">
        <div class="col-md-9">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Sr#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th width="280px">Action</th>
                </tr>
                @if(count($supervisors))
                @foreach ($supervisors as $supervisor)
                @if($supervisor->getRoleNames() == '["Supervisor"]')
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $supervisor->name }}</td>
                    <td>{{ $supervisor->email }}</td>
                    <td>
                        <form action="{{ route('supervisors.destroy',$supervisor->id) }}" method="POST">
                            <a class="btn btn-info" href="{{ route('supervisors.show',$supervisor->id) }}">Show</a>
                            @can('supervisor-edit')
                            <a class="btn btn-primary" href="{{ route('supervisors.edit',$supervisor->id) }}">Edit</a>
                            @endcan
                            @csrf
                            @method('DELETE')
                            @can('supervisor-delete')
                            <button type="submit" class="btn btn-danger">Delete</button>
                            @endcan
                        </form>
                    </td>
                </tr>
                @endif
                @endforeach
                @else
                <tr>
                    <td colspan="5" class="text-center">There is no Supervisor.</td>
                </tr>
                @endif
            </table>
        </div>
        <div class="col-md-3">
            <h3>Filter</h3><hr>
            <form action="{{ route('supervisors.index') }}" method="GET" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" value="{{$filter_name}}" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
@endsection