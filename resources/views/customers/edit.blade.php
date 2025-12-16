@extends('layouts.app')
@section('content')
<div class="container-fluid px-1">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="m-0 h5"><Strong> Edit Customer</strong></h2>
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

                    <form action="{{ route('customers.update',$customer->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="url" value="{{ url()->previous() }}">

                        <div class="form-row">
                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold"> <span class="text-danger">*</span> Name:</label>
                                <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="form-control" placeholder="Name">
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold"> <span class="text-danger">*</span> Email:</label>
                                <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="form-control" placeholder="abc@gmail.com">
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold">Password:</label>
                                <input type="password" name="password" class="form-control" placeholder="Password">
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold">Confirm Password:</label>
                                <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password">
                            </div>

                            <div class="form-group col-12 col-md-4 mb-3">
                                <label class="font-weight-bold"> <span class="text-danger">*</span> Status:</label>
                                <select name="status" class="form-control">
                                    <option value="1" {{ old('status', $customer->status) == 1 ? 'selected' : '' }} > Enable</option>
                                    <option value="0" {{ old('status', $customer->status) == 0 ? 'selected' : '' }} > Disable</option>
                                </select>
                            </div>

                            <div class="col-12 pt-2">
                                <hr>
                                <h4 class="mb-3"><strong>Affiliate Session</strong></h4>
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold">Affiliate:</label>
                                <select name="affiliate_id" class="form-control">
                                    <option></option>
                                    @foreach ($affiliates as $affiliate)
                                    @if($affiliate->affiliate->status == 1)
                                        <option value="{{ $affiliate->id }}" {{ (old('affiliate_id', $customer->userAffiliate->affiliate_id ?? '') == $affiliate->id) ? 'selected' : '' }}>{{ $affiliate->name }}@if($affiliate->affiliate->code)({{ $affiliate->affiliate->code }}) @endif</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12 col-md-3 mb-3">
                                <label class="font-weight-bold">Affiliate Commission type:</label>
                                <select name="type" class="form-control">
                                    <option value=""></option>
                                    <option value="F" {{ old('type', $customer->userAffiliate->type ?? null) == "F" ? 'selected' : '' }}>Fix</option>
                                    <option value="P" {{ old('type', $customer->userAffiliate->type ?? null) == "P" ? 'selected' : '' }}>Percentage</option>
                                </select>
                            </div>

                            <div class="form-group col-12 col-md-3 mb-3">
                                <label class="font-weight-bold">Affiliate Commission:</label>
                                <input type="number" name="commission" class="form-control" placeholder="Affiliate Commission" value="{{ old('commission', $customer->userAffiliate->commission ?? '') }}">
                            </div>

                            <div class="form-group col-12 col-md-4 mb-3">
                                <label class="font-weight-bold">Expiry Date:</label>
                                <input type="date" name="expiry_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('expiry_date', $customer->userAffiliate->expiry_date ?? '') }}">
                            </div>

                            <div class="col-12 text-center mt-3">
                                <button type="submit" class="btn btn-md btn-primary shadow-sm float-end font-weight-bold">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection