@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Edit Driver</h2>
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
    <form action="{{ route('drivers.update',$driver->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="url" value="{{ url()->previous() }}">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Name:</strong>
                    <input type="text" name="name" value="{{ old('name' , $driver->name) }}" class="form-control" placeholder="Name">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Email:</strong>
                    <input type="email" name="email" value="{{ old('email', $driver->email) }}" class="form-control" placeholder="abc@gmail.com">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group"><strong>Password:</strong>
                    <input type="password" name="password" class="form-control" placeholder="Password">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group"><strong>Confirm Password:</strong>
                    <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Phone Number:</strong>
                    <input id="number_country_code" type="hidden" name="number_country_code" />
                    <input type="tel" id="number" name="phone" value="{{ isset($driver->driver->phone) ? ($driver->driver->phone) : null }}" class="form-control">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Whatsapp Number:</strong>
                    <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                    <input type="tel" id="whatsapp" name="whatsapp" value="{{ isset($driver->driver->whatsapp) ? $driver->driver->whatsapp : null }}" class="form-control">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Commission:</strong>
                    <input type="number" name="commission" value="{{ old( 'commission',$driver->driver->commission ?? "" )}}" class="form-control" placeholder="Commission In %">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Affiliate:</strong>
                    <select name="affiliate_id" class="form-control">
                        <option value=""></option>
                        @foreach ($affiliates as $affiliate)
                            @if($affiliate->affiliate->status == 1)
                            <option value="{{ $affiliate->id }}"
                                {{ old('affiliate_id') == $affiliate->id || ($driver->driver && $driver->driver->affiliate_id == $affiliate->id) ? 'selected' : '' }}>
                                {{ $affiliate->name }}
                            </option>                            
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
</div>
@endsection