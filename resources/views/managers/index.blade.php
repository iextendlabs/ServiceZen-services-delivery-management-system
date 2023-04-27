@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Manager</h2>
            </div>
            <div class="float-end">
                @can('manager-create')
                    <a class="btn btn-success" href="{{ route('managers.create') }}"> Create New Manager</a>
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
                @if(count($managers))
                @foreach ($managers as $manager)
                @if($manager->getRoleNames() == '["Manager"]')
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $manager->name }}</td>
                    <td>{{ $manager->email }}</td>
                    <td>
                        @if(!empty($manager->getRoleNames()))
                            @foreach($manager->getRoleNames() as $v)
                                <span class="badge rounded-pill bg-dark">{{ $v }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('managers.destroy',$manager->id) }}" method="POST">
                            <a class="btn btn-info" href="{{ route('managers.show',$manager->id) }}">Show</a>
                            @can('manager-edit')
                            <a class="btn btn-primary" href="{{ route('managers.edit',$manager->id) }}">Edit</a>
                            @endcan
                            @csrf
                            @method('DELETE')
                            @can('manager-delete')
                            <button type="submit" class="btn btn-danger">Delete</button>
                            @endcan
                        </form>
                    </td>
                </tr>
                @endif
                @endforeach
                @else
                <tr>
                    <td colspan="5" class="text-center">There is no Manager.</td>
                </tr>
                @endif
            </table>
            {!! $managers->links() !!}
        </div>
        <div class="col-md-3">
            <h3>Filter</h3><hr>
            <form action="managerFilter" method="POST" enctype="multipart/form-data">
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