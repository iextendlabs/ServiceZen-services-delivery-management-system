@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Complaint ({{ $total_complaint }})</h2>
                </div>
                <div class="float-end">
                    @can('complaint-create')
                        <a class="btn btn-success" href="{{ route('complaints.create') }}"><i class="fa fa-plus"></i></a>
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
        <h3>Complaint  ({{ $total_complaint }})</h3>
        <div class="row">
            <div class="col-md-9">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Sr#</th>
                        <th><a class="text-black ml-2 text-decoration-none"
                                href="{{ route('complaints.index', array_merge(request()->query(), ['sort' => 'title', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Title</a>
                            @if (request('sort') === 'title')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class="text-black ml-2 text-decoration-none"
                                href="{{ route('complaints.index', array_merge(request()->query(), ['sort' => 'description', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Description</a>
                            @if (request('sort') === 'description')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th>User</th>
                        <th><a class="text-black ml-2 text-decoration-none"
                            href="{{ route('complaints.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Status</a>
                        @if (request('sort') === 'status')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                        @endif
                    </th>
                        {{-- <th>Status</th> --}}
                        <th>Action</th>
                    </tr>
                    @if (count($complaints))
                        @foreach ($complaints as $complaint)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $complaint->title }}</td>
                                <td>{{ $complaint->description ? substr($complaint->description, 0, 50) . '...' : '' }}</td>
                                <td>{{ $complaint->user->name ?? '' }}</td>
                                <td>{{ $complaint->status }}</td>
                                <td>
                                    <form id="deleteForm{{ $complaint->id }}"
                                        action="{{ route('complaints.destroy', $complaint->id) }}" method="POST">
                                        <a class="btn btn-warning" href="{{ route('complaints.show', $complaint->id) }}"><i
                                                class="fa fa-eye"></i></a>
                                        @can('complaint-edit')
                                            <a class="btn btn-primary" href="{{ route('complaints.edit', $complaint->id) }}"><i
                                                    class="fa fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('complaint-delete')
                                            <button type="button" class="btn btn-danger"
                                                onclick="confirmDelete('{{ $complaint->id }}')"><i
                                                    class="fa fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center">There is no complaint.</td>
                        </tr>
                    @endif
                </table>
                {!! $complaints->links() !!}

            </div>
            <div class="col-md-3">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('complaints.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Title:</strong>
                                <input type="text" name="title" value="{{ $filter['title'] }}" class="form-control"
                                    placeholder="Title">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Order ID:</strong>
                                <input type="number" name="order_id" value="{{ $filter['order_id'] }}"
                                    class="form-control" placeholder="Order">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Status:</strong>
                                <select name="status" class="form-control">
                                    <option></option>
                                    <option value="Open" @if ($filter['status'] === 'Open') selected @endif>Open</option>
                                    <option value="Close" @if ($filter['status'] === 'Close') selected @endif>Close</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>User:</strong>
                                <input type="text" name="user" value="{{ $filter['user'] }}" class="form-control"
                                    placeholder="User name or email">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Filter</button>
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
