@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Role Management ({{ $total_role }})</h2>
                </div>
                <div class="float-end">
                    @can('role-create')
                        <a class="btn btn-success" href="{{ route('roles.create') }}"> Create New Role</a>
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
        <table class="table table-striped table-bordered">
            <tr>
                <th>Sr#</th>
                <th><a class=" ml-2 text-decoration-none"
                        href="{{ route('roles.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                    @if (request('sort') === 'name')
                        <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                    @endif
                </th>
                <th width="280px">Action</th>
            </tr>

            @foreach ($roles as $key => $role)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $role->name }}</td>
                    <td>
                        <a class="btn btn-info" href="{{ route('roles.show', $role->id) }}">Show</a>
                        @can('role-edit')
                            <a class="btn btn-primary" href="{{ route('roles.edit', $role->id) }}">Edit</a>
                        @endcan
                        @can('role-delete')
                            <form method="POST" action="{{ route('roles.destroy', $role->id) }}" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </table>
        {{ $roles->render() }}
    </div>
@endsection
