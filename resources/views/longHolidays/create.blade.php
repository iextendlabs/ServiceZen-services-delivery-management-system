@extends('layouts.app')
@section('content')
<div class="container">
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
<div class="row">
    <div class="col-md-12 margin-tb">
        <h2>Add New Long Holiday</h2>
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
<form action="{{ route('longHolidays.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Date Start:</strong>
                <input type="date" name="date_start" value="{{ date('Y-m-d') }}" class="form-control" placeholder="Date Start" min="{{ date('Y-m-d') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Date End:</strong>
                <input type="date" name="date_end" value="" class="form-control" placeholder="Date End" min="{{ date('Y-m-d') }}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Staff:</strong>
                <select name="staff_id" class="form-control">
                    @foreach ($staffs as $staff)
                    @if($staff->getRoleNames() == '["Staff"]')
                    @if($staff->id == $staff_id)
                    <option value="{{ $staff->id }}" selected>{{ $staff->name }}</option>
                    @else
                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endif
                    @endif
                    @endforeach
                </select>
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
        $("#search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $("table tr").hide();

            $("table tr").each(function() {

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