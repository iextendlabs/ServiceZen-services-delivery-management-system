@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Add New Time Slot</h2>
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
    <form action="{{ route('timeSlots.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Name:</strong>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Name">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong for="image">Type</strong>
                    <select name="type" class="form-control">
                        <option value="General" {{ old('type') == 'General' ? 'selected' : ''}}>General</option>
                        <option value="Specific" {{ old('type') == 'Specific' ? 'selected' : ''}}>Specific</option>
                        <option value="Partner" {{ old('type') == 'Partner' ? 'selected' : ''}}>Partner</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Time Start:</strong>
                    <input type="time" name="time_start" value="{{ old('time_start') }}" class="form-control" placeholder="Time Start">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Time End:</strong>
                    <input type="time" name="time_end" value="{{ old('time_end') }}" class="form-control" placeholder="Time End">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>No. of Seats:</strong>
                    <input type="number" name="seat" value="{{ old('seat',1) }}" class="form-control">
                </div>
            </div>
            <div class="col-md-12" id="date" style="display: none;">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Date:</strong>
                    <input type="date" name="date" value="{{ old('date') }}" class="form-control" placeholder="Date">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Status:</strong>
                    <select name="status" class="form-control">
                        <option value="1" {{ old('status') == '1' ? 'selected' : ''}}> Enable</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : ''}}>Disable</option>
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
    $('select[name="type"]').on('change', function() {
        var type = $('select[name="type"]').val();
        if (type == 'Specific') {
            $('#date').show();
        } else if (type == 'General' || type == 'Partner') {
            $('#date').hide();
        }
    });
</script>
@endsection