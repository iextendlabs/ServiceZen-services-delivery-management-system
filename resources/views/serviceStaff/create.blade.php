@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 margin-tb">
        <h2>Add New Service Staff</h2>
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
<form action="{{ route('serviceStaff.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Name:</strong>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Name">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Email:</strong>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="abc@gmail.com">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Phone Number:</strong>
                <input type="number" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Phone Number">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Status:</strong>
                <select name="status" class="form-control">
                    <option value="1">Enable</option>
                    <option value="0">Disable</option>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong for="image">Upload Image</strong>
                <input type="file" name="image" id="image" class="form-control-file">
                <br>
                <img id="preview" src="" height="130px">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Password:</strong>
                <input type="password" name="password" class="form-control" placeholder="Password">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Confirm Password:</strong>
                <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Supervisor:</strong>
                <select name="supervisor_id" class="form-control">
                    <option value=""></option>
                    @if(count($users))
                    @foreach($users as $user)
                    @if($user->getRoleNames() == '["Supervisor"]')
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endif
                    @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Commission:</strong>
                <input type="number" name="commission" class="form-control" value="{{ old('commission') }}" placeholder="Commission In %">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Additional Charges:</strong>
                <input type="number" name="charges" class="form-control" value="{{ old('charges') }}" placeholder="Additional Charges">
            </div>
        </div>
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>

<script>
    document.getElementById('image').addEventListener('change', function(e) {
        var preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(e.target.files[0]);
    });
</script>
@endsection