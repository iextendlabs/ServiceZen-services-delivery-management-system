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
        @if(isset($customer->customerProfiles))
        <div class="col-md-12">
            <div class="form-group">
                <strong>Number:</strong>
                {{optional($customer->customerProfiles->first())->number }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Whatsapp:</strong>
                {{optional($customer->customerProfiles->first())->whatsapp }}
            </div>
        </div>
    </div>
    <div class="row">
        <hr><h2> Address</h2>
        <div class="col-md-12">
            <table class="table table-striped table-bordered album bg-light">
                <tr>
                    <th>Building Name</th>
                    <th>Area</th>
                    <th>Landmark</th>
                    <th>Flat Villa</th>
                    <th>Street</th>
                    <th>City</th>
                    <th>District</th>
                </tr>
                @if(count($customer->customerProfiles) > 0)
                @foreach ($customer->customerProfiles as $customerProfile)
                <tr>
                    <td>{{ $customerProfile->buildingName ?? "" }}</td>
                    <td>{{ $customerProfile->area ?? "" }}</td>
                    <td>{{ $customerProfile->landmark ?? "" }}</td>
                    <td>{{ $customerProfile->flatVilla ?? "" }}</td>
                    <td>{{ $customerProfile->city ?? "" }}</td>
                    <td>{{ $customerProfile->flatVilla ?? "" }}</td>
                    <td>{{ $customerProfile->district ?? "" }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="7" class="text-center">There is no Customer Address</td>
                </tr>
                @endif
            </table>
        </div>
        @endif
    </div>
    @if(isset($customer->userAffiliate->affiliateUser))
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

    <div class="row">
        @if(isset($customer->coupons))
        <h3>Customer Coupon</h3>
        <div class="col-md-12">
            @if(count($customer->coupons) != 0)
            <input type="text" id="coupon-search" placeholder="Search coupon by name and code..." class="form-control">
            <div class="scroll-div">
                <table class="table table-striped table-bordered album bg-light coupons-table">
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
            </div>
            @else
            <div class="text-center">
                <p>There are no Coupon Assigned</p>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#coupon-search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".coupons-table tr").hide();

            $(".coupons-table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();
                var code = $row.find("td:first").next().next().text().toLowerCase();

                if (name.indexOf(value) != -1) {
                    $(this).show();
                } else if (code.indexOf(value) != -1) {
                    $(this).show();
                }

            });
        });
    });
</script>
@endsection