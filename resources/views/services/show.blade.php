@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="pull-left">
                <h2> Show Service</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $service->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Description:</strong>
                {!! $service->description !!}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Price:</strong>
                {{ $service->price }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Package Services:</strong>
                @foreach($service->package as $package)
                    {{ $package->service->name }}, 
                @endforeach
            </div>
        </div>
    </div>
@endsection