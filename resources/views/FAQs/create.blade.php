@extends('layouts.app')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Add New FAQ</h2>

                <div class="bg-white p-4 shadow-sm">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('FAQs.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label class="font-weight-bold">
                                <span class="text-danger">*</span> Question
                            </label>
                            <input type="text"
                                   name="question"
                                   value="{{ old('question') }}"
                                   class="form-control form-control-lg"
                                   placeholder="Enter the question">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">
                                <span class="text-danger">*</span> Answer
                            </label>
                            <textarea name="answer"
                                      class="form-control"
                                      rows="6"
                                      placeholder="Enter the answer">{{ old('answer') }}</textarea>
                        </div>

                        <div class="form-row align-items-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Category</label>
                                    <select name="category_id" class="form-control">
                                        <option value=""></option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id', $category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3 mt-md-0">
                                <div class="form-group">
                                    <label class="font-weight-bold">Service</label>
                                    <select name="service_id" class="form-control">
                                        <option value=""></option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}"
                                                {{ old('service_id', $service_id) == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold d-block">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Enable</option>
                                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Disable</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3 mt-md-0">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold d-block">Featured FAQ</label>

                                    <!-- Hidden field ensures a value is sent when checkbox is unchecked -->
                                    <input type="hidden" name="feature" value="0">

                                    <div class="custom-control custom-switch">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="feature"
                                               name="feature"
                                               value="1"
                                               {{ old('feature') == '1' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="feature">Enable featured FAQ</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4 mb-0 text-right">
                            <button type="submit" class="btn btn-primary btn-lg px-4">Submit</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
