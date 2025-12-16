@extends('layouts.app')
<link href="{{ asset('css/checkout.css') }}?v={{config('app.version')}}" rel="stylesheet">
@section('content')
    <div class="container-fluid px-3 px-md-4">
        <div class="row">
            <div class="col-12 mt-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0">Show Currency</h2>
                </div>
            </div>
        </div>
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
        <div class="card mt-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-group">
                            <strong>Currency:</strong>
                            <div>{{ $currency->name }}</div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-group">
                            <strong>Symbol:</strong>
                            <div>{{ $currency->symbol }}</div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4 mb-3">
                        <div class="form-group">
                            <strong>Rate:</strong>
                            <div>{{ $currency->rate }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
