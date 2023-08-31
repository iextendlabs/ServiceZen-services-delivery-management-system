@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 margin-tb">
        <div class="float-start">
            <h2>Edit Service Staff</h2>
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
<form action="{{ route('serviceStaff.update',$serviceStaff->id) }}" method="POST" enctype="multipart/form-data">
    <input type="hidden" value="{{ $serviceStaff->staff->id }}" name="staff_id">
    @csrf
    @method('PUT')
    <ul class="nav nav-tabs" id="myTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="social-links-tab" data-toggle="tab" href="#social-links" role="tab" aria-controls="social-links" aria-selected="false">Social Links</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabsContent">
        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Name:</strong>
                        <input readonly type="text" name="name" value="{{ $serviceStaff->name }}" class="form-control" placeholder="Name">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Email:</strong>
                        <input type="email" name="email" value="{{ $serviceStaff->email }}" class="form-control" placeholder="abc@gmail.com">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Phone Number:</strong>
                        <input type="number" name="phone" value="{{ $serviceStaff->staff->phone }}" class="form-control" placeholder="Phone Number">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Status:</strong>
                        <select name="status" class="form-control">

                            <option value="1" @if($serviceStaff->staff->status == 1) selected @endif>Enable</option>
                            <option value="0" @if($serviceStaff->staff->status == 0) selected @endif>Disable</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong for="image">Upload Image</strong>
                        <input type="file" name="image" id="image" class="form-control-file ">
                        <br>
                        <img id="preview" src="/staff-images/{{$serviceStaff->staff->image}}" height="130px">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Password:</strong>
                        <input type="password" name="password" class="form-control" placeholder="Password">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Confirm Password:</strong>
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
                            @if($user->id == $serviceStaff->staff->supervisor_id)
                            <option value="{{ $user->id }}" selected>{{ $user->name }}</option>
                            @else
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endif
                            @endif
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Commission:</strong>
                        <input type="number" name="commission" value="{{ $serviceStaff->staff->commission }}" class="form-control" placeholder="Commission In %">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Additional Charges:</strong>
                        <input type="number" name="charges" value="{{ $serviceStaff->staff->charges }}" class="form-control" placeholder="Additional Charges">
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="social-links" role="tabpanel" aria-labelledby="social-links-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Instagram <i class="fa fa-instagram"></i>:</strong>
                        <input type="text" name="instagram" class="form-control" placeholder="Instagram" value="{{ $serviceStaff->staff->instagram }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Snapchat:</strong>
                        <input type="text" name="snapchat" class="form-control" placeholder="Snapchat" value="{{ $serviceStaff->staff->snapchat }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Facebook:</strong>
                        <input type="text" name="facebook" class="form-control" placeholder="Facebook" value="{{ $serviceStaff->staff->facebook }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Youtube:</strong>
                        <input type="text" name="youtube" class="form-control" placeholder="Youtube" value="{{ $serviceStaff->staff->youtube }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Tiktok:</strong>
                        <input type="text" name="tiktok" class="form-control" placeholder="Tiktok" value="{{ $serviceStaff->staff->tiktok }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-block btn-primary">Save</button>
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