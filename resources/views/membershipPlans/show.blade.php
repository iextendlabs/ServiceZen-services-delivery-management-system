@extends('layouts.app')
<link href="{{ asset('css/checkout.css') }}?v={{config('app.version')}}" rel="stylesheet">
@section('content')
    <div class="container">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
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
        <div class="row justify-content-center mx-1">
            <div class="card">
                <div class="card-header bg-white mt-1 justify-content-between d-flex align-items-center">
                <div class="col-md-12">
                    <div class="float-start ">
                        <h2> Show Membership Plan</h2>
                    </div>
                </div>
            </div>
                <div class="card-body bg-white mt-3 p-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Title:</strong>
                            {{ $membership_plan->plan_name }}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Description:</strong>
                            {!! $membership_plan->description !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Membership Fee:</strong>
                            {{ $membership_plan->membership_fee }}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Expire after days:</strong>
                            {{ $membership_plan->expire }}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Type:</strong>
                            {{ $membership_plan->type }}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Status:</strong>
                            {{ $membership_plan->status == 1 ? "Enable" : "Disable" }}
                        </div>
                    </div>
                </div>    
            </div>
        </div>
    </div>
@endsection
