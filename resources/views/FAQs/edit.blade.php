@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Update FAQs</h2>
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
    <form action="{{ route('FAQs.update',$FAQ->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="url" value="{{ url()->previous() }}">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Question:</strong>
                    <input type="text" name="question" value="{{ old( 'question' ,$FAQ->question ) }}" class="form-control" placeholder="Question">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Answer:</strong>
                    <textarea class="form-control" style="height:150px" name="answer" placeholder="Answer">{{ old('answer', $FAQ->answer) }}</textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Category:</strong>
                    <select name="category_id" class="form-control">
                        <option></option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                            {{ old('category_id', $FAQ->category_id ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->title }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Service:</strong>
                    <select name="service_id" class="form-control">
                        <option></option>
                        @foreach($services as $service)
                        <option value="{{$service->id}}" {{ old('service_id', $FAQ->service_id) == $service->id ? 'selected' : '' }}>{{$service->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                    <div class="form-group">
                        <strong>Status:</strong>
                        <select name="status" class="form-control">
                            <option value="1"  {{old('status', $FAQ->status) == '1' ? 'selected' : ''}}>Enable</option>
                            <option value="0" {{old('status', $FAQ->status) == '0' ? 'selected' : ''}}>Disable</option>
                        </select>
                    </div>
                </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </form>
</div>
@endsection