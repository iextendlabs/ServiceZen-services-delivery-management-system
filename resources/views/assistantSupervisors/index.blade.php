@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Assistant Supervisor</h2>
            </div>
            <div class="float-end">
                @can('assistant-supervisor-create')
                    <a class="btn btn-success" href="{{ route('assistantSupervisors.create') }}"> Create New Assistant Supervisor</a>
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
    <div class="row">
        <div class="col-md-9">
            <table class="table table-bordered">
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th width="280px">Action</th>
                </tr>
                @if(count($assistant_supervisors))
                @foreach ($assistant_supervisors as $assistant_supervisor)
                @if($assistant_supervisor->getRoleNames() == '["Assistant Supervisor"]')
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $assistant_supervisor->name }}</td>
                    <td>{{ $assistant_supervisor->email }}</td>
                    <td>
                        @if(!empty($assistant_supervisor->getRoleNames()))
                            @foreach($assistant_supervisor->getRoleNames() as $v)
                                <span class="badge rounded-pill bg-dark">{{ $v }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('assistantSupervisors.destroy',$assistant_supervisor->id) }}" method="POST">
                            <a class="btn btn-info" href="{{ route('assistantSupervisors.show',$assistant_supervisor->id) }}">Show</a>
                            @can('assistant-supervisor-edit')
                            <a class="btn btn-primary" href="{{ route('assistantSupervisors.edit',$assistant_supervisor->id) }}">Edit</a>
                            @endcan
                            @csrf
                            @method('DELETE')
                            @can('assistant-supervisor-delete')
                            <button type="submit" class="btn btn-danger">Delete</button>
                            @endcan
                        </form>
                    </td>
                </tr>
                @endif
                @endforeach
                @else
                <tr>
                    <td colspan="5" class="text-center">There is no Assistant Supervisor.</td>
                </tr>
                @endif
            </table>
        </div>
        <div class="col-md-3">
            <h3>Filter</h3><hr>
            <form action="assistantSupervisorFilter" method="POST" enctype="multipart/form-data">
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