@extends('layouts.app')
@section('content')
    @section('page_title')
    <h3 class="">Staff Zones</h3>
    @endsection
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2>Staff Zone ({{ $total_staffZone }})</h2>
            </div>
            <div class="col-md-6">
                @can('staff-zone-create')
                    <a class="btn  text-dark  float-end" href="{{ route('staffZones.create') }}"><i class="fas fa-plus"></i> Create Staff Zone</a>
                @endcan
            </div>
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <hr class="mb-3">
        <div class="row">
            <div class="col-md-9">
                <table class="table table-bordered">
                    <tr class="text-center bg-white">
                        <th class="text-primary">Sr#</th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('staffZones.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('staffZones.index', array_merge(request()->query(), ['sort' => 'description', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Description</a>
                            @if (request('sort') === 'description')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('staffZones.index', array_merge(request()->query(), ['sort' => 'transport_charges', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Transport
                                Charges</a>
                            @if (request('sort') === 'transport_charges')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th class="text-primary">Currency</th>
                        <th class="text-primary" width="280px">Action</th>
                    </tr>
                    @if (count($staffZones))
                        @foreach ($staffZones as $staffZone)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $staffZone->name }}</td>
                                <td>{{ $staffZone->description }}</td>
                                <td>{{ $staffZone->transport_charges }}</td>
                                <td>{{ $staffZone->currency->name ?? '' }}</td>
                                <td>
                                    <form id="deleteForm{{ $staffZone->id }}"
                                        action="{{ route('staffZones.destroy', $staffZone->id) }}" method="POST">
                                        <a class="btn text-dark"
                                            href="{{ route('staffZones.show', $staffZone->id) }}"><i class="fa fa-eye"></i></a>
                                        @can('staff-zone-edit')
                                            <a class="btn text-dark"
                                                href="{{ route('staffZones.edit', $staffZone->id) }}"><i class="fa fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('staff-zone-delete')
                                            <button type="button" onclick="confirmDelete('{{ $staffZone->id }}')"
                                                class="btn text-danger"><i class="fa fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">There is no staff zone.</td>
                        </tr>
                    @endif
                </table>
                {!! $staffZones->links() !!}
            </div>
            <div class="col-md-3">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body">
                        <h3>Filter</h3>
                        <hr>
                        <form action="{{ route('staffZones.index') }}" method="GET" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12">
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
                                <div class="col-md-12">
                                    <label class="small text-muted font-weight-medium mb-1">Country Name:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend w-100">
                                            <span class="input-group-text bg-white border-right-0"
                                                style="border-radius: 0.75rem 0 0 0.75rem;"><i
                                                    class="fas fa-globe text-muted"></i></span>
                                                    
                                            <select name="country_id" class="form-control select2" >
                                                <option></option>
                                                @foreach($country as $c)
                                                <option value="{{ $c->id }}" {{ $filter['country_id'] == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 d-flex justify-content-end mt-4">
                                    <a href="{{ url()->current() }}" class="btn btn-sm btn-light border mr-2 font-weight-medium">Reset</a>
                                    <button type="submit" class="btn btn-sm btn-primary shadow-sm font-weight-bold">Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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
