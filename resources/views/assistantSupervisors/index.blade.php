@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Assistant Supervisor ({{ $total_assistant_supervisor }})</h2>
                </div>
                <div class="float-end">
                    @can('assistant-supervisor-create')
                        <a class="btn btn-success" href="{{ route('assistantSupervisors.create') }}"> Create New Assistant
                            Supervisor</a>
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
                                href="{{ route('assistantSupervisors.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('assistantSupervisors.index', array_merge(request()->query(), ['sort' => 'email', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Email</a>
                            @if (request('sort') === 'email')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th width="280px">Action</th>
                    </tr>
                    @if (count($assistant_supervisors))
                        @foreach ($assistant_supervisors as $assistant_supervisor)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $assistant_supervisor->name }}</td>
                                <td>{{ $assistant_supervisor->email }}</td>
                                <td>
                                    <form id="deleteForm{{ $assistant_supervisor->id }}"
                                        action="{{ route('assistantSupervisors.destroy', $assistant_supervisor->id) }}"
                                        method="POST">
                                        <a class="btn btn-info"
                                            href="{{ route('assistantSupervisors.show', $assistant_supervisor->id) }}">Show</a>
                                        @can('assistant-supervisor-edit')
                                            <a class="btn btn-primary"
                                                href="{{ route('assistantSupervisors.edit', $assistant_supervisor->id) }}">Edit</a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('assistant-supervisor-delete')
                                            <button type="button" onclick="confirmDelete('{{ $assistant_supervisor->id }}')"
                                                class="btn btn-danger">Delete</button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">There is no Assistant Supervisor.</td>
                        </tr>
                    @endif
                </table>
                {!! $assistant_supervisors->links() !!}
            </div>
            <div class="col-md-3">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('assistantSupervisors.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input type="text" name="name" value="{{ $filter_name }}" class="form-control"
                                    placeholder="Name">
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
