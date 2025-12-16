@extends('layouts.app')
@section('content')
<div class="container-fluid px-1">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <h2 class="mb-0">Add New Short Holiday</h2>
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
    <form action="{{ route('shortHolidays.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card mt-3">
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-12 col-md-4 mb-3">
                        <label class="font-weight-bold"><span class="text-danger">*</span> Date</label>
                        <input type="date" name="date" value="{{ old('date') }}" class="form-control" placeholder="Date">
                    </div>

                    <div class="form-group col-12 col-md-4 mb-3">
                        <label class="font-weight-bold"><span class="text-danger">*</span> Time Start</label>
                        <input type="time" name="time_start" value="{{ old('time_start') }}" class="form-control" placeholder="Time Start">
                    </div>

                    <div class="form-group col-12 col-md-4 mb-3">
                        <label class="font-weight-bold"><span class="text-danger">*</span> Hours</label>
                        <input type="number" name="hours" value="{{ old('hours') }}" class="form-control" placeholder="Hours">
                    </div>

                    <div class="form-group col-12 mb-3">
                        <label class="font-weight-bold">Status</label>
                        <select name="status" class="form-control">
                            <option value="1"  {{ old('status') == '1' ? 'selected' : '' }}>Enable</option>
                            <option value="0"  {{ old('status') == '0' ? 'selected' : '' }}>Disable</option>
                        </select>
                    </div>

                    <div class="form-group col-12 mb-3">
                        <label class="font-weight-bold"><span class="text-danger">*</span> Staff</label>
                        <select name="staff_id" class="form-control">
                            @foreach ($staffs as $staff)
                                @if($staff->hasRole("Staff"))
                                    <option value="{{ $staff->id }}"  {{ old('staff_id') == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 text-center mt-2">
                        <button type="submit" class="btn btn-md btn-primary shadow-sm float-end font-weight-bold">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>
@endsection