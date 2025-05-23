@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Add New Short Holiday</h2>
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
    <form action="{{ route('shortHolidays.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Date:</strong>
                    <input type="date" name="date" value="{{ old('date') }}" class="form-control" placeholder="Date">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Time Start:</strong>
                    <input type="time" name="time_start" value="{{ old('time_start') }}" class="form-control" placeholder="TIme Start">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Hours:</strong>
                    <input type="number" name="hours" value="{{ old('hours') }}" class="form-control" placeholder="Hours">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Status:</strong>
                        <select name="status" class="form-control">
                            <option value="1"  {{ old('status') == '1' ? 'selected' : '' }}>Enable</option>
                            <option value="0"  {{ old('status') == '0' ? 'selected' : '' }}>Disable</option>
                        </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Staff:</strong>
                    <select name="staff_id" class="form-control">
                        @foreach ($staffs as $staff)
                        @if($staff->hasRole("Staff"))
                        <option value="{{ $staff->id }}"  {{ old('staff_id') == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
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
@endsection