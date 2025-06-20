@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2> Show Time Slot</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $time_slot->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Type:</strong>
                {{ $time_slot->type }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Time Start:</strong>
                {{ date('h:i A', strtotime($time_slot->time_start)) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Time End:</strong>
                {{ date('h:i A', strtotime($time_slot->time_end)) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>No. of Seats:</strong>
                {{ $time_slot->seat }}
            </div>
        </div>
        @if($time_slot->type == 'Specific')
        <div class="col-md-12">
            <div class="form-group">
                <strong>Date:</strong>
                {{ $time_slot->date }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection