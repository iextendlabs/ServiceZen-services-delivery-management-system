@extends('layouts.app')
@section('content')
<div class="container">
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <span>{{ $message }}</span>
        <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2> Show Customer</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $customer->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Email:</strong>
                {{ $customer->email }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Roles:</strong>
                @if(!empty($customer->getRoleNames()))
                @foreach($customer->getRoleNames() as $v)
                <span class="badge rounded-pill bg-dark">{{ $v }}</span>
                @endforeach
                @endif
            </div>
        </div>
        @if(isset($customer->customerProfile))
        <div class="col-md-12">
            <div class="form-group">
                <strong>Number:</strong>
                {{ $customer->customerProfile->number }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Whatsapp:</strong>
                {{ $customer->customerProfile->whatsapp }}
            </div>
        </div>
        <hr><h2> Address</h2>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Building Name:</strong>
                {{ $customer->customerProfile->buildingName }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Area:</strong>
                {{ $customer->customerProfile->area }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Landmark:</strong>
                {{ $customer->customerProfile->landmark }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Flat Villa:</strong>
                {{ $customer->customerProfile->flatVilla }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Street:</strong>
                {{ $customer->customerProfile->street }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>City:</strong>
                {{ $customer->customerProfile->city }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>District:</strong>
                {{ $customer->customerProfile->district }}
            </div>
        </div>
        @endif
    </div>
    
    @if(isset($customer->userAffiliate->affiliateUser))
    <hr>
    <div class="row">
        <h3>Affiliate</h3>
        <table class="table table-striped table-bordered album bg-light">
            <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Commission</th>
            </tr>
            <tr>
                <td>{{ $customer->userAffiliate->affiliateUser->name ?? "" }}</td>
                <td>{{ $customer->userAffiliate->affiliate->code ?? "" }}</td>
                <td>
                    <small>
                    @if(isset($customer->userAffiliate->commission))<s>@endif
                    {{$customer->userAffiliate->affiliate->commission }}% <br>
                    @if(isset($customer->userAffiliate->commission))</s>@endif
                    @if(isset($customer->userAffiliate->commission))

                    @if($customer->userAffiliate->type == "F")
                    <b>AED {{ $customer->userAffiliate->commission }}</b>
                    @elseif($customer->userAffiliate->type == "P")
                    <b>{{ $customer->userAffiliate->commission }}%</b>
                    @endif
                    @endif
                    </small>
                </td>
            </tr>
        </table>
    </div>
    @endif

    <hr>
    <div class="row">
        @if(isset($customer->coupons))
        <h3>Customer Coupon</h3>
        @if(count($customer->coupons) != 0)
        <table class="table table-striped table-bordered album bg-light">
            <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Discount</th>
                <th>Action</th>
            </tr>
            @foreach ($customer->coupons as $coupons)
            <tr>
                <td>{{ $coupons->name }}</td>
                <td>{{ $coupons->code }}</td>
                <td>@if($coupons->type == "Percentage") {{ $coupons->discount }} % @else AED {{ $coupons->discount }} @endif</td>
                <td>
                    <form action="{{ route('customerCoupon.destroy', $coupons->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
        @else
        <div class="text-center">
            <p>There are no Coupon Assigned</p>
        </div>
        @endif
        @endif
    </div>
</div>
@endsection