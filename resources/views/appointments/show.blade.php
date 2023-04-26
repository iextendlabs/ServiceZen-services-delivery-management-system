@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2> Show appointment</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $appointment->service->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Staff:</strong>
                @if(isset($appointment->serviceStaff->name))
                    {{ $appointment->serviceStaff->name }}
                @else
                    N\A
                @endif
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Customer:</strong>
                {{ $appointment->customer->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Address:</strong>
                {{ $appointment->address }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Date:</strong>
                {{ $appointment->date }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Time:</strong>
                {{ $appointment->time }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Price:</strong>
                {{ $appointment->service->price }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Status:</strong>
                {{ $appointment->status }}
            </div>
        </div>
    </div>
@endsection