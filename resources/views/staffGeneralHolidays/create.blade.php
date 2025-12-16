@extends('layouts.app')
@section('content')
<div class="container-fluid px-3 px-md-4">
<div class="row">
    <div class="col-12 mt-3">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="mb-0">Add New Staff General Holiday</h2>
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
<form action="{{ route('staffGeneralHolidays.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="card mt-3">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-12 mb-3">
                    <label class="font-weight-bold">Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Enable</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Disable</option>
                    </select>
                </div>

                <div class="form-group col-12 mb-3">
                    <label class="font-weight-bold"><span class="text-danger">*</span> Days</label>
                    <input type="text" name="day-search" id="day-search" class="form-control mb-2" placeholder="Search day">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered days_table mb-0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Day</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($week_days as $day)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="days[{{ ++$i }}]" value="{{ $day }}" {{ in_array($day, old('days', [])) ? 'checked' : '' }}>
                                        </td>
                                        <td>{{ $day }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-group col-12 mb-3 scroll-div">
                    <label class="font-weight-bold"><span class="text-danger">*</span> Staffs</label>
                    <input type="text" name="staff-search" id="staff-search" class="form-control mb-2" placeholder="Search Staff By Name And Email">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered staff_table mb-0">
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
                                                <input type="checkbox" name="ids[{{ ++$i }}]" value="{{ $staff->id }}"  @if(in_array($staff->id, old('ids', [])) || $staff->id == $staff_id) checked @endif>
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
                    <button type="submit" class="btn btn-primary px-4">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
</div>
<script>
    $(document).ready(function() {
        $('#staff-search').on('keyup', function() {
            var value = $(this).val().toLowerCase();

            $('.staff_table tbody tr').each(function() {
                var $row = $(this);
                var name = $row.find('td:nth-child(2)').text().toLowerCase();
                var email = $row.find('td:nth-child(3)').text().toLowerCase();

                if (name.indexOf(value) !== -1 || email.indexOf(value) !== -1) {
                    $row.show();
                } else {
                    $row.hide();
                }
            });
        });

        $('#day-search').on('keyup', function() {
            var value = $(this).val().toLowerCase();

            $('.days_table tbody tr').each(function() {
                var $row = $(this);
                var day = $row.find('td:nth-child(2)').text().toLowerCase();

                if (day.indexOf(value) !== -1) {
                    $row.show();
                } else {
                    $row.hide();
                }
            });
        });
    });
</script>
@endsection