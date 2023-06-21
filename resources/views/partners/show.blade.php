@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2> Show Partner</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $partner->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Description:</strong>
                {{ $partner->description }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Image:</strong>
                <img id="preview" src="/partner-images/{{$partner->image}}" height="130px">
            </div>
        </div>
    </div>
@endsection