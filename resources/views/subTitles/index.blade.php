@extends('layouts.app')
<style>
    a {
        text-decoration: none !important;
    }
</style>
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="float-left">
                    <h2>Sub Title / Designation</h2>
                </div>
                <div class="float-right">
                    @can('staff-designation-create')
                        <a class="btn btn-success  float-end" href="{{ route('subTitles.create') }}"> <i class="fa fa-plus"></i></a>
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
            <div class="col-md-12">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('subTitles.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input type="text" name="name" value="{{ $filter['name'] }}" class="form-control"
                                    placeholder="Name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 offset-md-8">
                            <div class="d-flex flex-wrap justify-content-md-end">
                                <div class="col-md-3 mb-3">
                                    <a href="{{ url()->current() }}" class="btn btn-lg btn-secondary">Reset</a>
                                </div>
                                <div class="col-md-9 mb-3">
                                    <button type="submit" class="btn btn-lg btn-block btn-primary">Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <h3>Sub Title / Designation ({{ $total_sub_title }})</h3>
            <div class="col-md-12">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Sr#</th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('subTitles.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>

                        <th>Action</th>
                    </tr>
                    @if (count($subTitles))
                        @foreach ($subTitles as $subTitle)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $subTitle->name }}</td>
                                
                                <td>
                                    <form id="deleteForm{{ $subTitle->id }}" action="{{ route('subTitles.destroy', $subTitle->id) }}"
                                        method="POST">
                                        <a class="btn btn-warning" href="{{ route('subTitles.show', $subTitle->id) }}"><i
                                                class="fa fa-eye"></i></a>
                                        @can('staff-designation-edit')
                                            <a class="btn btn-primary" href="{{ route('subTitles.edit', $subTitle->id) }}"><i
                                                    class="fa fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('staff-designation-delete')
                                            <button type="button" onclick="confirmDelete('{{ $subTitle->id }}')"
                                                class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="text-center">There is no Sub Title / Designation.</td>
                        </tr>
                    @endif
                </table>
                {!! $subTitles->links() !!}
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
