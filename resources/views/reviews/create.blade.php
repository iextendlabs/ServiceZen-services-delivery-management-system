@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 margin-tb">
        <h2>Add New Review</h2>
    </div>
</div>
@if ($errors->any())
<div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Service:</strong>
                <select name="service_id" class="form-control">
                    @foreach($services as $service)
                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Review:</strong>
                <textarea class="form-control" style="height:150px" name="content" placeholder="Review">{{old('content')}}</textarea>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><label for="rating">Rating</label><br>
                @for($i = 1; $i <= 5; $i++) 
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}>
                    <label class="form-check-label" for="rating{{ $i }}">{{ $i }}</label>
                </div>
                @endfor
            </div>
        </div>

    <div class="col-md-12 text-center">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
    </div>
</form>
@endsection