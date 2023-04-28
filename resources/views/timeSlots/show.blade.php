@extends('layouts.app')
@section('content')
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
                <strong>Time Start:</strong>
                {{ $time_slot->time_start }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Time End:</strong>
                {{ $time_slot->time_end }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Active:</strong>
                {{ $time_slot->active }}
            </div>
        </div>
    </div>
@endsection