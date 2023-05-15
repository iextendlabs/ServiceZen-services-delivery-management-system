@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-6">
            <h2>Staff Group</h2>
        </div>
        <div class="col-md-6">
            <a class="btn btn-success  float-end" href="{{ route('staffGroups.create') }}"> Create New Staff Group</a>
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
            <table class="table table-bordered">
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th width="280px">Action</th>
                </tr>
                @if(count($staffGroups))
                @foreach ($staffGroups as $staffGroup)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $staffGroup->name }}</td>
                    <td>
                        <form action="{{ route('staffGroups.destroy',$staffGroup->id) }}" method="POST">
                            <a class="btn btn-info" href="{{ route('staffGroups.show',$staffGroup->id) }}">Show</a>
                            <a class="btn btn-primary" href="{{ route('staffGroups.edit',$staffGroup->id) }}">Edit</a>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="4" class="text-center">There is no staff Group.</td>
                </tr>
                @endif  
            </table>
            {!! $staffGroups->links() !!}
        </div>
    </div>
    
@endsection