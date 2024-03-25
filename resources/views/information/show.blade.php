@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Show Information</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $information->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Description:</strong>
                {!! $information->description !!}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Position:</strong>
                {{ $information->position }}
            </div>
        </div>
    </div>
</div>
@endsection