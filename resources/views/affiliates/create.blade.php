@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Add New Affiliate</h2>
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
        <form action="{{ route('affiliates.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Name:</strong>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                            placeholder="Name">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Email:</strong>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                            placeholder="abc@gmail.com">
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
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Phone Number:</strong>
                        <input id="number_country_code" type="hidden" name="number_country_code" />
                        <input type="tel" id="number" name="number" class="form-control"
                            value="{{ old('phone') }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Whatsapp Number:</strong>
                        <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                        <input type="tel" id="whatsapp" name="whatsapp" class="form-control"
                            value="{{ old('whatsapp') }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Code:</strong>
                        <input type="text" name="code" class="form-control" value="{{ old('code') }}"
                            placeholder="Code">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Commission:</strong>
                        <input type="number" name="commission" class="form-control" value="{{ old('commission') }}"
                            placeholder="Commission In %">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Expire after days:</strong>
                        <input type="number" name="expire" class="form-control" value="{{ old('expire') }}"
                            placeholder="Enter days like 20">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Fix Salary:</strong>
                        <input type="number" name="fix_salary" class="form-control" value="{{ old('fix_salary') }}"
                            placeholder="Fix Salary">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Parent Affiliate:</strong>
                        <select name="parent_affiliate_id" class="form-control">
                            <option value=""></option>
                            @foreach ($affiliates as $affiliate)
                                <option value="{{ $affiliate->id }}">{{ $affiliate->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                       <strong>Parent Affiliate Commission:</strong>
                        <input type="number" name="parent_affiliate_commission" class="form-control" value="{{ old('parent_affiliate_commission') }}"
                            placeholder="Parent Affiliate Commission In %">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Membership Plan:</strong>
                        <select name="membership_plan_id" class="form-control">
                            <option value=""></option>
                            @foreach ($membership_plans as $membership_plan)
                                <option value="{{ $membership_plan->id }}">{{ $membership_plan->plan_name }} (AED{{$membership_plan->membership_fee}})</option>
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
