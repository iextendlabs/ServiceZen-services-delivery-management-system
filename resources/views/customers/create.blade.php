@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Add New Customer</h2>
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
    <form action="{{ route('customers.store') }}" method="POST">
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
            <hr>
            <h4><strong> Affiliate Session</strong></h4>
            <div class="col-md-12">
                <div class="form-group"><strong>Affiliate:</strong>
                    <select name="affiliate_id" class="form-control">
                        <option></option>
                        @foreach ($affiliates as $affiliate)
                            <option value="{{ $affiliate->id }}" @if(old('affiliate_id') == $affiliate->id) selected @endif>{{ $affiliate->name }}@if($affiliate->affiliate->code)({{ $affiliate->affiliate->code }}) @endif</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group"><strong>Affiliate Commission type:</strong>
                    <select name="type" class="form-control">
                        <option></option>
                        <option @if(old('type') == "F") selected @endif value="F">Fix</option>
                        <option @if(old('type') == "P") selected @endif value="P">Persentage</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group"><strong>Affiliate Commission:</strong>
                    <input type="number" name="commission" class="form-control" placeholder="Affiliate Commission" value="{{ old('commission') }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group"><strong>Expiry Date:</strong>
                    <input type="date" name="expiry_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('expiry_date') }}">
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
</div>
@endsection