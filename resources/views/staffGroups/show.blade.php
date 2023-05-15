@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="pull-left">
                <h2> Show Staff Group</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $staffGroup->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Description:</strong>
                {{ $staffGroup->description }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Staff Id:</strong>
                @foreach(unserialize($staffGroup->staff_ids) as $staff)
                    {{ $staff }}, 
                @endforeach
            </div>
        </div>
    </div>
@endsection