@extends('layouts.app')
@section('content')
<div class="container-fluid px-1">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <h2 class="mb-0">Create New User</h2>
            </div>
        </div>
    </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger mt-3">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <input type="hidden" name="url" value="{{ url()->previous() }}">

        <div class="card mt-3">
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-12 col-md-6">
                        <label class="font-weight-bold">Name</label>
                        <input type="text" name="name" placeholder="Name" class="form-control" value="{{ old('name') }}">
                    </div>

                    <div class="form-group col-12 col-md-6">
                        <label class="font-weight-bold">Email</label>
                        <input type="email" name="email" placeholder="Email" class="form-control" value="{{ old('email') }}">
                    </div>

                    <div class="form-group col-12 col-md-6">
                        <label class="font-weight-bold">Password</label>
                        <input type="password" name="password" placeholder="Password" class="form-control">
                    </div>

                    <div class="form-group col-12 col-md-6">
                        <label class="font-weight-bold">Confirm Password</label>
                        <input type="password" name="confirm-password" placeholder="Confirm Password" class="form-control">
                    </div>

                    <div class="form-group col-12">
                        <label class="font-weight-bold">Role</label>
                        <select name="roles[]" class="form-control" multiple>
                            @foreach($roles as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 text-center mt-2">
                        <button type="submit" class="btn btn-primary px-4">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
