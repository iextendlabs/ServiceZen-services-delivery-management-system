@extends('layouts.app')
@section('content')
@section('page_title')
    <h3> Assistant Supervisors </h3>
@endsection
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
           
               
            </div>
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h3>Filter</h3>
                        <div class="float-end">
                            @can('assistant-supervisor-create')
                                <a class="btn text-dark" href="{{ route('assistantSupervisors.create') }}"><i class="fas fa-plus"></i> Create New Assistant Supervisor</a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('assistantSupervisors.index') }}" method="GET" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="small text-muted font-weight-medium mb-1">Name:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-user text-muted"></i></span>
                                            </div>
                                            <input type="text" name="name" value="{{ $filter_name }}" class="form-control"
                                            placeholder="Name">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group position-relative">
                                        <label class="small text-muted font-weight-medium mb-1">Email:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-envelope text-muted"></i></span>
                                            </div>
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
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-md btn-primary shadow-sm  font-weight-bold"><i class="fa fa-filter"></i> Filter</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>    
            </div>            
            <div class="col-md-12">
                <div class="float-start my-3">
                    <h2>Assistant Supervisor ({{ $total_assistant_supervisor }})</h2>
                </div>
                <table class="table table-bordered">
                    <tr class="bg-white">
                        <th>Sr#</th>
                        <th><i><a class=" ml-2 text-dark"
                                href="{{ route('assistantSupervisors.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a></i>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><i><a class=" ml-2 text-dark"
                                href="{{ route('assistantSupervisors.index', array_merge(request()->query(), ['sort' => 'email', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Email</a></i>
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
                                        <a class="btn text-dark"
                                            href="{{ route('assistantSupervisors.show', $assistant_supervisor->id) }}"><i class="fa fa-eye"></i></a>
                                        @can('assistant-supervisor-edit')
                                            <a class="btn text-dark"
                                                href="{{ route('assistantSupervisors.edit', $assistant_supervisor->id) }}"><i class="fa fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('assistant-supervisor-delete')
                                            <button type="button" onclick="confirmDelete('{{ $assistant_supervisor->id }}')"
                                                class="btn text-danger"><i class="fa fa-trash"></i></button>
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
                    data: { role: 'Assistant Supervisor', query: query },
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
