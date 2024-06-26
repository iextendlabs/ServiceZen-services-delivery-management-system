@extends('site.layout.app')
@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Add New Complaint</h2>
                </div>
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
        <form action="{{ route('siteComplaints.store') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order_id }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Title:</strong>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}"
                            placeholder="Title">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Description:</strong>
                        <textarea name="description" cols="30" rows="10" style="height:150px" class="form-control">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
@endsection
