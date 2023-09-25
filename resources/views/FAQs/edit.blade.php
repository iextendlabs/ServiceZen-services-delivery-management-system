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
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Question:</strong>
                    <input type="text" name="question" value="{{$FAQ->question}}" class="form-control" placeholder="Question">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Answer:</strong>
                    <textarea class="form-control" style="height:150px" name="answer" placeholder="Answer">{{$FAQ->answer}}</textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Category:</strong>
                    <select name="category_id" class="form-control">
                        <option></option>
                        @foreach($categories as $category)
                        @if($category->id == $FAQ->category_id)
                        <option value="{{$category->id}}" selected>{{$category->title}}</option>
                        @else
                        <option value="{{$category->id}}">{{$category->title}}</option>
                        @endif
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
                        @if($service->id == $FAQ->service_id)
                        <option value="{{$service->id}}" selected>{{$service->name}}</option>
                        @else
                        <option value="{{$service->id}}">{{$service->name}}</option>
                        @endif
                        @endforeach
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