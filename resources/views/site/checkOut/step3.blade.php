@extends('site.layout.app')
<style>
    .detail-item .detail-item-label {
        display: block;
        width: 40%;
        color: #80878d;
        font-size: 20px;
    }

    .detail-item .detail-item-value {
        width: 55%;
        text-align: right;
    }

    ._sb {
        justify-content: space-between;
        display: flex;
    }
</style>

<base href="/public">
@section('content')
<div class="row">
    <div class="col-md-12 py-5 text-center">
        <h2>Summary</h2>
    </div>
</div>
<div class="text-center" style="margin-bottom: 20px;">
    @if(Session::has('error'))
    <span class="alert alert-danger" role="alert">
        <strong>{{ Session::get('error') }}</strong>
    </span>
    @endif
    @if(Session::has('success'))
    <span class="alert alert-success" role="alert">
        <strong>{{ Session::get('success') }}</strong>
    </span>
    @endif
</div>
@php
$sub_total = 0;
$total_amount = 0;
$staff_charges = 0;
$transport_charges = 0;
@endphp
<div class="album bg-light">
    <div class="container">
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
        <div class="row">
            <div class="col-md-7 mt-3">
                <h5>Services</h5>
                <table class="table" style="border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>image</th>
                            <th>name</th>
                            <th>duration</th>
                            <th>price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td><img src="service-images/{{ $service->image }}" height="60px" width="60px" style="border: 1px solid #ddd; border-radius: 4px;"></td>
                            <td>{{ $service->name }}</td>
                            <td>{{ $service->duration }}</td>
                            @if(isset($service->discount))
                            <td>${{ $service->discount }}</td>
                            @else
                            <td>${{ $service->price }}</td>
                            @endif
                        </tr>
                        @if(isset($service->discount))
                        @php
                        $sub_total += $service->discount;
                        @endphp
                        @else
                        @php
                        $sub_total += $service->price;
                        @endphp
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-md-5 mt-3">
                <h5>Booking Details</h5>
                <div class="detail-item _sb pb-3 pt-3">
                    <div class="detail-item-label pt-5">
                        <div>Address</div>
                    </div>
                    <div class="detail-item-value">
                        <div> <strong>Building Name:</strong> {{ $address['buildingName'] }}</div>
                        <div> <strong>FlatVilla:</strong> {{ $address['flatVilla'] }}</div>
                        <div> <strong>Street:</strong> {{ $address['street'] }}</div>
                        <div> <strong>Area:</strong> {{ $address['area'] }}</div>
                        <div> <strong>City:</strong> {{ $address['city'] }}</div>
                        <div> <strong>Number:</strong> {{ $address['number'] }}</div>
                    </div>
                </div>
                <hr>
                <div class="detail-item _sb pb-3 pt-3">
                    <div class="detail-item-label pt-3">
                        <div>Time Slots And Staff</div>
                    </div>
                    <div class="detail-item-value">
                        <div> <strong>Time Slot:</strong> {{ date('h:i A', strtotime($time_slot->time_start)) }} -- {{ date('h:i A', strtotime($time_slot->time_end)) }}</div>
                        <!-- <img src="staff-images/{{ $staff->staff->image }}" height="60px" width="60px" style="border: 1px solid #ddd; border-radius: 4px;"> -->
                        <div> <strong>Staff:</strong> {{ $staff->name }}</div>
                        <div> <strong>Date:</strong> {{ $staff_and_time['date'] }}</div>
                    </div>
                </div>
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 mt-3 mt-3 offset-sm-9 ">
                <h5>Payment Summary</h5>
                <table class="table">
                    <tr>
                        <td class="text-right"><strong> Service Total:</strong></td>
                        <td>${{$sub_total}}</td>
                        @php
                        $total_amount = $sub_total +$total_amount;
                        @endphp
                    </tr>
                    @if($staff->staff->charges)
                    <tr>
                        <td class="text-right"><strong>Staff Charges:</strong></td>
                        <td>${{$staff->staff->charges}}</td>
                        @php
                        $staff_charges = $staff->staff->charges;
                        $total_amount = $staff_charges +$total_amount;
                        @endphp
                    </tr>
                    @endif
                    @if($time_slot->staffGroup->staffZone->transport_charges)
                    <tr>
                        <td class="text-right"><strong>Transport Charges:</strong></td>
                        <td>${{$time_slot->staffGroup->staffZone->transport_charges}}</td>
                        @php
                        $transport_charges = $time_slot->staffGroup->staffZone->transport_charges;
                        $total_amount = $transport_charges +$total_amount;
                        @endphp
                    </tr>
                    @endif
                    <tr>
                        <td class="text-right"><strong>Total:</strong></td>
                        <td>${{$total_amount}}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 offset-sm-9">
                <form action="{{ route('order.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="total_amount" value="{{ $total_amount }}">
                    <input type="hidden" name="sub_total" value="{{ $sub_total }}">
                    <input type="hidden" name="staff_charges" value="{{ $staff_charges }}">
                    <input type="hidden" name="transport_charges" value="{{ $transport_charges }}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Payment Method:</strong>
                                <select name="payment_method" class="form-control">
                                    <option></option>
                                    <option value="Cash-On-Delivery">Cash On Delivery</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary">Confirm Order</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection