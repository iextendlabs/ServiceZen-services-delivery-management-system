@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-6">
            <h2>Service Staff</h2>
        </div>
        <div class="col-md-6">
            @can('service-staff-create')
            <a class="btn btn-success float-end" href="{{ route('serviceStaff.create') }}"> Create New Staff</a>
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
        <div class="col-md-9">
            <table class="table table-bordered">
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th width="280px">Action</th>
                </tr>
                @if(count($serviceStaff))
                @foreach ($serviceStaff as $staff)
                @if($staff->getRoleNames() == '["Staff"]')
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $staff->name }}</td>
                    <td>{{ $staff->email }}</td>
                    <td>
                        <form action="{{ route('serviceStaff.destroy',$staff->id) }}" method="POST">
                            <a class="btn btn-info" href="{{ route('serviceStaff.show',$staff->id) }}">Show</a>
                            @can('service-staff-edit')
                            <a class="btn btn-primary" href="{{ route('serviceStaff.edit',$staff->id) }}">Edit</a>
                            @endcan
                            @csrf
                            @method('DELETE')
                            @can('service-staff-delete')
                            <button type="submit" class="btn btn-danger">Delete</button>
                            @endcan
                        </form>
                    </td>
                </tr>
                @endif
                @endforeach
                @else
                <tr>
                    <td colspan="5" class="text-center">There is no Staff.</td>
                </tr>
                @endif
            </table>
        </div>
        <div class="col-md-3">
            <h3>Filter</h3><hr>
            <form action="serviceStaffFilter" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" @if(isset($name)) value="{{$name}}"  @endif class="form-control" placeholder="Name">
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