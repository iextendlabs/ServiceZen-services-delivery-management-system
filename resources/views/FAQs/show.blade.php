@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Show FAQs</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Question:</strong>
                {{ $FAQ->question }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Answer:</strong>
                {{ $FAQ->answer }}
            </div>
        </div>
        @if($FAQ->category)
        <div class="col-md-12">
            <div class="form-group">
                <strong>Category:</strong>
                {{ $FAQ->category->title }}
            </div>
        </div>
        @endif

        @if($FAQ->service)
        <div class="col-md-12">
            <div class="form-group">
                <strong>Service:</strong>
                {{ $FAQ->service->name }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection