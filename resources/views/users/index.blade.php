@extends('layouts.app')
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-lg-12 margin-tb">
                <div class="float-start">
                    <h2>Users Management ({{ $total_user }})</h2>
                </div>
                <div class="float-end">
                    @can('user-create')
                        <a class="btn btn-success" href="{{ route('users.create') }}"> Create New User</a>
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
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Sr#</th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('users.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('users.index', array_merge(request()->query(), ['sort' => 'email', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Email</a>
                            @if (request('sort') === 'email')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('users.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc', 'table' => 'role'])) }}">Roles</a>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        {{-- <th>Roles</th> --}}
                        <th width="280px">Action</th>
                    </tr>
                    @if (count($data))
                        @foreach ($data as $key => $user)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if (!empty($user->getRoleNames()))
                                        @foreach ($user->getRoleNames() as $v)
                                            <span class="badge rounded-pill bg-dark">{{ $v }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    <form id="deleteForm{{ $user->id }}"
                                        action="{{ route('users.destroy', $user->id) }}" method="POST">
                                        <a class="btn btn-warning" href="{{ route('users.show', $user->id) }}"><i
                                                class="fa fa-eye"></i></a>
                                        {{-- @can('user-edit')
                                            <a class="btn btn-primary" href="{{ route('users.edit', $user->id) }}"><i
                                                    class="fa fa-edit"></i></a>
                                        @endcan --}}
                                        @csrf
                                        @method('DELETE')
                                        @can('user-delete')
                                            <button type="button" onclick="confirmDelete('{{ $user->id }}')"
                                                class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">There is no User.</td>
                        </tr>
                    @endif
                </table>
                {!! $data->links() !!}

            </div>
            <div class="col-md-3">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('users.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input type="text" name="name" value="{{ $filter['name'] }}" class="form-control"
                                    placeholder="Name">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Role:</strong>
                                <select name="role" class="form-control">
                                    <option></option>
                                    @foreach ($roles as $role)
                                        @if ($role->name == $filter['role'])
                                            <option value="{{ $role->name }}" selected>{{ $role->name }}</option>
                                        @else
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function confirmDelete(Id) {
            var result = confirm("Are you sure you want to delete this Item?");
            if (result) {
                document.getElementById('deleteForm' + Id).submit();
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            function checkTableResponsive() {
                var viewportWidth = $(window).width();
                var $table = $('table');

                if (viewportWidth < 768) {
                    $table.addClass('table-responsive');
                } else {
                    $table.removeClass('table-responsive');
                }
            }

            checkTableResponsive();

            $(window).resize(function() {
                checkTableResponsive();
            });
        });
    </script>
@endsection
