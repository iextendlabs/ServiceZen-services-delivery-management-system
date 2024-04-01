@extends('site.layout.app')
@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2> Your Complaint</h2>
                </div>
            </div>
        </div>
        <hr>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
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
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Title:</strong>
                    {{ $complaint->title }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Description:</strong>
                    {{ $complaint->description }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Status:</strong>
                    {{ $complaint->status }}
                </div>
            </div>
            @if ($complaint->order_id)
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Order:</strong>
                        <a href="{{ route('order.show', $complaint->order_id) }}">{{ $complaint->order_id }}</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
