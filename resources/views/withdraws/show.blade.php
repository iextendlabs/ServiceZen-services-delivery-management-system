@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Show Withdraw</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>User Name:</strong>
                {{ $withdraw->user_name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Amount:</strong>
                {!! $withdraw->amount !!}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Status:</strong>
                {{ $withdraw->status }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Payment Method:</strong>
                {{ $withdraw->payment_method }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Acount Detail:</strong>
                {{ $withdraw->account_detail }}
            </div>
        </div>
    </div>
</div>
@endsection