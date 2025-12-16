@extends('layouts.app')
<link href="{{ asset('css/checkout.css') }}?v={{config('app.version', '1')}}" rel="stylesheet">
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0 font-weight-bold">Edit Order Address</h4>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div>{{ $message }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Whoops!</strong> There were some problems with your input.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <ul class="mt-2 mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('orders.detail_edit',$order->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="url" value="{{ url()->previous() }}">

                        <div class="mb-4">
                            <label for="transport_charges" class="form-label">Transport Charges</label>
                            <div class="input-group">
                                <input type="text" name="transport_charges" id="transport_charges" value="{{ old( 'transport_charges',$order->order_total->transport_charges) }}" class="form-control {{ $errors->has('transport_charges') ? 'is-invalid' : '' }}" placeholder="Transport Charges">
                            </div>
                            @error('transport_charges')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <h5 class="mb-3 text-center font-weight-bold">Address</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="buildingName" class="form-label">Building Name</label>
                                <input required type="text" name="buildingName" id="buildingName" value="{{ old('buildingName',$order->buildingName) }}" class="form-control {{ $errors->has('buildingName') ? 'is-invalid' : '' }}" placeholder="Building Name">
                                @error('buildingName') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="flatVilla" class="form-label">Flat / Villa</label>
                                <input required type="text" name="flatVilla" id="flatVilla" value="{{ old('flatVilla',$order->flatVilla) }}" class="form-control {{ $errors->has('flatVilla') ? 'is-invalid' : '' }}" placeholder="Flat / Villa">
                                @error('flatVilla') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="street" class="form-label">Street</label>
                                <input required type="text" name="street" id="street" value="{{ old('street',$order->street) }}" class="form-control {{ $errors->has('street') ? 'is-invalid' : '' }}" placeholder="Street">
                                @error('street') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="district" class="form-label">District</label>
                                <input required type="text" name="district" id="district" value="{{ old('district',$order->district) }}" class="form-control {{ $errors->has('district') ? 'is-invalid' : '' }}" placeholder="District">
                                @error('district') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="area" class="form-label">Area</label>
                                <input required type="text" name="area" id="area" value="{{ old('area',$order->area) }}" class="form-control {{ $errors->has('area') ? 'is-invalid' : '' }}" placeholder="Area">
                                @error('area') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="landmark" class="form-label">Landmark</label>
                                <input required type="text" name="landmark" id="landmark" value="{{ old('landmark',$order->landmark) }}" class="form-control {{ $errors->has('landmark') ? 'is-invalid' : '' }}" placeholder="Landmark">
                                @error('landmark') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="city" class="form-label">City</label>
                                <input required type="text" name="city" id="city" value="{{ old('city',$order->city) }}" class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}" placeholder="City">
                                @error('city') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="mt-4">

                        <h5 class="mb-3 text-center font-weight-bold">Personal Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label">Customer Name</label>
                                <input required type="text" name="customer_name" id="customer_name" value="{{ old('customer_name',$order->customer_name) }}" class="form-control {{ $errors->has('customer_name') ? 'is-invalid' : '' }}" placeholder="Customer Name">
                                @error('customer_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="customer_email" class="form-label">Customer Email</label>
                                <input required type="email" name="customer_email" id="customer_email" value="{{ old('customer_email',$order->customer_email) }}" class="form-control {{ $errors->has('customer_email') ? 'is-invalid' : '' }}" placeholder="Customer Email">
                                @error('customer_email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="number" class="form-label">Phone Number</label>
                                <input id="number_country_code" type="hidden" name="number_country_code" />
                                <input required type="tel" name="number" id="number" value="{{ old('number',$order->number) }}" class="form-control {{ $errors->has('number') ? 'is-invalid' : '' }}">
                                @error('number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="whatsapp" class="form-label">Whatsapp Number</label>
                                <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                                <input required type="tel" name="whatsapp" id="whatsapp" value="{{ old('whatsapp',$order->whatsapp) }}" class="form-control {{ $errors->has('whatsapp') ? 'is-invalid' : '' }}">
                                @error('whatsapp') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="card-footer bg-white d-flex justify-content-end mt-4">
                            @can('order-edit')
                            <button type="submit" class="btn btn-primary">Update</button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/checkout.js') }}?v={{config('app.version', '1')}}"></script>
@endsection