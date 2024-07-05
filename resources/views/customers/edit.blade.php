@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Edit Customer</h2>
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
    <form action="{{ route('customers.update',$customer->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="url" value="{{ url()->previous() }}">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Name:</strong>
                    <input type="text" name="name" value="{{ $customer->name }}" class="form-control" placeholder="Name">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Email:</strong>
                    <input type="email" name="email" value="{{ $customer->email }}" class="form-control" placeholder="abc@gmail.com">
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
                    <span style="color: red;">*</span><strong>Status:</strong>
                    <select name="status" class="form-control">
                        <option value="1" @if($customer->status == 1) selected @endif > Enable</option>
                        <option value="0" @if($customer->status == 0) selected @endif > Disable</option>
                    </select>
                </div>
            </div>
            <hr>
            <h4><strong> Affiliate Session</strong></h4>
            <div class="col-md-12">
                <div class="form-group"><strong>Affiliate:</strong>
                    <select name="affiliate_id" class="form-control">
                        <option></option>
                        @foreach ($affiliates as $affiliate)
                        @if($affiliate->affiliate->status == 1)
                            <option value="{{ $affiliate->id }}" @if($customer->userAffiliate && $customer->userAffiliate->affiliate_id == $affiliate->id) selected @endif>{{ $affiliate->name }}@if($affiliate->affiliate->code)({{ $affiliate->affiliate->code }}) @endif</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group"><strong>Affiliate Commission type:</strong>
                    <select name="type" class="form-control">
                        <option></option>
                        <option @if($customer->userAffiliate && $customer->userAffiliate->type == "F") selected @endif value="F">Fix</option>
                        <option @if($customer->userAffiliate && $customer->userAffiliate->type == "P") selected @endif value="P">Persentage</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group"><strong>Affiliate Commission:</strong>
                    <input type="number" name="commission" class="form-control" placeholder="Affiliate Commission" value={{ $customer->userAffiliate ? $customer->userAffiliate->commission : null }}>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group"><strong>Expiry Date:</strong>
                    <input type="date" name="expiry_date" class="form-control" min="{{ date('Y-m-d') }}" value={{ $customer->userAffiliate ? $customer->userAffiliate->expiry_date : null }}>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
</div>
@endsection