@extends('layouts.app')
<style>
    a {
        text-decoration: none !important;
    }
</style>
@section('content')
    @section('page_title')
    <h3 class="">Countries</h3>
    @endsection
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="float-left d-flex align-items-center text-primary text-bold">
                    <i class="fas fa-globe fs-1 me-2 mb-2 "></i>
                    <h2>Country</h2>
                </div>
                <div class="float-right">
                    @can('country-create')
                        <a class="btn text-primary btn-lg rounded-3 float-end" href="{{ route('countries.create') }}"> <i class="fa fa-plus me-2"></i>Add Countries</a>
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
            <h3>Countries ({{ $total_countries }})</h3>
            <div class="col-md-9">
                <table class="table table-bordered">
                    <tr class="text-center bg-primary text-white">
                        <th>Sr#</th>
                        <th><a class=" ml-2 text-decoration-none text-white"
                                href="{{ route('countries.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>

                        <th>Action</th>
                    </tr>
                    @if (count($countries))
                        @foreach ($countries as $country)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $country->name }}</td>

                                <td>
                                    <form id="deleteForm{{ $country->id }}" action="{{ route('countries.destroy', $country->id) }}"
                                        method="POST">
                                        @can('country-edit')
                                            <a class="btn text-dark" href="{{ route('countries.edit', $country->id) }}"><i class="fas fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('country-delete')
                                            <button type="button" onclick="confirmDelete('{{ $country->id }}')"
                                                class="btn text-danger"><i class="fas fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="text-center">There is no Country.</td>
                        </tr>
                    @endif
                </table>
                {!! $countries->links() !!}
            </div>            
            <div class="col-md-3">
                <form action="{{ route('countries.index') }}" method="GET" enctype="multipart/form-data">
                    {{-- <div class="row"> --}}
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Filter</h5>
                                <div class="">
                                    <div class="form-group">
                                        <label class="small text-muted font-weight-medium mb-1">Name:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-user text-muted"></i></span>
                                            </div>
                                            <input type="text" name="name" value="{{ $filter['name'] }}" class="form-control"
                                                placeholder="Name">
                                        </div>
                                    </div>    
                                </div>
                                <div class="">
                                    <div class="d-flex flex-wrap justify-content-md-end">
                                        <div class="mt-4 d-flex justify-content-end">
                                            <a href="{{ url()->current() }}" class="btn btn-md btn-light border mr-2 font-weight-medium">Reset</a>
                                            <button type="submit" class="btn btn-md btn-primary shadow-sm font-weight-bold">Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {{-- </div> --}}
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
