@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 margin-tb">
        <div class="float-start">
            <h2>Show Review</h2>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <strong>User:</strong>
            {{ $review->user->name }}
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <strong>Service:</strong>
            {{ $review->service->name }}
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <strong>Review:</strong>
            {{ $review->content }}
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <strong for="rating">Rating:</strong><br>
            @for($i = 1; $i <= 5; $i++) @if($i <=$review->rating)
                <span class="text-warning">&#9733;</span>
                @else
                <span class="text-muted">&#9734;</span>
                @endif
                @endfor
        </div>
    </div>
</div>
@endsection