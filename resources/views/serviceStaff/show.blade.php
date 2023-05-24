@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2> Show Service Staff</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $serviceStaff->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Email:</strong>
                {{ $serviceStaff->email }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Phone Number:</strong>
                {{ $serviceStaff->staff->phone }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Commission:</strong>
                {{ $serviceStaff->staff->commission }}%
            </div>
        </div>
        @if(isset( $serviceStaff->staff->charges ))
        <div class="col-md-12">
            <div class="form-group">
                <strong>Additional Charges:</strong>
                ${{ $serviceStaff->staff->charges }}
            </div>
        </div>
        @endif
        <div class="col-md-12">
            <div class="form-group">
                <strong>Roles:</strong>
                @if(!empty($serviceStaff->getRoleNames()))
                    @foreach($serviceStaff->getRoleNames() as $v)
                        <span class="badge rounded-pill bg-dark">{{ $v }}</span>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection