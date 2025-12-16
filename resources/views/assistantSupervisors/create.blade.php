@extends('layouts.app')
@section('content')
@section('page_title')
    <h3> Add Assistant Supervisor </h3>
@endsection
<div class="container-fluid px-1">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="m-0 h5"><strong>Add New Assistant Supervisor</strong></h2>
                </div>
                <div class="card-body">
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

                    <form action="{{ route('assistantSupervisors.store') }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold"><span class="text-danger">*</span> Name:</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Name">
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold"><span class="text-danger">*</span> Email:</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="abc@gmail.com">
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold"><span class="text-danger">*</span> Password:</label>
                                <input type="password" name="password" class="form-control" placeholder="Password">
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold"><span class="text-danger">*</span> Confirm Password:</label>
                                <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password">
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold">Supervisor:</label>
                                <select name="supervisor_id" class="form-control">
                                    <option></option>
                                    @foreach($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>{{ $supervisor->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 text-center mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection