@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2> Show Service Category</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Title:</strong>
                {{ $service_category->title }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Description:</strong>
                {{ $service_category->description }}
            </div>
        </div>
        @if($service_category->parentCategory)
        <div class="col-md-12">
            <div class="form-group">
                <strong>Parent Category:</strong>
                {{ $service_category->parentCategory->title }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection