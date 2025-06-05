@extends('layouts.app')
@section('content')
<div class="container">
<div class="row">
    <div class="col-md-12 margin-tb">
        <h2>Add New Staff General Holiday</h2>
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
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Status:</strong>
                <select name="status" class="form-control">
                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Enable</option>
                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Disable</option>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Days:</strong>
                <input type="text" name="day-search" id="day-search" class="form-control" placeholder="Search day">
                <table class="table table-striped table-bordered days_table">
                    <tr>
                        <th></th>
                        <th>Day</th>
                    </tr>
                    @foreach ($week_days as $day)

                    <tr>
                        <td>
                            <input type="checkbox" name="days[{{ ++$i }}]" value="{{ $day }}" {{ in_array($day, old('days', [])) ? 'checked' : '' }}>
                        </td>
                        <td>{{ $day }}</td>
                    </tr>

                    @endforeach
                </table>
            </div>
        </div>
        <div class="col-md-12 scroll-div">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Staffs:</strong>
                <input type="text" name="staff-search" id="staff-search" class="form-control" placeholder="Search Staff By Name And Email">
                <table class="table table-striped table-bordered staff_table">
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
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
                </table>
            </div>
        </div>
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>
</div>
<script>
    $(document).ready(function() {
        $("#staff-search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".staff_table tr").hide();

            $(".staff_table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();

                var email = $row.find("td:last").text().toLowerCase();

                if (name.indexOf(value) != -1) {
                    $(this).show();
                } else if (email.indexOf(value) != -1) {
                    $(this).show();
                }
            });
        });
        $("#day-search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".days_table tr").hide();

            $(".days_table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();

                var email = $row.find("td:last").text().toLowerCase();

                if (name.indexOf(value) != -1) {
                    $(this).show();
                } else if (email.indexOf(value) != -1) {
                    $(this).show();
                }
            });
        });
    });
</script>
@endsection