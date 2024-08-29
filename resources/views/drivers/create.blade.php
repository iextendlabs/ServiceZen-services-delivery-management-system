@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Add New Driver</h2>
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
    <form action="{{ route('drivers.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Name:</strong>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Name">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Email:</strong>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="abc@gmail.com">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Password:</strong>
                    <input type="password" name="password" class="form-control" placeholder="Password">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Confirm Password:</strong>
                    <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Phone Number:</strong>
                    <input id="number_country_code" type="hidden" name="number_country_code" />
                    <input type="tel" id="number" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Whatsapp Number:</strong>
                    <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                    <input type="tel" id="whatsapp" name="whatsapp" class="form-control" value="{{ old('whatsapp') }}" >
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Commission:</strong>
                    <input type="number" name="commission" class="form-control" value="{{ old('commission') }}" placeholder="Commission In %">
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
                                    @if (old('affiliate_id') == $affiliate->id) selected @endif> {{ $affiliate->name }}
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