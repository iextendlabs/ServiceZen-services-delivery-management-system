@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <h2>Add New FAQs</h2>
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
        <form action="{{ route('FAQs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Question:</strong>
                        <input type="text" name="question" value="{{ old('question') }}" class="form-control"
                            placeholder="Question">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Answer:</strong>
                        <textarea class="form-control" style="height:150px" name="answer" placeholder="Answer">{{ old('answer') }}</textarea>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Feature FAQs:</strong>
                        <div class="form-check form-switch">
                            <!-- Hidden field ensures a value is sent when checkbox is unchecked -->
                            <input type="hidden" name="feature" value="0">

                            <input class="form-check-input" type="checkbox" name="feature" id="feature" value="1"
                                {{ old('feature') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="feature">Enable featured FAQs</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Category:</strong>
                        <select name="category_id" class="form-control">
                            <option></option>
                            @foreach ($categories as $category)
                                <option {{ old('category_id', $category_id) == $category->id ? 'selected' : '' }}
                                    value="{{ $category->id }}">{{ $category->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Service:</strong>
                        <select name="service_id" class="form-control">
                            <option></option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}"
                                    {{ old('service_id', $service_id) == $service->id ? 'selected' : '' }}>
                                    {{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Status:</strong>
                        <select name="status" class="form-control">
                            <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Enable</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Disable</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
@endsection
