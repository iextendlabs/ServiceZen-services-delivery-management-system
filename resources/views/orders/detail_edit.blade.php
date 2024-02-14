@extends('layouts.app')
<link href="{{ asset('css/checkout.css') }}?v={{config('app.version')}}" rel="stylesheet">
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 py-5 text-center">
            <h2>Edit Order Address</h2>
        </div>
    </div>
    <div class="container">
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <span>{{ $message }}</span>
            <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div>
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
            <form action="{{ route('orders.update',$order->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="url" value="{{ url()->previous() }}">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>Transport Charges:</strong>
                            <div class="input-group mb-3">
                                <input type="text" name="transport_charges" id="transport_charges" value="{{ $order->order_total->transport_charges }}" class="form-control" placeholder="Transport Charges">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h3 class="text-center">Address</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Building Name:</strong>
                                <input type="text" name="buildingName" id="buildingName" value="{{ $order->buildingName }}" class="form-control" placeholder="Building Name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Flat / Villa:</strong>
                                <input type="text" name="flatVilla" id="flatVilla" value="{{ $order->flatVilla }}" class="form-control" placeholder="Flat / Villa">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Street:</strong>
                                <input type="text" name="street" id="street" value="{{ $order->street }}" class="form-control" placeholder="Street">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>District:</strong>
                                <input type="text" name="district" id="district" value="{{ $order->district }}" class="form-control" placeholder="District">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Area:</strong>
                                <input type="text" name="area" id="area" value="{{ $order->area }}" class="form-control" placeholder="Area">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Landmark:</strong>
                                <input type="text" name="landmark" id="landmark" value="{{ $order->landmark }}" class="form-control" placeholder="Landmark">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>City:</strong>
                                <input type="text" name="city" id="city" value="{{ $order->city }}" class="form-control" placeholder="City">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h3 class="text-center">Personal Information</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Customer Name:</strong>
                                <input type="text" customer_name="customer_name" id="customer_name" value="{{ $order->customer_name }}" class="form-control" placeholder="Customer Name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Customer Email:</strong>
                                <input type="text" name="customer_email" id="customer_email" value="{{ $order->customer_email }}" class="form-control" placeholder="Customer Email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Phone Number:</strong>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ config('app.country_code') }}</span>
                                    </div>
                                    <input type="text" name="number" id="number" value="{{ str_replace('+971', '', $order->number) }}" class="form-control" placeholder="Phone Number">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Whatsapp Number:</strong>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ config('app.country_code') }}</span>
                                    </div>
                                    <input type="text" name="whatsapp" id="whatsapp" value="{{ str_replace('+971', '', $order->whatsapp) }}" class="form-control" placeholder="Whatsapp Number">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 text-right no-print">
                        @can('order-edit')
                        <button type="submit" class="btn btn-primary">Update</button>
                        @endcan
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="{{ asset('js/checkout.js') }}?v={{config('app.version')}}"></script>
@endsection