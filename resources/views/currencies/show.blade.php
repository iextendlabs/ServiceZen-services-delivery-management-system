@extends('layouts.app')
<link href="{{ asset('css/checkout.css') }}?v={{config('app.version')}}" rel="stylesheet">
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2> Show Currency</h2>
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
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Currency:</strong>
                    {{ $currency->name }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Symbol:</strong>
                    {{ $currency->symbol }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Rate:</strong>
                    {{ $currency->rate }}
                </div>
            </div>
            
        </div>
    </div>
@endsection
