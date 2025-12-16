@extends('layouts.app')
@section('content')
<div class="container-fluid px-1">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="m-0 h5"><strong>Add New Customer</strong></h2>
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

                    <form action="{{ route('customers.store') }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold"><span class="text-danger">*</span> Name:</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Name">
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold"><span class="text-danger">*</span> Email:</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="abc@gmail.com">
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold"><span class="text-danger">*</span> Password:</label>
                                <input type="password" name="password" class="form-control" placeholder="Password">
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold"><span class="text-danger">*</span> Confirm Password:</label>
                                <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password">
                            </div>

                            <div class="form-group col-12 col-md-4 mb-3">
                                <label class="font-weight-bold"><span class="text-danger">*</span> Status:</label>
                                <select name="status" class="form-control">
                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}> Enable</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}> Disable</option>
                                </select>
                            </div>

                            <div class="col-12 pt-2">
                                <hr>
                                <h4 class="mb-3"><strong> Affiliate Session</strong></h4>
                            </div>

                            <div class="form-group col-12 col-md-6 mb-3">
                                <label class="font-weight-bold">Affiliate:</label>
                                <select name="affiliate_id" class="form-control">
                                    <option></option>
                                    @foreach ($affiliates as $affiliate)
                                    @if($affiliate->affiliate->status == 1)
                                        <option value="{{ $affiliate->id }}" {{ old('affiliate_id') == $affiliate->id ? 'selected' : '' }}>{{ $affiliate->name }}@if($affiliate->affiliate->code)({{ $affiliate->affiliate->code }}) @endif</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12 col-md-3 mb-3">
                                <label class="font-weight-bold">Affiliate Commission type:</label>
                                <select name="type" class="form-control">
                                    <option></option>
                                    <option value="F" {{ old('type') == "F" ? 'selected' : '' }}>Fix</option>
                                    <option value="P" {{ old('type') == "P" ? 'selected' : '' }}>Percentage</option>                        
                                </select>
                            </div>

                            <div class="form-group col-12 col-md-3 mb-3">
                                <label class="font-weight-bold">Affiliate Commission:</label>
                                <input type="number" name="commission" class="form-control" placeholder="Affiliate Commission" value="{{ old('commission') }}">
                            </div>

                            <div class="form-group col-12 col-md-4 mb-3">
                                <label class="font-weight-bold">Expiry Date:</label>
                                <input type="date" name="expiry_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('expiry_date') }}">
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