@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2> Show Coupon</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $coupon->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Code:</strong>
                {{ $coupon->code }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Type:</strong>
                {{ $coupon->type }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Discount:</strong>
                {{ $coupon->discount }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Date Start:</strong>
                {{ $coupon->date_start }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Date End:</strong>
                {{ $coupon->date_end }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Status:</strong>
                @if($coupon->status == 1)Enable @else Disable @endif
            </div>
        </div>
    </div>
@endsection