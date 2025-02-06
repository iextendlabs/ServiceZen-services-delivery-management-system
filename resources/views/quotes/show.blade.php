@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Show Quote</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>User Name:</strong>
                {{ $quote->user->name ?? "" }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Service:</strong>
                {{ $quote->service_name }}
            </div>
        </div>
        @if($quote->serviceOption)
        <div class="col-md-12">
            <div class="form-group">
                <strong>Option:</strong>
                {{ $quote->serviceOption->option_name }}(@currency($quote->serviceOption->option_price, true))
            </div>
        </div>
        @endif
        <div class="col-md-12">
            <div class="form-group">
                <strong>Detail:</strong>
                {!! $quote->detail !!}
            </div>
        </div>
    </div>
</div>
@endsection