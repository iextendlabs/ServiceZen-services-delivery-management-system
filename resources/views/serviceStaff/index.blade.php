@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="float-left">
                <h2>Service Staff</h2>
            </div>
            <div class="float-right">
                @can('service-staff-create')
                <a class="btn btn-success float-end" href="{{ route('serviceStaff.create') }}"><i class="fa fa-plus"></i></a>
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
    <h3>Services Staff  ({{ $total_staff }})</h3>
    <div class="row">
        <div class="col-md-9">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Sr#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Sub Title / Designation</th>
                    <th width="280px">Action</th>
                </tr>
                @if(count($serviceStaff))
                @foreach ($serviceStaff as $staff)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $staff->name }}</td>
                    <td>{{ $staff->email }}</td>
                    <td>@if($staff->staff->status == 1) Enabled @else Disabled @endif</td>
                    <td>{{ $staff->staff->sub_title }}</td>
                    <td>
                        <form id="deleteForm{{ $staff->id }}" action="{{ route('serviceStaff.destroy',$staff->id) }}" method="POST">
                            <a class="btn btn-warning" href="{{ route('serviceStaff.show',$staff->id) }}"><i class="fa fa-eye"></i></a>
                            @can('service-staff-edit')
                            <a class="btn btn-primary" href="{{ route('serviceStaff.edit',$staff->id) }}"><i class="fa fa-edit"></i></a>
                            @endcan
                            @csrf
                            @method('DELETE')
                            @can('service-staff-delete')
                            <button type="button" onclick="confirmDelete('{{ $staff->id }}')" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                            @endcan
                            <a class="btn btn-primary" href="{{ route('staffHolidays.create',['staff' => $staff->id]) }}" title="Add Holiday"><i class="fas fa-calendar"></i></a>
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
                            <input type="text" name="name" value="{{$filter_name}}" class="form-control" placeholder="Name">
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
    $(document).ready(function () {
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

        $(window).resize(function () {
            checkTableResponsive();
        });
    });
</script>
@endsection