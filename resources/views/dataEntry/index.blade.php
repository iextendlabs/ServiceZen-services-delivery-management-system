@extends('layouts.app')
@section('content')
    <div class="container">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="card p-3 mb-3">
                    <div class="card-header bg-white">
                        <div class="float-end">
                            @can('data-entry-create')
                            <a class="btn font-weight-bold" href="{{ route('dataEntry.create') }}"><i class="fa fa-plus"></i> Add Data Entry User</a>
                            @endcan
                        </div>
                        <h3>Filter</h3>
                    </div>
                <div class="card-body">
                    <form action="{{ route('dataEntry.index') }}" method="GET" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>Name:</strong>
                                    <input type="text" name="name" value="{{ $filter_name }}" class="form-control"
                                        placeholder="Name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group position-relative">
                                    <strong>Email:</strong>
                                    <input type="email" name="email" id="email-autocomplete" value="{{ $filter_email }}" class="form-control" autocomplete="off">
                                    <ul id="email-suggestions" class="list-group" style="position:absolute; z-index:1000; width:100%; display:none; max-height:180px; overflow-y:auto;"></ul>
                                    <style>
                                        #email-suggestions .list-group-item {
                                            cursor: pointer !important;
                                        }
                                        #email-suggestions .list-group-item:hover {
                                            background-color: #f0f0f0;
                                        }
                                    </style>
                                </div>
                            </div>
                            <div class="col-md-4 mt-4">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>            
            <div class="col-md-12">
                <div class="float-start">
                    <h2 class="font-weight-bold">Data Entry User ({{ $total_user }})</h2>
                </div>
                <table class="table table-hover table-bordered">
                    <tr>
                        <th>Sr#</th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('dataEntry.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('dataEntry.index', array_merge(request()->query(), ['sort' => 'email', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Email</a>
                            @if (request('sort') === 'email')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>

                        <th width="280px">Action</th>
                    </tr>
                    @if (count($users))
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <form id="deleteForm{{ $user->id }}"
                                        action="{{ route('dataEntry.destroy', $user->id) }}" method="POST">
                                        <a class="btn text-dark" href="{{ route('dataEntry.show', $user->id) }}"><i class="fa fa-eye"></i></a>
                                        @can('data-entry-edit')
                                            <a class="btn text-dark"
                                                href="{{ route('dataEntry.edit', $user->id) }}"><i class="fa fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('data-entry-delete')
                                            <button type="button" onclick="confirmDelete('{{ $user->id }}')"
                                                class="btn text-danger"><i class="fa fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">There is no data entry user.</td>
                        </tr>
                    @endif
                </table>
                {!! $users->links() !!}
            </div>
        </div>
    </div>
    <script>
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        $(document).ready(function() {
            var $input = $('#email-autocomplete');
            var $suggestions = $('#email-suggestions');
            $input.on('input', debounce(function() {
                var query = $(this).val();
                if (query.length < 2) {
                    $suggestions.hide();
                    return;
                }
                $.ajax({
                    url: '{{ route('autocomplete.email') }}',
                    data: { role: 'Data Entry', query: query },
                    success: function(data) {
                        $suggestions.empty();
                        if (Array.isArray(data) && data.length) {
                            data.forEach(function(email) {
                                var $li = $('<li class="list-group-item list-group-item-action"></li>').text(email);
                                $li.on('click', function() {
                                    $input.val(email);
                                    $suggestions.hide();
                                });
                                $suggestions.append($li);
                            });
                            $suggestions.show();
                        } else {
                            var $li = $('<li class="list-group-item text-muted"></li>').text('No data found');
                            $suggestions.append($li);
                            $suggestions.show();
                        }
                    },
                    error: function(xhr) {
                        $suggestions.empty();
                        var $li = $('<li class="list-group-item text-danger"></li>').text('Error fetching data');
                        $suggestions.append($li);
                        $suggestions.show();
                    }
                });
            }, 300));
            $input.on('blur', function() {
                setTimeout(function() { $suggestions.hide(); }, 200);
            });
        });

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
