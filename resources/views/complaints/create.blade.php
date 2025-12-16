@extends('layouts.app')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 px-4 mb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="mb-0">Add New Complaint</h3>
                </div>
            </div>
        </div>

        <div class="row mx-0 px-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('complaints.store') }}" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="form-group col-md-8">
                                    <label class="font-weight-bold">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title') }}" placeholder="Title">
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option></option>
                                        <option value="Open" {{ old('status') === 'Open' ? 'selected' : '' }}>Open</option>
                                        <option value="Close" {{ old('status') === 'Close' ? 'selected' : '' }}>Close</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-12">
                                    <label class="font-weight-bold">Description <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control" rows="6">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">User <span class="text-danger">*</span></label>
                                    <select name="user_id" class="form-control">
                                        <option></option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} | {{ $user->email }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6 text-right align-self-end">
                                    <button type="submit" class="btn btn-primary btn-lg">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
