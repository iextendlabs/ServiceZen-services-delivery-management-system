@extends('layouts.app')
@section('content')
<div class="container-fluid px-1">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <h2 class="mb-0">Add New Staff Holiday</h2>
            </div>
        </div>
    </div>
    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form action="{{ route('staffHolidays.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card mt-3">
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-12 col-md-3 mb-3">
                        <label class="font-weight-bold"><span class="text-danger">*</span> Date</label>
                        <input type="date" name="date" value="{{ old('date',date('Y-m-d')) }}" class="form-control" placeholder="Date">
                    </div>

                    <div class="form-group col-12 mb-3 scroll-div">
                        <label class="font-weight-bold"><span class="text-danger">*</span> Staffs</label>
                        <input type="text" name="search" id="search" class="form-control mb-2" placeholder="Search Staff By Name And Email">
                        <div class="table-responsive">
                            <table id="staffsTable" class="table table-striped table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Name</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($staffs as $staff)
                                        @if($staff->hasRole("Staff"))
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="ids[{{ ++$i }}]" value="{{ $staff->id }}" {{ in_array($staff->id, old('ids', [])) || $staff->id == $staff_id ? 'checked' : '' }}>
                                                </td>
                                                <td>{{ $staff->name }}</td>
                                                <td>{{ $staff->email }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12 text-center mt-2">
                        <button type="submit" class="btn btn-md btn-primary shadow-sm float-end font-weight-bold">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
        $("#search").on('keyup', function() {
            var value = $(this).val().toLowerCase();

            $("#staffsTable tbody tr").each(function() {
                var $row = $(this);
                var name = $row.find("td:nth-child(2)").text().toLowerCase();
                var email = $row.find("td:nth-child(3)").text().toLowerCase();

                if (name.indexOf(value) !== -1 || email.indexOf(value) !== -1) {
                    $row.show();
                } else {
                    $row.hide();
                }
            });
        });
    });
</script>
@endsection