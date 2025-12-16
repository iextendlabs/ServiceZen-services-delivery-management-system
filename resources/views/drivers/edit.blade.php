@extends('layouts.app')
@section('content')
<div class="container-fluid px-1">
    <div class="row">
        <div class="col-12 ">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="m-0 h5"><strong>Edit Driver</strong></h2>
                </div>
                <div class="card-body">
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

                    <form action="{{ route('drivers.update',$driver->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="url" value="{{ url()->previous() }}">

                        <div class="form-row">
                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold"><span class="text-danger">*</span> Name:</label>
                                <input type="text" name="name" value="{{ old('name' , $driver->name) }}" class="form-control" placeholder="Name">
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold"><span class="text-danger">*</span> Email:</label>
                                <input type="email" name="email" value="{{ old('email', $driver->email) }}" class="form-control" placeholder="abc@gmail.com">
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold">Password:</label>
                                <input type="password" name="password" class="form-control" placeholder="Password">
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold">Confirm Password:</label>
                                <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password">
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold"><span class="text-danger">*</span> Phone Number:</label>
                                <input id="number_country_code" type="hidden" name="number_country_code" />
                                <input type="tel" id="number" name="phone" value="{{ old('phone', $driver->driver->phone ?? '') }}" class="form-control">
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold"><span class="text-danger">*</span> Whatsapp Number:</label>
                                <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                                <input type="tel" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $driver->driver->whatsapp ?? '') }}" class="form-control">
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold">Commission:</label>
                                <input type="number" name="commission" value="{{ old( 'commission',$driver->driver->commission ?? "")}}" class="form-control" placeholder="Commission In %">
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold">Affiliate:</label>
                                <select name="affiliate_id" class="form-control">
                                    <option value=""></option>
                                    @foreach ($affiliates as $affiliate)
                                        @if($affiliate->affiliate->status == 1)
                                        <option value="{{ $affiliate->id }}" {{ old('affiliate_id', $driver->driver->affiliate_id ?? '') == $affiliate->id ? 'selected' : '' }}>
                                            {{ $affiliate->name }}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 text-center mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection