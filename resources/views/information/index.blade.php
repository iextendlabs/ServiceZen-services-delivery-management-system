@extends('layouts.app')
<style>
    a {
        text-decoration: none !important;
    }
</style>
@section('content')
    @section('page_title')
    <h3 class="text-mute">Information</h3>
    @endsection
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
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center mb-3">
                        <h3>Filter</h3>
                        <div class="float-right">
                            @can('information-create')
                                <a class="btn text-dark float-end" href="{{ route('information.create') }}"> <i
                                        class="fa fa-plus"></i>  New Info</a>
                            @endcan
                        </div>
                    </div>    
                    <div class="card-body">
                        <form action="{{ route('information.index') }}" method="GET" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="small text-muted font-weight-medium mb-1">Name:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0"
                                                    style="border-radius: 0.75rem 0 0 0.75rem;"><i
                                                        class="fas fa-user text-muted"></i></span>
                                            </div>
                                            <input type="text" name="name" value="{{ $filter['name'] }}" class="form-control"
                                                placeholder="Name">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="small text-muted font-weight-medium mb-1">Position:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0"
                                                    style="border-radius: 0.75rem 0 0 0.75rem;"><i
                                                        class="fas fa-user text-muted"></i></span>
                                            </div>
                                            <select name="position" class="form-control">
                                                <option></option>
                                                <option value="Top Menu" @if ($filter['position'] == 'Top Menu') selected @endif>Top Menu
                                                </option>
                                                <option value="Bottom Footer" @if ($filter['position'] == 'Bottom Footer') selected @endif>Bottom
                                                    Footer</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="small text-muted font-weight-medium mb-1">Status:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0"
                                                    style="border-radius: 0.75rem 0 0 0.75rem;"><i
                                                        class="fas fa-user text-muted"></i></span>
                                            </div>
                                            <select name="status" class="form-control">
                                                <option></option>
                                                <option value="1" @if ($filter['status'] === '1') selected @endif>Enabled</option>
                                                <option value="0" @if ($filter['status'] === '0') selected @endif>Disabled</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mt-4">
                                    <div class="d-flex flex-wrap justify-content-md-end">
                                        <a href="{{ url()->current() }}" class="btn btn-sm btn-light border mr-2 font-weight-medium"><i class="fas fa-redo"></i> Reset</a>
                                        <button type="submit" class="btn btn-sm btn-primary shadow-sm font-weight-bold"><i class="fas fa-filter"></i> Filter</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>    
            </div>
            <h3>Information ({{ $total_information }})</h3>
            <div class="col-md-12">
                <table class="table table-bordered">
                    <tr class="bg-white">
                        <th class="text-primary">Sr#</th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('information.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th class="text-primary">Slug</th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('information.index', array_merge(request()->query(), ['sort' => 'description', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Description</a>
                            @if (request('sort') === 'description')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('information.index', array_merge(request()->query(), ['sort' => 'position', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Position</a>
                            @if (request('sort') === 'position')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th class="text-primary">Status</th>
                        <th class="text-primary">Action</th>
                    </tr>
                    @if (count($information))
                        @foreach ($information as $info)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td class="text-left">{{ $info->name }}</td>
                                <td class="text-left">{{ $info->slug }}</td>
                                <td class="text-left">{{ Str::limit(strip_tags(html_entity_decode($info->description)), 50, '...') }}</td>
                                <td class="text-left">{{ $info->position }}</td>
                                <td class="text-left">{{ $info->status ? 'Enabled' : 'Disabled' }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a class="btn text-dark" href="{{ route('information.show', $info->id) }}"><i
                                                class="fa fa-eye"></i></a>
                                        @can('information-edit')
                                            <a class="btn text-dark" href="{{ route('information.edit', $info->id) }}"><i
                                                    class="fa fa-edit"></i></a>
                                        @endcan
                                        <form id="deleteForm{{ $info->id }}" action="{{ route('information.destroy', $info->id) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            @can('information-delete')
                                                <button type="button" onclick="confirmDelete('{{ $info->id }}')" class="btn text-danger"><i class="fas fa-trash"></i></button>
                                            @endcan
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">There is no information.</td>
                        </tr>
                    @endif
                </table>
                {!! $information->links() !!}
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