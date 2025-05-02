@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="float-left">
                    <h2>Service Staff ({{ $total_staff }})</h2>
                </div>
                <div class="float-right">
                    @can('service-staff-create')
                        <a class="btn btn-success float-end" href="{{ route('serviceStaff.create') }}"><i
                                class="fa fa-plus"></i></a>
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
                                href="{{ route('serviceStaff.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('serviceStaff.index', array_merge(request()->query(), ['sort' => 'email', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Email</a>
                            @if (request('sort') === 'email')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('serviceStaff.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Status</a>
                            @if (request('sort') === 'status')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>

                        <th>Sub Title / Designation</th>
                        {{-- <th>Driver</th> --}}
                        <th width="280px">Action</th>
                    </tr>
                    @if (count($serviceStaff))
                        @foreach ($serviceStaff as $staff)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $staff->name }}</td>
                                <td>{{ $staff->email }}</td>
                                <td>
                                    @if ($staff->staff->status == 1)
                                        Enabled
                                    @else
                                        Disabled
                                    @endif
                                </td>
                                <td>
                                    @foreach ($staff->subTitles as $subTitle)
                                        <span class="badge badge-info m-2">{{ $subTitle->name }}</span>
                                    @endforeach
                                </td>
                                
                                {{-- <td>
                                    @if($staff->staff->driver)
                                        <a href="{{ route('drivers.index', ['id' => $staff->staff->driver->id]) }}">{{ $staff->staff->driver ? $staff->staff->driver->name : '' }}</a>
                                    @endif
                                </td> --}}
                                <td>
                                    <form id="deleteForm{{ $staff->id }}"
                                        action="{{ route('serviceStaff.destroy', $staff->id) }}" method="POST">
                                        <a class="btn btn-warning" href="{{ route('serviceStaff.show', $staff->id) }}"><i
                                                class="fa fa-eye"></i></a>
                                        @can('service-staff-edit')
                                            <a class="btn btn-primary" href="{{ route('serviceStaff.edit', $staff->id) }}"><i
                                                    class="fa fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('service-staff-delete')
                                            <button type="button" onclick="confirmDelete('{{ $staff->id }}')"
                                                class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                        @endcan
                                        <a class="btn btn-primary"
                                            href="{{ route('staffHolidays.create', ['staff' => $staff->id]) }}"
                                            title="Add Holiday"><i class="fas fa-calendar"></i></a>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">There is no Staff.</td>
                        </tr>
                    @endif
                </table>
                {!! $serviceStaff->links() !!}

            </div>
            <div class="col-md-3">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('serviceStaff.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input type="text" name="name" value="{{ $filter['name'] ?? '' }}" class="form-control"
                                    placeholder="Name">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Email:</strong>
                                <input type="text" name="email" value="{{ $filter['email'] ?? '' }}" class="form-control"
                                    placeholder="Email">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Sub Title / Designation:</strong></label>
                                <select name="sub_title" class="form-control select2" id="sub_title">
                                    <option value="">Select Designation</option>
                                    @foreach ($sub_titles as $sub_title)
                                        <option value="{{ $sub_title->id }}" {{ $sub_title->id == $filter['sub_title'] ? 'selected' : '' }}>
                                            {{ $sub_title->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Location:</strong></label>
                                <select name="location" class="form-control select2" id="location">
                                    <option value="">Select Location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location }}" {{ $location == $filter['location'] ? 'selected' : '' }}>
                                            {{ $location }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Zones:</strong></label>
                                <select name="zone_id" class="form-control select2" id="zone_id">
                                    <option value="">Select Zone</option>
                                    @foreach ($staffZones as $zone)
                                        <option value="{{ $zone->id }}" {{ $zone->id == $filter['zone_id'] ? 'selected' : '' }}>
                                            {{ $zone->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Minimum Order Value:</strong></label>
                                <input type="number" class="form-control" name="min_order_value" value="{{ $filter['min_order_value'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Services:</strong></label>
                                <select name="service_id" class="form-control select2" id="service_id">
                                    <option value="">Select Service</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}" {{ $service->id == $filter['service_id'] ? 'selected' : '' }}>
                                            {{ $service->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Categories:</strong></label>
                                <select name="category_id" class="form-control select2" id="category_id">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ $category->id == $filter['category_id'] ? 'selected' : '' }}>
                                            {{ $category->title }}
                                        </option>
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
